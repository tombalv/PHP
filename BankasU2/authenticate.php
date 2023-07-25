<?php
$users = [
    ['username' => 'admin', 'password' => 'admin'],
    ['username' => 'user1', 'password' => 'user123'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Patikriname ar vartotojas yra sąraše ir slaptažodis teisingas
    $authenticatedUser = null;
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            $authenticatedUser = $user;
            break;
        }
    }

    if ($authenticatedUser) {
        // Nustatome prisijungusio vartotojo sesijos kintamuosius
        session_start();
        $_SESSION['username'] = $authenticatedUser['username'];
        $_SESSION['role'] = ($authenticatedUser['username'] === 'admin') ? 'admin' : 'user';

        // Nukreipiame į pagrindinį puslapį po sėkmingo prisijungimo
        header('Location: index.php');
        exit();
    } else {
        // Nukreipiame į prisijungimo puslapį su klaidos pranešimu
        header('Location: login.php?error=1');
        exit();
    }
}

?>