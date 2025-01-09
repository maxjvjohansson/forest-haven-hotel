<?php
session_start();

if (!isset($_SESSION['receipt'])) {
    // Throw error if receipt does not exist
    header('Content-Type: application/json', true, 400);
    echo json_encode(["error" => "No receipt available."]);
    exit;
}

// Get receipt from session
$receipt = $_SESSION['receipt'];

// Clear receipt from session
unset($_SESSION['receipt']);

// Unset booking_data
unset($_SESSION['booking_data']);

// Return receipt as JSON
header('Content-Type: application/json');
echo json_encode($receipt, JSON_PRETTY_PRINT);
exit;
