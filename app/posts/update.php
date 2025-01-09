<?php

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update room prices in database
    if (isset($_POST['room_prices']) && is_array($_POST['room_prices'])) {
        foreach ($_POST['room_prices'] as $roomId => $newPrice) {
            $query = $database->prepare("UPDATE rooms SET price = :price WHERE id = :id");
            $query->bindParam(':price', $newPrice, PDO::PARAM_INT);
            $query->bindParam(':id', $roomId, PDO::PARAM_INT);
            $query->execute();
        }
    }

    // Update feature prices in database
    if (isset($_POST['feature_prices']) && is_array($_POST['feature_prices'])) {
        foreach ($_POST['feature_prices'] as $featureId => $newPrice) {
            $query = $database->prepare("UPDATE features SET price = :price WHERE id = :id");
            $query->bindParam(':price', $newPrice, PDO::PARAM_INT);
            $query->bindParam(':id', $featureId, PDO::PARAM_INT);
            $query->execute();
        }
    }

    // Update hotel stars
    if (isset($_POST['hotel_stars'])) {
        $hotelStars = (int) $_POST['hotel_stars'];
        $query = $database->prepare("UPDATE admin_settings SET stars = :stars");
        $query->bindParam(':stars', $hotelStars, PDO::PARAM_INT);
        $query->execute();
    }

    // Update discount settings
    if (isset($_POST['discount_min_days'], $_POST['discount_feature_name'])) {
        $discountMinDays = (int)$_POST['discount_min_days'];
        $discountFeatureName = $_POST['discount_feature_name'];

        $query = $database->prepare("
            UPDATE admin_settings 
            SET discount_min_days = :min_days, 
                discount_feature_name = :feature_name
            WHERE id = 1
        ");
        $query->bindParam(':min_days', $discountMinDays, PDO::PARAM_INT);
        $query->bindParam(':feature_name', $discountFeatureName, PDO::PARAM_STR);
        $query->execute();
    }

    // Update rooms that apply for discount
    if (isset($_POST['discount_rooms']) && is_array($_POST['discount_rooms'])) {
        $selectedRooms = $_POST['discount_rooms'];

        $query = $database->prepare("
            UPDATE discount_rooms 
            SET is_active = 0 
            WHERE admin_setting_id = 1
        ");
        $query->execute();

        $query = $database->prepare("
            UPDATE discount_rooms 
            SET is_active = 1 
            WHERE admin_setting_id = 1 AND room_name = :room_name
        ");

        foreach ($selectedRooms as $roomName) {
            $query->bindParam(':room_name', $roomName, PDO::PARAM_STR);
            $query->execute();
        }
    }

    // Send user back to admin dashboard with a message that everything went well
    header('Location: ../../admin/dashboard.php?success=1');
    exit;
}
