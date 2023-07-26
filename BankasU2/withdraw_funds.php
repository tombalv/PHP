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
    <style>
        .popup {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
        z-index: 9999;
    }

    .popup-content {
        font-size: 16px;
        color: #333;
    }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 10px;
        }
        h1 {
        margin-bottom: 1rem;
        color: #333;
        }

        nav {
            background-color: #444;
            padding: 0.5rem;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav li {
            display: inline-block;
            margin-right: 1rem;
        }

        nav a {
            color: #007bff;
            text-decoration: none;
            padding: 5px;
            border: 1px solid #007bff;
            border-radius: 5px;
        }

        nav a:hover {
            background-color: #007bff;
            color: #fff;
        }

        nav li a {
        text-decoration: none;
        color: #fff;
        }

        nav li a:hover {
        text-decoration: underline;
        }

        button {
            width: 65px;
            height: 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
     <script>
        function showPopup(message) {
            var popup = document.createElement("div");
            popup.className = "popup";
            popup.innerHTML = '<div class="popup-content">' + message + '</div>';
            document.body.appendChild(popup);
            setTimeout(function () {
                document.body.removeChild(popup);
            }, 3000); // Remove the popup after 3 seconds (adjust as needed)
        }
    </script>
</head>
<body>
<h1>Išimti lėšas</h1>

        <nav>
            <ul>
                <li><a href="index.php">Pagrindinis</a></li>
                <li><a href="create_account.php">Sukurti sąskaita</a></li>
                <li><a href="add_funds.php?account_number=<?php echo $account['account_number']; ?>">Pridėti lėšų</a></li>
            </ul>
        </nav>
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
    <?php endif; ?>
</body>
</html>
