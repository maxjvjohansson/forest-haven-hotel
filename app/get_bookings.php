<?php

declare(strict_types=1);

require_once __DIR__ . '/autoload.php';

// Function to get already booked dates and return as JSON
function getBookedDates(PDO $database): string
{
    try {
        $sql = "SELECT room_id, arrival_date AS `from`, departure_date AS `to` FROM bookings";
        $statement = $database->query($sql);
        $bookings = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Correct format for dates to use in flatpickr
        foreach ($bookings as &$booking) {
            $booking['from'] = date('Y-m-d', strtotime($booking['from']));
            $booking['to'] = date('Y-m-d', strtotime($booking['to']));
        }
        return json_encode($bookings);
    } catch (Exception $e) {
        error_log('Error fetching bookings: ' . $e->getMessage());
        return json_encode([]);
    }
}

// Send response as JSON
header('Content-Type: application/json');
echo getBookedDates($database);
