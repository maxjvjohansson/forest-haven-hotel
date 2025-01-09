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

        // Get discount settings from database
        $discountQuery = $database->query("SELECT discount_min_days, discount_feature_name FROM admin_settings LIMIT 1");
        $discountSettings = $discountQuery->fetch(PDO::FETCH_ASSOC);

        $activeRoomsQuery = $database->prepare("
            SELECT room_name 
            FROM discount_rooms 
            WHERE admin_setting_id = 1 AND is_active = 1
        ");
        $activeRoomsQuery->execute();
        $activeDiscountRooms = $activeRoomsQuery->fetchAll(PDO::FETCH_COLUMN);

        $minDaysForDiscount = $discountSettings['discount_min_days'];
        $discountFeature = $discountSettings['discount_feature_name'];

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

        // Get prices from selected room
        $roomQuery = $database->prepare("SELECT price, name FROM rooms WHERE id = :room_id");
        $roomQuery->bindParam(':room_id', $room, PDO::PARAM_INT);
        $roomQuery->execute();
        $roomData = $roomQuery->fetch(PDO::FETCH_ASSOC);
        $roomPrice = $roomData['price'];
        $roomName = $roomData['name'];

        // Validate that the room is granted for discount
        $isEligibleRoom = in_array($roomName, $activeDiscountRooms);

        // Validate features and get actual feature price
        $cleanFeatures = validateFeatures($database, $features);
        $featureQuery = $database->prepare("
            SELECT id, name, price 
            FROM features 
            WHERE id IN (" . implode(',', array_fill(0, count($cleanFeatures), '?')) . ")
        ");
        $featureQuery->execute($cleanFeatures);
        $selectedFeatures = $featureQuery->fetchAll(PDO::FETCH_ASSOC);

        // Count number of days per booking
        $numberOfDays = calculateDays($arrivalDate, $departureDate);

        // Calculate total cost
        $totalCost = $roomPrice * $numberOfDays;

        // Apply discount if conditions is true
        $totalFeatureCost = 0;
        foreach ($selectedFeatures as $feature) {
            if (
                $numberOfDays >= $minDaysForDiscount &&
                $isEligibleRoom &&
                strcasecmp($feature['name'], $discountFeature) === 0
            ) {
                continue;
            }
            $totalFeatureCost += $feature['price'];
        }

        $totalCost += $totalFeatureCost;

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

        // Make deposit via centralbank endpoint
        if (!makeDeposit($transferCode)) {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Failed to process the deposit."]);
            exit;
        }

        // Save booking in database
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

        // Save features in booking_feature table in database
        foreach ($selectedFeatures as $feature) {
            $insertFeature = $database->prepare("
                INSERT INTO booking_feature (booking_id, feature_id, feature_cost)
                VALUES (:booking_id, :feature_id, :feature_cost)
            ");
            $insertFeature->execute([
                ':booking_id' => $bookingId,
                ':feature_id' => $feature['id'],
                ':feature_cost' => (
                    $numberOfDays >= $minDaysForDiscount &&
                    $isEligibleRoom &&
                    strcasecmp($feature['name'], $discountFeature) === 0
                ) ? 0 : $feature['price']
            ]);
        }

        // If all went well, create and return response/reciept as JSON
        $_SESSION['receipt'] = [
            "island" => "Lindenwood Isle",
            "hotel" => "Forest Haven Hotel",
            "arrival_date" => $arrivalDate,
            "departure_date" => $departureDate,
            "total_cost" => $totalCost,
            "features" => array_map(fn($feature) => [
                "name" => $feature['name'],
                "cost" => (
                    $numberOfDays >= $minDaysForDiscount &&
                    $isEligibleRoom &&
                    strcasecmp($feature['name'], $discountFeature) === 0
                ) ? 0 : $feature['price']
            ], $selectedFeatures),
            "additional_info" => [
                "greeting" => "Thank you for choosing Forest Haven Hotel",
                "imageUrl" => "https://maxjvjohansson.se/foresthavenhotel/assets/images/forest-haven-greeting.webp"
            ]
        ];

        $_SESSION['booking_data'] = [
            'room' => $room,
            'arrival_date' => $arrivalDate,
            'departure_date' => $departureDate,
            'guest_name' => $guestName,
            'transfer_code' => $transferCode,
            'features' => $selectedFeatures
        ];

        header('Location: ../../views/receipt.php');
        exit;
    }
} else {
    header('Location: ../../views/booking_form.php');
    exit;
}
