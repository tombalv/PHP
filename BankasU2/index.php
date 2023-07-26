<?php
session_start();

// Patikriname, ar vartotojas yra prisijungęs
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Jei vartotojas yra prisijungęs, galime gauti jo vartotojo vardą ir rolę
$username = $_SESSION['username'];
$role = $_SESSION['role'];
include 'functions.php';

$data = readDataFromJson(); // Nuskaitome duomenis iš JSON failo
// Sort accounts alphabetically by surname
usort($data['accounts'], function ($a, $b) {
    return strcmp($a['surname'], $b['surname']);
});

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Patikriname, ar formoje buvo pasirinkta operacija "add" arba "withdraw"
    if (isset($_POST['operation']) && $_POST['operation'] === 'add') {
        $data = readDataFromJson();
        $accountNumber = $_POST['account_number'];
        $amount = floatval($_POST['amount']);

        // Raskime sąskaitą pagal sąskaitos numerį
        foreach ($data['accounts'] as &$account) {
            if ($account['account_number'] === $accountNumber) {
                // Įrašome transakciją į sąskaitos duomenis
                $transaction = [
                    'date' => date('Y-m-d H:i:s'),
                    'type' => 'add',
                    'amount' => $amount
                ];
                $account['transactions'][] = $transaction;

                // Atnaujiname sąskaitos likutį
                $account['balance'] += $amount;

                // Save the updated data to JSON file
                saveDataToJson($data);

                // Add success message to the session
                $_SESSION['add_funds_success'] = 'Lėšos sėkmingai pridėtos į jūsų sąskaitą.';

                // Po sėkmingo įrašymo, galime peradresuoti vartotoją į sąskaitų sąrašo puslapį
                header('Location: index.php');
                exit();
            }
        }
        unset($account); // Išvalome nuorodą, kad išsaugotų pakeitimus
    } elseif (isset($_POST['delete'])) {
        // Handle the code for deleting the account here
        $accountNumberToDelete = $_POST['account_number'];
        $data = readDataFromJson();
        
        // Ištriname sąskaitą iš sąrašo pagal sąskaitos numerį
        foreach ($data['accounts'] as $key => $account) {
            if ($account['account_number'] === $accountNumberToDelete) {
                unset($data['accounts'][$key]);
                // Add success message to the session
                $_SESSION['delete_success'] = true;
                break;
            }
        }
        // Išsaugome duomenis į JSON failą be ištrintos sąskaitos
        saveDataToJson($data);

        // Po sėkmingo ištrynimo, galime peradresuoti vartotoją į sąskaitų sąrašo puslapį
        header('Location: index.php');
        exit();
    }
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Banko aplikacija</title>
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

        nav {
            background-color: #f0f0f0;
            padding: 10px;
        }

        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        nav li {
            display: inline-block;
            margin-right: 10px;
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

        main {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 8px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        form {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"], input[type="number"], input[type="password"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin-top: 20px;
        }
        header {
        background-color: #333;
        padding: 1rem;
        color: #fff;
        }

        header h1 {
        margin: 0;
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

        nav li a {
        text-decoration: none;
        color: #fff;
        }

        nav li a:hover {
        text-decoration: underline;
        }

        h1 {
        margin-bottom: 1rem;
        color: #333;
        }

        h2 {
        margin-bottom: 1rem;
        color: #444;
        }

        /* Menu styles */
        nav li a.menu-button {
        background-color: #008cba;
        padding: 0.8rem 1rem;
        border-radius: 4px;
        }

        nav li a.menu-button:hover {
        background-color: #006a88;
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
    <h1>Sveiki atvykę į Banko aplikaciją!</h1>
    <?php if (isset($username)) : ?>
        <h2>Sveikas, <?php echo $username; ?>!</h2>
    <?php endif; ?>
<nav>
    <ul>
        <li><a href="create_account.php">Sukurti sąskaita</a></li>
        <li><a href="add_funds.php">Pridėti lėšų</a></li>
        <li><a href="withdraw_funds.php">Išimti lėšas</a></li>
    </ul>
</nav>


    <h2>Sąskaitų sąrašas:</h2>
    <?php
    
    // Patikriname, ar vartotojas yra prisijungęs
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        exit();
    }
    if (isset($_SESSION['delete_success']) && $_SESSION['delete_success']) {
        echo '<script>showPopup("Sąskaita sėkmingai ištrinta!");</script>';
        // Clear the session variable to prevent showing the message again on page reload
        unset($_SESSION['delete_success']);
    }
    ?>
    <table>
        <tr>
            <th>Vardas</th>
            <th>Pavardė</th>
            <th>Sąskaitos numeris</th>
            <th>Asmens kodas</th>
            <th>Likutis</th>
            <th>Veiksmas</th>
        </tr>
        <?php foreach ($data['accounts'] as $account) : ?>
            <tr>
                <td><?php echo $account['name']; ?></td>
                <td><?php echo $account['surname']; ?></td>
                <td><?php echo $account['account_number']; ?></td>
                <td><?php echo $account['personal_id']; ?></td>
                <td><?php echo number_format($account['balance'], 2); ?> EUR</td>
                <td>
                <?php if ($account['balance'] <= 0) : ?>
                    <form method="post"  onsubmit="return confirm('Ar tikrai norite ištrinti šią sąskaitą?');">
                        <input type="hidden" name="account_number" value="<?php echo $account['account_number']; ?>">
                        <button type="submit" name="delete">Ištrinti</button>
                    </form>
                    <?php else : ?>
                        <span>Sąskaitoje yra lėšų, negalima ištrinti.</span>
                <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
