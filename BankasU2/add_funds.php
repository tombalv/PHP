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
    $accountNumber = $_POST['account_number'];
    $amount = floatval($_POST['amount']);
    $transactionType = $_POST['transaction_type'];

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
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./css/add_funds.css">
    <title>Banko aplikacija - Pridėti lėšų</title>
</head>
<body>
    <h1>Pridėti lėšų</h1>
        <nav>
            <ul>
                <li><a href="index.php">Pagrindinis</a></li>
                <li><a href="create_account.php">Sukurti sąskaita</a></li>
                <li><a href="withdraw_funds.php">Išimti lėšas</a></li>
            </ul>
        </nav>
    <?php

    // Nuskaitome duomenis iš JSON failo
    $data = readDataFromJson();

    // Raskime sąskaitą pagal sąskaitos numerį, jei perduotas iš ankstesnio puslapio
    $selectedAccountNumber = $_GET['account_number'] ?? '';
    $currentAccount = null;
    foreach ($data['accounts'] as $account) {
        if ($account['account_number'] === $selectedAccountNumber) {
            $currentAccount = $account;
            break;
        }
    }
        // Rūšiavimo funkcija pagal pavardę
    function sortBySurname($a, $b)
    {
        return strcmp($a['surname'], $b['surname']);
    }

    // Surūšiuoti sąskaitas pagal pavardę
    usort($data['accounts'], 'sortBySurname');
    ?>

    <?php if ($currentAccount) : ?>
        <p>Savininkas: <?php echo $currentAccount['name'] . ' ' . $currentAccount['surname']; ?></p>
        <p>Sąskaitos likutis: <?php echo $currentAccount['balance']; ?></p>
        <form method="post" action="add_funds.php">
            <input type="hidden" name="account_number" value="<?php echo $currentAccount['account_number']; ?>">
            <label for="amount">Įveskite sumą:</label>
            <input type="number" step="0.01" name="amount" id="amount" required>
            <input type="hidden" name="transaction_type" value="add" checked>
            <button type="submit">Patvirtinti</button>
        </form>
    <?php else : ?>
        <p>Pasirinkite sąskaitą, kuriai norite pridėti lėšų:</p>
        <form method="get">
            <select name="account_number">
                <?php foreach ($data['accounts'] as $account) : ?>
                    <option value="<?php echo $account['account_number']; ?>">
                        <?php echo $account['name'] . ' ' . $account['surname'] . ' (' . $account['balance'] . ' EUR)'; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Pasirinkti</button>
        </form>
        <?php  if (isset($_SESSION['add_funds_success']) && $_SESSION['add_funds_success']) {
                echo '<div class="success-message">';
                echo 'Sąskaita papildyta.';
                echo '</div>';
                unset($_SESSION['add_funds_success']);
            }?>
    <?php endif; ?>
</body>
</html>
