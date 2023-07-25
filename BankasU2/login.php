<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pradedame sesiją
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Prisijungimas</title>
    
    <style>
        body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    
    background-color: #f5f5f5;
}

.container {
    max-width: 600px;
    display: flex;
    flex-direction: column;
    align-items: center;

}

/* Header styles */
.title {
    color: #006a88;
    text-align: center;
    margin-bottom: 20px;
}

/* Form styles */
.login-form {
    background-color: #f5f5f5;
    padding: 20px;
    border-radius: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.login-form label {
    display: block;
    margin-bottom: 10px;
    font-weight: bold;
}

.login-form input[type="text"],
.login-form input[type="password"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

.login-form button {
    background-color: #006a88;
    color: #fff;
    padding: 10px 20px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    
}

.login-form button:hover {
    background-color: #00506b;
}

/* Error message styles */
.error-message {
    color: red;
    font-size: 14px;
    text-align: center;
    margin-bottom: 20px;
}
    </style>
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

