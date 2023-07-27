<?php
session_start();
include 'functions.php';

// Patikriname, ar vartotojas yra prisijungęs
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = readDataFromJson();
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $personalId = $_POST['personal_id'];

    // Validate personal identification number (asmens kodas)
    if (strlen($personalId) !== 11 || !ctype_digit($personalId)) {
        header('Location: create_account.php?error=2'); // Error code 2 for invalid personal identification number
        exit();
    }

    // Patikriname, ar asmens kodas jau egzistuoja
    foreach ($data['accounts'] as $account) {
        if ($account['personal_id'] === $personalId) {
            header('Location: create_account.php?error=1'); // Error code 1 for existing personal identification number
            exit();
        }
    }

    // Generuojame unikalų sąskaitos numerį
    $accountNumber = generateAccountNumber();

    // Sukuriame naują sąskaitos masyvą
    $newAccount = [
        'account_number' => $accountNumber,
        'name' => $name,
        'surname' => $surname,
        'personal_id' => $personalId,
        'balance' => 0,
        'transactions' => []
    ];

    // Pridedame naują sąskaitą prie duomenų masyvo
    $data['accounts'][] = $newAccount;

    // Išsaugome atnaujintus duomenis į JSON failą
    saveDataToJson($data);

    // Set a session variable to indicate successful account creation
    $_SESSION['account_created'] = true;

    // Peradresuojame vartotoją į sąskaitų sąrašo puslapį
    header('Location: create_account.php');
    exit();
}
?>


<!DOCTYPE html>
<html>
    <head>
       <link rel="stylesheet" href="./css/create_account.css">
    <title>Nauja sąskaita</title>
    </head>
        <body>
   
       
        <h1>Nauja sąskaita</h1>
        <nav>
            <ul>
                <li><a href="index.php">Pagrindis</a></li>
                <li><a href="add_funds.php">Pridėti lėšų</a></li>
                <li><a href="withdraw_funds.php">Išimti lėšas</a></li>
            </ul>
        </nav>
        <?php
            // Check if the account has been created and show the message
            if (isset($_SESSION['account_created']) && $_SESSION['account_created']) {
                echo '<div class="success-message">';
                echo 'Sąskaita sėkmingai sukurta.';
                echo '</div>';

                // Clear the PHP session variable to avoid showing the message again on subsequent page loads
                unset($_SESSION['account_created']);
            }

            // Check for error and display error message if error=1
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['error']) && $_GET['error'] === '1') {
                echo '<div class="error-message">';
                echo 'Klaida: Sąskaita su tokiu asmens kodu jau egzistuoja.';
                echo '</div>';
            }

            // Check for error and display error message if error=2
            if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['error']) && $_GET['error'] === '2') {
                echo '<div class="error-message">';
                echo 'Klaida: Asmens kodą sudaro 11 skaitmenų ilgio.';
                echo '</div>';
            }
            ?>
        <form method="post" action="create_account.php">
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