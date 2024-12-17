<?php

declare(strict_types=1);

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

function validateFeatures(PDO $database, array $features): array
{
    // Convert all feature values to whole numbers and filter away invalid values
    $cleanFeatures = array_map('intval', $features);
    $cleanFeatures = array_filter($cleanFeatures, fn($value) => $value > 0);

    if (!empty($cleanFeatures)) {
        $placeholders = implode(',', array_fill(0, count($cleanFeatures), '?'));
        $statement = $database->prepare("SELECT id FROM features WHERE id IN ($placeholders)");

        $statement->execute($cleanFeatures);
        $validFeatures = $statement->fetchAll(PDO::FETCH_COLUMN);

        // Return only the features that exist in the database
        return array_intersect($cleanFeatures, $validFeatures);
    }

    return []; // Return empty array if features don't exist
}

// Function to calculate how many days are being booked
function calculateNumberOfDays(string $arrivalDate, string $departureDate): int
{
    $arrival = new DateTime($arrivalDate);
    $departure = new DateTime($departureDate);
    return $arrival->diff($departure)->days;
}

// Function to check if a room is available at selected dates
function isRoomAvailable(PDO $database, int $room, string $arrivalDate, string $departureDate): bool
{
    $statement = $database->prepare("
        SELECT COUNT(*) FROM bookings 
        WHERE room_id = :room_id
        AND (arrival_date <= :departure_date AND departure_date >= :arrival_date)
    ");
    $statement->bindParam(':room_id', $room, PDO::PARAM_INT);
    $statement->bindParam(':arrival_date', $arrivalDate, PDO::PARAM_STR);
    $statement->bindParam(':departure_date', $departureDate, PDO::PARAM_STR);
    $statement->execute();

    return $statement->fetchColumn() == 0;
}

// Function to check if UUID is valid
function isValidUuid(string $uuid): bool
{
    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid) === 1;
}

// Validate transfercode on centralbank API
function validateTransferCode(string $transferCode, float $totalCost): bool
{
    $client = new Client();

    try {
        $response = $client->post('https://www.yrgopelago.se/centralbank/transferCode', [
            'json' => [
                'transferCode' => $transferCode,
                'totalcost' => $totalCost
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return isset($data['status']) && $data['status'] === 'valid';
    } catch (RequestException $e) {
        return false;
    }
}

// Deposit to centralbank
function makeDeposit(string $guestName, string $transferCode, int $numberOfDays): bool
{
    $client = new Client();

    try {
        $response = $client->post('https://www.yrgopelago.se/centralbank/deposit', [
            'json' => [
                'user' => $guestName,
                'transferCode' => $transferCode,
                'numberOfDays' => $numberOfDays
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return isset($data['status']) && $data['status'] === 'success';
    } catch (RequestException $e) {
        return false;
    }
}
