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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Patikriname, ar formoje buvo pasirinkta operacija "delete"
    if (isset($_POST['delete'])) {
        $accountNumberToDelete = $_POST['account_number'];
        $data = readDataFromJson();

        // Ištriname sąskaitą iš sąrašo pagal sąskaitos numerį
        foreach ($data['accounts'] as $key => $account) {
            if ($account['account_number'] === $accountNumberToDelete) {
                unset($data['accounts'][$key]);
                break;
            }
        }

        // Išsaugome duomenis į JSON failą be ištrintos sąskaitos
        saveDataToJson($data);

        // Po sėkmingo ištrynimo, galime peradresuoti vartotoją į sąskaitų sąrašo puslapį
        header('Location: index.php');
        exit();
    }

    // Patikriname, ar formoje buvo pasirinkta operacija "add" arba "withdraw"
    if (isset($_POST['operation'])) {
        $data = readDataFromJson();
        $accountNumber = $_POST['account_number'];
        $amount = floatval($_POST['amount']);
        $operation = $_POST['operation'];

        // Raskime sąskaitą pagal sąskaitos numerį
        foreach ($data['accounts'] as &$account) {
            if ($account['account_number'] === $accountNumber) {
                // Įrašome transakciją į sąskaitos duomenis
                $transaction = [
                    'date' => date('Y-m-d H:i:s'),
                    'type' => $operation,
                    'amount' => $amount
                ];
                $account['transactions'][] = $transaction;

                // Atnaujiname sąskaitos likutį
                if ($operation === 'add') {
                    $account['balance'] += $amount;
                } elseif ($operation === 'withdraw') {
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
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Banko aplikacija</title>
    <style>
    



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
</head>
<body>
    <h1>Sveiki atvykę į Banko aplikaciją!</h1>
    <?php if (isset($username)) : ?>
        <h2>Sveikas, <?php echo $username; ?>!</h2>
    <?php endif; ?>
<nav>
    <ul>
    <li><a href="create_account.php">Nauja sąskaita</a></li>
        <li><a href="add_funds.php?account_number=<?php echo $account['account_number']; ?>">Pridėti lėšų</a></li>
        <li><a href="withdraw_funds.php">Išimti lėšas</a></li>
    </ul>
</nav>

    <h2>Sąskaitų sąrašas:</h2>
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
                <td><?php echo $account['personal_code']; ?></td>
                <td><?php echo $account['balance']; ?></td>
                <td>
                <form method="post" onsubmit="return confirm('Ar tikrai norite ištrinti šią sąskaitą?');">
                <input type="hidden" name="account_number" value="<?php echo $account['account_number']; ?>">
                <button type="submit" name="delete">Ištrinti</button>
            </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
