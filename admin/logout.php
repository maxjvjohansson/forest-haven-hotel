<?php

declare(strict_types=1);

// START SESSION IF NOT ALREADY STARTED
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Remove user session
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}

// End session
session_destroy();

// Send user back to homepage
header('Location: ../index.php');
exit;
