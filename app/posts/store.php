<?php

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['room'], $_POST['arrival_date'], $_POST['departure_date'], $_POST['guest_name'], $_POST['transfer_code'])) {

        $room = filter_var(trim($_POST['room']), FILTER_SANITIZE_NUMBER_INT);
        $arrivalDate = htmlspecialchars(trim($_POST['arrival_date']));
        $departureDate = htmlspecialchars(trim($_POST['departure_date']));
        $guestName = htmlspecialchars(trim($_POST['guest_name']));
        $transferCode = htmlspecialchars(trim($_POST['transfer_code']));
        $features = $_POST['features'] ?? [];

        // Validate required fields
        if (empty($room) || empty($arrivalDate) || empty($departureDate) || empty($guestName) || empty($transferCode)) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "All fields are required and must be valid."]);
            exit;
        }

        // Validate transfercode format
        if (!isValidUuid($transferCode)) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Invalid transfer code format."]);
            exit;
        }

        // Get price from room
        $roomQuery = $database->prepare("SELECT price FROM rooms WHERE id = :room_id");
        $roomQuery->bindParam(':room_id', $room, PDO::PARAM_INT);
        $roomQuery->execute();
        $roomPrice = $roomQuery->fetchColumn();

        // Get actual number of stars for the hotel
        $stars = getHotelStars($database);

        // Sanitized features/function to check if feature is valid and get price from features
        $cleanFeatures = validateFeatures($database, $features);
        $featureQuery = $database->prepare("
        SELECT id, name, price 
        FROM features 
        WHERE id IN (" . implode(',', array_fill(0, count($cleanFeatures), '?')) . ")
    ");
        $featureQuery->execute($cleanFeatures);
        $selectedFeatures = $featureQuery->fetchAll(PDO::FETCH_ASSOC);

        // Calculate the total cost of selected features
        $totalFeatureCost = array_reduce($selectedFeatures, fn($sum, $feature) => $sum + $feature['price'], 0);

        // Calculate total cost of the booking
        $numberOfDays = calculateDays($arrivalDate, $departureDate);
        $totalCost = ($roomPrice * $numberOfDays) + $totalFeatureCost;

        // Check if a room is available at selected dates
        if (!isRoomAvailable($database, $room, $arrivalDate, $departureDate)) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Sorry, the room is already booked for the selected dates."]);
            exit;
        }

        // Validate transfercode against external API
        if (!validateTransferCode($transferCode, $totalCost)) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Transfer code is invalid or insufficient funds."]);
            exit;
        }

        // Make a deposit through external API
        if (!makeDeposit($transferCode)) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Failed to process the deposit."]);
            exit;
        }

        // Save the booking in the database
        $insertBooking = $database->prepare("
        INSERT INTO bookings (room_id, room_price, guest_name, arrival_date, departure_date, total_cost, transfer_code)
        VALUES (:room_id, :room_price, :guest_name, :arrival_date, :departure_date, :total_cost, :transfer_code)
    ");
        $insertBooking->execute([
            ':room_id' => $room,
            ':room_price' => $roomPrice,
            ':guest_name' => $guestName,
            ':arrival_date' => $arrivalDate,
            ':departure_date' => $departureDate,
            ':total_cost' => $totalCost,
            ':transfer_code' => $transferCode
        ]);

        $bookingId = $database->lastInsertId();

        // Save features in booking_feature table
        foreach ($selectedFeatures as $feature) {
            $insertFeature = $database->prepare("
            INSERT INTO booking_feature (booking_id, feature_id, feature_cost)
            VALUES (:booking_id, :feature_id, :feature_cost)
        ");
            $insertFeature->execute([
                ':booking_id' => $bookingId,
                ':feature_id' => $feature['id'],
                ':feature_cost' => $feature['price']
            ]);
        }

        // Generate response if booking went well
        $response = [
            "island" => "Lindenwood Isle",
            "hotel" => "Forest Haven Hotel",
            "arrival_date" => $arrivalDate,
            "departure_date" => $departureDate,
            "total_cost" => $totalCost,
            "stars" => $stars,
            "features" => array_map(fn($feature) => [
                "name" => $feature['name'],
                "cost" => $feature['price'],
            ], $selectedFeatures),
            "additional_info" => [
                "greeting" => "Thank you for choosing Forest Haven Hotel",
                "imageUrl" => "https://maxjvjohansson.se/foresthavenhotel/assets/images/forest-haven-greeting.png"
            ]
        ];

        // Send response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);

        header('Location: ../../index.php');
        exit;
    }
} else {
    header('Location: ../../views/booking_form.php');
    exit;
}
