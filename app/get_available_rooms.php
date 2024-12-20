<?php
// Initiate autoload and DB connection
require_once __DIR__ . '/autoload.php';

// Get room_id from GET-Parameters
$roomId = isset($_GET['room_id']) ? intval($_GET['room_id']) : 0;

// Validate room ID
if ($roomId == 0) {
    echo json_encode(["error" => "Invalid room ID"]);
    exit;
}

// Fetch bookings for the selected room
$query = $database->prepare("
    SELECT arrival_date, departure_date
    FROM bookings
    WHERE room_id = :room_id
");
$query->execute(['room_id' => $roomId]);
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

// Debugging: If there are no bookings, return an empty array
if (empty($bookings)) {
    echo json_encode([]);
    exit;
}

// Create a list of unavailable dates
$unavailableDates = [];
foreach ($bookings as $booking) {
    $start = new DateTime($booking['arrival_date']);
    $end = new DateTime($booking['departure_date']);
    $end->modify('-1 day');  // Include departure day

    while ($start <= $end) {
        $unavailableDates[] = $start->format('Y-m-d');
        $start->modify('+1 day');
    }
}

// Return unavailable dates as JSON
header('Content-Type: application/json');
echo json_encode($unavailableDates); // Send back JSON array
