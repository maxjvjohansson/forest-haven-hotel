<?php

require_once __DIR__ . '/../autoload.php';

$room_id = $_POST['room'];
$guest_name = $_POST['guest_name'];
$arrival_date = $_POST['arrival_date'];
$departure_date = $_POST['departure_date'];
$transfer_code = $_POST['transfer_code'];
$features = isset($_POST['features']) ? $_POST['features'] : [];

$query = $database->prepare("SELECT * FROM bookings WHERE room_id = :room_id 
    AND ((arrival_date <= :departure_date AND departure_date >= :arrival_date))");
$query->bindParam(':room_id', $room_id);
$query->bindParam(':arrival_date', $arrival_date);
$query->bindParam(':departure_date', $departure_date);
$query->execute();
$existing_booking = $query->fetch(PDO::FETCH_ASSOC);

if ($existing_booking) {
    echo json_encode(['error' => 'Room is not available for the selected dates']);
    exit;
}
