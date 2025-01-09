<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . '/autoload.php';

// Get discount settings and return as JSON
try {
    $query = $database->query("SELECT discount_feature_name, discount_min_days FROM admin_settings LIMIT 1");
    $discountSettings = $query->fetch(PDO::FETCH_ASSOC);
    $currentDiscountFeature = $discountSettings['discount_feature_name'] ?? '';
    $currentMinDays = $discountSettings['discount_min_days'] ?? 3;

    $queryRooms = $database->prepare("SELECT room_name FROM discount_rooms WHERE is_active = 1");
    $queryRooms->execute();
    $discountRooms = $queryRooms->fetchAll(PDO::FETCH_COLUMN);

    $response = [
        'discount_feature' => $currentDiscountFeature,
        'min_days_for_discount' => (int)$currentMinDays,
        'discount_rooms' => $discountRooms,
    ];

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Unable to fetch discount info.']);
}
