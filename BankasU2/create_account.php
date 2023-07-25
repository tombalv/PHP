<?php
include 'functions.php';

// Pranešimo kintamasis
$message = '';

// Patikriname, ar yra POST užklausa ir ar saskaita jau buvo sukurta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['surname'], $_POST['personal_code'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $personalCode = $_POST['personal_code'];

    // Tikriname, ar vardas ir pavardė ilgesni nei 3 simboliai
    if (strlen($name) < 3 || strlen($surname) < 3) {
        $message = 'Vardas ir pavardė turi būti ilgesni nei 3 simboliai.';
    } else {
        // Tęsiame sąskaitos sukūrimo logiką
        $data = readDataFromJson(); // Gauti duomenis iš JSON failo

        // Patikriname, ar sąskaitos numeris jau neegzistuoja
        $exists = true;
        while ($exists) {
            $newAccountNumber = generateAccountNumber();
            $exists = false;
            foreach ($data['accounts'] as $account) {
                if ($account['account_number'] === $newAccountNumber) {
                    $exists = true;
                    break;
                }
            }
        }

        $newAccount = [
            'name' => $name,
            'surname' => $surname,
            'account_number' => $newAccountNumber,
            'personal_code' => $personalCode,
            'balance' => 0
        ];
        $data['accounts'][] = $newAccount; // Pridėti naują sąskaitą prie sąrašo

        saveDataToJson($data); // Išsaugoti duomenis į JSON failą

        $message = "Nauja sąskaita sukurta. Sąskaitos numeris: " . $newAccount['account_number'];
        header('Location: index.php');
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nauja sąskaita</title>
    
</head>
<body>
    <h1>Nauja sąskaita</h1>
    <form method="post" action="create_account.php">
    <label for="name">Vardas:</label>
    <input type="text" name="name" id="name" required>
    <label for="surname">Pavardė:</label>
    <input type="text" name="surname" id="surname" required>
    <label for="personal_code">Asmens kodas:</label>
    <input type="text" name="personal_code" id="personal_code" required>
    <button type="submit">Sukurti</button>
</form>
</body>
</html>