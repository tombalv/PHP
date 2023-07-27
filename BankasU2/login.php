<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pradedame sesiją
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./css/login.css">
    <title>Prisijungimas</title>
</head>
<body>
    <div>
    <h1 class="title">Prisijungimas</h1>
    <?php if (isset($_GET['error']) && $_GET['error'] == 1) : ?>
        <p class="error-message">Neteisingas vartotojo vardas arba slaptažodis!</p>
    <?php endif; ?>
    <form class="login-form" action="authenticate.php" method="post">
        <label for="username">Vartotojo vardas:</label>
        <input type="text" id="username" name="username" required><br>

        <label for="password">Slaptažodis:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit" class="login-button">Prisijungti</button>
    </form>
    </div>
    
</body>
</html>

