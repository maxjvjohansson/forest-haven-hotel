<?php

declare(strict_types=1);

require_once __DIR__ . '/../autoload.php';

// Check for valid ID by GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $bookingId = (int) $_GET['id'];

    try {
        $database->beginTransaction();

        // Delete every related feature from booking_feature table
        $query = $database->prepare("DELETE FROM booking_feature WHERE booking_id = :booking_id");
        $query->bindParam(':booking_id', $bookingId, PDO::PARAM_INT);
        $query->execute();

        // Delete booking from bookings
        $query = $database->prepare("DELETE FROM bookings WHERE id = :id");
        $query->bindParam(':id', $bookingId, PDO::PARAM_INT);
        $query->execute();

        // If both queries went well, commit the transaction
        $database->commit();

        // Redirect user to dashboard and show a success message
        header('Location: /admin/dashboard.php?success=2');
        exit;
    } catch (PDOException $error) {
        $database->rollBack();
        echo 'Error: ' . $error->getMessage();
    }
} else {
    echo "Invalid booking ID.";
}
