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
    <link rel="stylesheet" href="./css/index.css">
    <title>Banko aplikacija</title>
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
        echo '<div class="success-message">';
        echo 'Sąskaita sėkmingai ištrinta.';
        echo '</div>';

        // Clear the PHP session variable to avoid showing the message again on subsequent page loads
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
