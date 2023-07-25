<?php
session_start();

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
    <title>Banko aplikacija - Pridėti lėšų</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f0f0f0;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 5px;
}

.title {
    text-align: center;
}

.login-form {
    margin-top: 20px;
}

.login-form label,
.login-form input,
.login-form button {
    display: block;
    width: 100%;
    margin-bottom: 10px;
}

.login-form button {
    margin-top: 20px;
}

/* Add styles for the navigation menu */
nav {
    background-color: #006a88;
    padding: 10px;
}

nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
}

nav li {
    margin-right: 20px;
}

nav li:last-child {
    margin-right: 0;
}

nav li a {
    color: #fff;
    text-decoration: none;
    font-size: 16px;
}

nav li a:hover {
    background-color: #004e6b;
}

/* Add styles for the table */
table {
    width: 100%;
    border-collapse: collapse;
}

table th,
table td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

table th {
    text-align: left;
    background-color: #f2f2f2;
}

table tr:hover {
    background-color: #f5f5f5;
}
    </style>
</head>
<body>
    <h1>Pridėti lėšų</h1>

    <?php
    // Patikriname, ar vartotojas yra prisijungęs
    session_start();
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }

    // Jei vartotojas yra prisijungęs, galime gauti jo vartotojo vardą ir rolę
    $username = $_SESSION['username'];
    $role = $_SESSION['role'];
    include 'functions.php';

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
        <form method="post" action="add_funds_backend.php">
            <input type="hidden" name="account_number" value="<?php echo $currentAccount['account_number']; ?>">
            <label for="amount">Įveskite sumą:</label>
            <input type="number" step="0.01" name="amount" id="amount" required>
            <input type="radio" name="transaction_type" value="add" checked>
            <label for="add">Pridėti lėšų</label>
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
    <?php endif; ?>
</body>
</html>
