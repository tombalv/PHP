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
    $accountNumber = $_POST['account_number'];
    $amount = floatval($_POST['amount']);
    $transactionType = $_POST['transaction_type'];

    // Raskime sąskaitą pagal sąskaitos numerį
    foreach ($data['accounts'] as &$account) {
        if ($account['account_number'] === $accountNumber) {
            // Įrašome transakciją į sąskaitos duomenis
            $transaction = [
                'date' => date('Y-m-d H:i:s'),
                'type' => $transactionType,
                'amount' => $amount
            ];
            $account['transactions'][] = $transaction;

            // Atnaujiname sąskaitos likutį
            if ($transactionType === 'add') {
                $account['balance'] += $amount;
            } elseif ($transactionType === 'withdraw') {
                if ($account['balance'] >= $amount) {
                    $account['balance'] -= $amount;
                } else {
                    header('Location: withdraw_funds.php?error=1');
                    exit();
                }
            }

            break;
        }
    }
    unset($account); // Išvalome nuorodą, kad išsaugotų pakeitimus
    saveDataToJson($data);

    // Po sėkmingo įrašymo, galime peradresuoti vartotoją į sąskaitų sąrašo puslapį
    header('Location: index.php');
    exit();
}
