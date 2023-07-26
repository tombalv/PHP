<?php
include 'functions.php';

// Pranešimo kintamasis
$message = '';

// Patikriname, ar yra POST užklausa ir ar saskaita jau buvo sukurta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['surname'], $_POST['personal_id'])) {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $personalCode = $_POST['personal_id'];

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
            'personal_id' => $personalCode,
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

    <nav>
    <ul>
    <li><a href="index.php">Pagrindis</a></li>
    <li><a href="add_funds.php?account_number=<?php echo $account['account_number']; ?>">Pridėti lėšų</a></li>

        <li><a href="withdraw_funds.php">Išimti lėšas</a></li>
    </ul>
</nav>
    <?php if (isset($_GET['error']) && $_GET['error'] == 1) : ?>
        <p style="color: red;">Toks asmens kodas jau egzistuoja. Įveskite unikalų asmens kodą.</p>
    <?php endif; ?>
    
    <form method="post" action="create_account_backend.php">
    <label for="name">Vardas:</label>
    <input type="text" name="name" id="name" required>
    <label for="surname">Pavardė:</label>
    <input type="text" name="surname" id="surname" required>
    <label for="personal_id">Asmens kodas:</label>
    <input type="text" name="personal_id" id="personal_id" required>
    <button type="submit">Sukurti</button>
</form>
</body>
</html>