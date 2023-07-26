<?php
session_start();

// Patikriname, ar vartotojas yra prisijungęs
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = readDataFromJson();

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $personalId = $_POST['personal_id'];

    // Patikriname, ar asmens kodas jau egzistuoja
    foreach ($data['accounts'] as $account) {
        if ($account['personal_id'] === $personalId) {
            header('Location: create_account.php?error=1');
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

    // Peradresuojame vartotoją į sąskaitų sąrašo puslapį
    header('Location: index.php');
    exit();
}
?>
