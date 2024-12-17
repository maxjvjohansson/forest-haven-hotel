<?php

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../functions.php';

if (isset($_POST['room'], $_POST['arrival_date'], $_POST['departure_date'], $_POST['guest_name'], $_POST['transfer_code'])) {

    $room = filter_var(trim($_POST['room']), FILTER_SANITIZE_NUMBER_INT);
    $arrivalDate = htmlspecialchars(trim($_POST['arrival_date']));
    $departureDate = htmlspecialchars(trim($_POST['departure_date']));
    $guestName = htmlspecialchars(trim($_POST['guest_name']));
    $transferCode = htmlspecialchars(trim($_POST['transfer_code']));

    $features = $_POST['features'] ?? [];
    $cleanFeatures = validateFeatures($database, $features);

    // Check a rooms availability
    $statement = $database->prepare("SELECT COUNT(*) FROM bookings WHERE room_id = :room_id
        AND (arrival_date <= :departure_date AND departure_date >= :arrival_date)");
    $statement->bindParam(':room_id', $room, PDO::PARAM_INT);
    $statement->bindParam(':arrival_date', $arrivalDate, PDO::PARAM_STR);
    $statement->bindParam(':departure_date', $departureDate, PDO::PARAM_STR);
    $statement->execute();

    $roomBooked = $statement->fetchColumn();

    // If room is booked
    if ($roomBooked > 0) {
        echo "Sorry, the room is already booked for the selected dates.";
        exit; // Abort if room is booked
    }
}
