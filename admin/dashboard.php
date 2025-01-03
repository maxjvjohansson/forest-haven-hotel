<?php

require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/functions.php';

// Get prices from rooms
$query = $database->query("SELECT id, type, name, price FROM rooms");
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

// Get prices from features
$query = $database->query("SELECT id, name, price FROM features");
$features = $query->fetchAll(PDO::FETCH_ASSOC);

// Get stars from admin settings
$hotelStars = getHotelStars($database);

?>

<title>Forest Haven | Admin Dashboard</title>

<form id="adminDashboard" method="POST" action="../../app/posts/update.php">
    <h2>Forest Haven Hotel | Admin Dashboard</h2>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <p style="color: green;">Updates saved successfully!</p>
    <?php endif; ?>

    <!-- Update Room Prices -->
    <fieldset>
        <legend>Update Room Prices</legend>
        <?php foreach ($rooms as $room): ?>
            <label for="room_price_<?= $room['id'] ?>">
                <?= htmlspecialchars($room['name']) ?> - Current Price: $<?= $room['price'] ?>
            </label>
            <input type="number" id="room_price_<?= $room['id'] ?>" name="room_prices[<?= $room['id'] ?>]" value="<?= $room['price'] ?>" required><br>
        <?php endforeach; ?>
    </fieldset>

    <!-- Update Feature Prices -->
    <fieldset>
        <legend>Update Feature Prices</legend>
        <?php foreach ($features as $feature): ?>
            <label for="feature_price_<?= $feature['id'] ?>">
                <?= htmlspecialchars($feature['name']) ?> - Current Price: $<?= $feature['price'] ?>
            </label>
            <input type="number" id="feature_price_<?= $feature['id'] ?>" name="feature_prices[<?= $feature['id'] ?>]" value="<?= $feature['price'] ?>" required><br>
        <?php endforeach; ?>
    </fieldset>

    <!-- Update Hotel Stars -->
    <fieldset>
        <legend>Update Hotel Stars</legend>
        <label for="hotel_stars">Current Stars: <?= $hotelStars ?> ★</label>
        <input type="number" id="hotel_stars" name="hotel_stars" value="<?= $hotelStars ?>" min="1" max="5" required>
    </fieldset>

    <!-- Confirm/Submit -->
    <button type="submit">Update</button>

    <!-- Logout -->
    <a href="logout.php" class="logout-button">Logout</a>
</form>