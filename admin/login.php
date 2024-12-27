<?php

declare(strict_types=1);

session_start();

require_once __DIR__ . '/../app/autoload.php';

$error = [];

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    // Verify username and password
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
        header('Location: /admin/dashboard.php');
        exit;
    }
}

if (isset($_SESSION['error'])) {
    foreach ($_SESSION['error'] as $message) {
        echo $message;
    }
    unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forest Haven | Admin Login</title>
</head>

<body>

    <h2>Forest Haven Hotel | Admin Login</h2>

    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br>

        <button type="submit">Log in</button>
        <a href="/views/index.php" class="back-button">Back to Home</a>
    </form>

</body>

</html>