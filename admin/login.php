<?php

declare(strict_types=1);

session_start();

// Inkludera autoload.php för att ladda miljövariabler och andra inställningar
require_once __DIR__ . '/../app/autoload.php';

$error = [];

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Verifiera användarnamn och lösenord (API-nyckel)
    if ($username !== ADMIN_USERNAME) {
        $error[] = "User not found!";
        $_SESSION['error'] = $error;
        header('Location: login.php');
        exit;
    }

    if ($password !== ADMIN_PASSWORD) {
        $error[] = "Password not correct!";
        $_SESSION['error'] = $error;
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin/dashboard.php');
        exit;
    }
}

if (isset($_SESSION['error'])) {
    foreach ($_SESSION['error'] as $msg) {
        echo "<p style='color:red;'>$msg</p>";
    }
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="sv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>

<body>

    <form method="POST">
        <label for="username">Användarnamn:</label>
        <input type="text" name="username" id="username" required><br>

        <label for="password">Lösenord (API-nyckel):</label>
        <input type="password" name="password" id="password" required><br>

        <button type="submit">Logga in</button>
    </form>

</body>

</html>