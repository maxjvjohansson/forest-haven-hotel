<?php

require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/functions.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If not, send to loginpage
    header('Location: login.php');
    exit;
}

// Get prices from rooms
$query = $database->query("SELECT id, type, name, price FROM rooms");
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

// Get prices from features
$query = $database->query("SELECT id, name, price FROM features");
$features = $query->fetchAll(PDO::FETCH_ASSOC);

// Get stars from admin settings
$hotelStars = getHotelStars($database);

// List all bookings if no searchfilter is used
$bookings = [];
$searchQuery = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $searchQuery = htmlspecialchars($_GET['search'], ENT_QUOTES, 'UTF-8');

    // Filter bookings based on search
    $query = $database->prepare("SELECT b.id, b.guest_name, r.name AS room_name, b.arrival_date, b.departure_date, b.total_cost
                                 FROM bookings b
                                 JOIN rooms r ON b.room_id = r.id
                                 WHERE b.guest_name LIKE :search OR r.name LIKE :search");
    $query->execute([':search' => '%' . $searchQuery . '%']);
    $bookings = $query->fetchAll(PDO::FETCH_ASSOC);
} else {
    // If no word is searched, list all bookings
    $query = $database->query("SELECT b.id, b.guest_name, r.name AS room_name, b.arrival_date, b.departure_date, b.total_cost
                                FROM bookings b
                                JOIN rooms r ON b.room_id = r.id");
    $bookings = $query->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forest Haven | Admin Dashboard</title>

    <link rel="icon" href="../assets/icons/logo-black.svg">

    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <header>
        <div class="menu-container">
            <a href="#" class="logo">
                <img src="../assets/icons/logo-white.svg" alt="Forest Haven Logo" class="logo-img">
                <p>Forest Haven Hotel | Admin Dashboard</p>
            </a>
        </div>
        <a href="logout.php" class="logout-button">Logout</a>
    </header>

    <main>
        <form id="adminDashboard" method="POST" action="../app/posts/update.php">
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <p class="success-message">Updates saved successfully!</p>
            <?php endif; ?>

            <!-- Update Room Prices -->
            <fieldset>
                <legend>Update Room Prices</legend>
                <?php foreach ($rooms as $room): ?>
                    <div class="form-group">
                        <label for="room_price_<?= $room['id'] ?>">
                            <?= htmlspecialchars($room['name']) ?> - Current Price: $<?= $room['price'] ?>
                        </label>
                        <input type="number" id="room_price_<?= $room['id'] ?>" name="room_prices[<?= $room['id'] ?>]" value="<?= $room['price'] ?>" required>
                    </div>
                <?php endforeach; ?>
            </fieldset>

            <!-- Update Feature Prices -->
            <fieldset>
                <legend>Update Feature Prices</legend>
                <?php foreach ($features as $feature): ?>
                    <div class="form-group">
                        <label for="feature_price_<?= $feature['id'] ?>">
                            <?= htmlspecialchars($feature['name']) ?> - Current Price: $<?= $feature['price'] ?>
                        </label>
                        <input type="number" id="feature_price_<?= $feature['id'] ?>" name="feature_prices[<?= $feature['id'] ?>]" value="<?= $feature['price'] ?>" required>
                    </div>
                <?php endforeach; ?>
            </fieldset>

            <!-- Update Hotel Stars -->
            <fieldset>
                <legend>Update Hotel Stars</legend>
                <div class="form-group">
                    <label for="hotel_stars">Current Stars: <?= $hotelStars ?> ★</label>
                    <input type="number" id="hotel_stars" name="hotel_stars" value="<?= $hotelStars ?>" min="1" max="5" required>
                </div>
            </fieldset>

            <!-- Confirm/Submit -->
            <button type="submit" class="form-button">Update</button>
        </form>

        <!-- Searchform -->
        <form action="dashboard.php" method="GET" class="search-form">
            <?php if (isset($_GET['success']) && $_GET['success'] == 2): ?>
                <p class="success-message">Booking deleted successfully!</p>
            <?php endif; ?>
            <label for="search">Search Booking (Guest Name or Room):</label>
            <input type="text" id="search" name="search" value="<?= $searchQuery ?>" placeholder="Enter guest name or room">
            <button type="submit" class="form-button">Search</button>
        </form>

        <h3>Manage Bookings</h3>

        <!-- Searchresults -->
        <?php if (count($bookings) > 0): ?>
            <table class="bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Guest Name</th>
                        <th>Room</th>
                        <th>Arrival Date</th>
                        <th>Departure Date</th>
                        <th>Total Cost</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['id'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($booking['guest_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($booking['room_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($booking['arrival_date'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($booking['departure_date'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>$<?= htmlspecialchars($booking['total_cost'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <a href="../app/posts/delete.php?id=<?= $booking['id'] ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-results">No bookings found matching your search criteria.</p>
        <?php endif; ?>

    </main>
</body>


</html>