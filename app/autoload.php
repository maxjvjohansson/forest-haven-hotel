<?php

declare(strict_types=1);

// START SESSION IF NOT ALREADY STARTED
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Initialize dotenv and load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$databasePath = __DIR__ . '/../app/database';

// Get database name from environment variables
$databaseName = $_ENV['DB_NAME'];

// Setup the database connection using environment variables
$database = new PDO("sqlite:" . $databasePath . '/' . $databaseName);

// Get admin credentials from environment variables
define('ADMIN_USERNAME', $_ENV['ADMIN_USER']);  // Admin username
define('ADMIN_PASSWORD', $_ENV['API_KEY']);  // Admin password/api-key
