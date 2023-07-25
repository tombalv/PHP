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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Banko aplikacija - Išimti lėšas</title>
</head>
<body>
<h1>Išimti lėšas</h1>
    <?php
    // Patikriname, ar vartotojas yra prisijungęs
    session_start();
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

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
    ?>

    <?php if ($currentAccount) : ?>
        
        <p>Savininkas: <?php echo $currentAccount['name'] . ' ' . $currentAccount['surname']; ?></p>
        <p>Sąskaitos likutis: <?php echo $currentAccount['balance']; ?></p>
        <form method="post" action="withdraw_funds.php">
            <input type="hidden" name="account_number" value="<?php echo $currentAccount['account_number']; ?>">
            <label for="amount">Įveskite sumą:</label>
            <input type="number" step="0.01" name="amount" id="amount" required>
            <input type="radio" name="transaction_type" value="withdraw" checked>
            <label for="withdraw">Išimti lėšas</label>
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
    <?php endif; ?>
</body>
</html>
