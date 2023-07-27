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

    // Find the account based on the account number
    foreach ($data['accounts'] as &$account) {
        if ($account['account_number'] === $accountNumber) {
            // Check if the account has sufficient balance for the withdrawal
            if ($account['balance'] >= $amount) {
                // Add the transaction details to the account
                $transaction = [
                    'date' => date('Y-m-d H:i:s'),
                    'type' => 'withdraw',
                    'amount' => $amount
                ];
                $account['transactions'][] = $transaction;

                // Update the account balance
                $account['balance'] -= $amount;

                // Save the updated data to the JSON file
                saveDataToJson($data);

                // Add success message to the session
                $_SESSION['withdraw_funds_success'] = 'Lėšos sėkmingai išimtos iš jūsų sąskaitos.';

                // Redirect the user back to the account list page after successful funds withdrawal
                header('Location: withdraw_funds.php');
                exit();
            } else {
                // If the account balance is insufficient, show an error message
                $_SESSION['withdraw_funds_error'] = 'Nepavyko išimti lėšų iš sąskaitos. Nepakankamas likutis sąskaitoje.';
                header('Location: withdraw_funds.php');
                exit();
            }
        }
    }

    // If the account number is not found, show an error message
    $_SESSION['withdraw_funds_error'] = 'Nepavyko išimti lėšų iš sąskaitos. Nepavyko rasti sąskaitos.';
    header('Location: withdraw_funds.php');
    exit();
}


?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./css/withdraw_funds.css">
<title>Banko aplikacija - Išimti lėšas</title>
</head>
    <body>
        <h1>Išimti lėšas</h1>
        <nav>
        <ul>
        <li><a href="index.php">Pagrindinis</a></li>
        <li><a href="create_account.php">Sukurti sąskaita</a></li>
        <li><a href="add_funds.php">Pridėti lėšų</a></li>
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
        <form method="post" action="withdraw_funds.php">
            <input type="hidden" name="account_number" value="<?php echo $currentAccount['account_number']; ?>">
            <label for="amount">Įveskite sumą:</label>
            <input type="number" step="0.01" name="amount" id="amount" required>
            <input type="hidden" name="transaction_type" value="withdraw" checked>
            <button type="submit">Patvirtinti</button>
        </form>
        <?php else : ?>
        <p>Pasirinkite sąskaitą, iš kurios norite išimti lėšas:</p>
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
        <?php  if (isset($_SESSION['withdraw_funds_success']) && $_SESSION['withdraw_funds_success']) {
                echo '<div class="success-message">';
                echo 'Lėšos nuskaišiuotos.';
                echo '</div>';
                unset($_SESSION['withdraw_funds_success']);
            }
            if (isset($_SESSION['withdraw_funds_error']) && $_SESSION['withdraw_funds_error']) {
                echo '<div class="error-message">';
                echo 'Sąskaitos likutis nepakankamas.';
                echo '</div>';
                unset($_SESSION['withdraw_funds_error']);
            }?>
        <?php endif; ?>
    </body>
</html>
