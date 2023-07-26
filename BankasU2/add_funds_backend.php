<?php
session_start();
include 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = readDataFromJson();
    $accountNumber = $_POST['account_number'];
    $amount = floatval($_POST['amount']);

    // Find the account based on the account number
    foreach ($data['accounts'] as &$account) {
        if ($account['account_number'] === $accountNumber) {
            // Add the transaction details to the account
            $transaction = [
                'date' => date('Y-m-d H:i:s'),
                'type' => 'add',
                'amount' => $amount
            ];
            $account['transactions'][] = $transaction;

            // Update the account balance
            $account['balance'] += $amount;

            // Save the updated data to the JSON file
            saveDataToJson($data);

            // Add success message to the session
            $_SESSION['add_funds_success'] = 'Lėšos sėkmingai pridėtos į jūsų sąskaitą.';

            // Redirect the user back to the account list page after successful funds addition
            header('Location: add_funds.php');
            exit();
        }
    }

    // If the account number is not found, show an error message
    $_SESSION['add_funds_error'] = 'Nepavyko pridėti lėšų į sąskaitą. Nepavyko rasti sąskaitos.';
    header('Location: add_funds.php');
    exit();
}
?>