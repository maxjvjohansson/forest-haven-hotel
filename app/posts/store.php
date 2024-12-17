<?php

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../functions.php';

if (isset($_POST['room'], $_POST['arrival_date'], $_POST['departure_date'], $_POST['guest_name'], $_POST['transfer_code'])) {

    $room = filter_var(trim($_POST['room']), FILTER_SANITIZE_NUMBER_INT);
    $arrivalDate = htmlspecialchars(trim($_POST['arrival_date']));
    $departureDate = htmlspecialchars(trim($_POST['departure_date']));
    $guestName = htmlspecialchars(trim($_POST['guest_name']));
    $transferCode = htmlspecialchars(trim($_POST['transfer_code']));
    $totalCost = filter_var(trim($_POST['total_cost']), FILTER_VALIDATE_FLOAT);
    $features = $_POST['features'] ?? [];

    // Validate required fields
    if (
        empty($room) ||
        empty($arrivalDate) ||
        empty($departureDate) ||
        empty($guestName) ||
        empty($transferCode) ||
        $totalCost === false
    ) {
        echo json_encode(["error" => "All fields are required and must be valid."]);
        exit;
    }

    // Validate transfercode format
    if (!isValidUuid($transferCode)) {
        echo json_encode(["error" => "Invalid transfer code format."]);
        exit;
    }

    // Sanitized features/function to check if feature is valid
    $cleanFeatures = validateFeatures($database, $features);

    // Calculate number of days of a booking
    $numberOfDays = calculateNumberOfDays($arrivalDate, $departureDate);

    // Check if a room is available at selected dates
    if (!isRoomAvailable($database, $room, $arrivalDate, $departureDate)) {
        echo json_encode(["error" => "Sorry, the room is already booked for the selected dates."]);
        exit;
    }

    // Validate transfercode against external API
    if (!validateTransferCode($transferCode, $totalCost)) {
        echo json_encode(["error" => "Transfer code is invalid or insufficient funds."]);
        exit;
    }

    // Make a deposit through external API
    if (!makeDeposit($guestName, $transferCode, $numberOfDays)) {
        echo json_encode(["error" => "Failed to process the deposit."]);
        exit;
    }

    // Save the booking in the database
    $insertStatement = $database->prepare("
        INSERT INTO bookings (room_id, arrival_date, departure_date, guest_name, total_cost)
        VALUES (:room_id, :arrival_date, :departure_date, :guest_name, :total_cost)
    ");
    $insertStatement->bindParam(':room_id', $room, PDO::PARAM_INT);
    $insertStatement->bindParam(':arrival_date', $arrivalDate, PDO::PARAM_STR);
    $insertStatement->bindParam(':departure_date', $departureDate, PDO::PARAM_STR);
    $insertStatement->bindParam(':guest_name', $guestName, PDO::PARAM_STR);
    $insertStatement->bindParam(':total_cost', $totalCost, PDO::PARAM_STR);
    $insertStatement->execute();

    echo json_encode(["success" => "Booking successfully saved."]);
}
