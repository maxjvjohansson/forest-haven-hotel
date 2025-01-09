<?php

// Get room prices from database
$query = $database->query("SELECT id, type, name, price FROM rooms");
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

// Get feature prices from database
$query = $database->query("SELECT id, name, price FROM features");
$features = $query->fetchAll(PDO::FETCH_ASSOC);

// Get discount settings from database
$query = $database->query("SELECT discount_min_days, discount_feature_name FROM admin_settings LIMIT 1");
$discountSettings = $query->fetch(PDO::FETCH_ASSOC);

// Get rooms that are valid for discount
$query = $database->prepare("SELECT room_name FROM discount_rooms WHERE admin_setting_id = 1 AND is_active = 1");
$query->execute();
$activeDiscountRooms = $query->fetchAll(PDO::FETCH_COLUMN);

// Variables for discounts
$minDaysForDiscount = $discountSettings['discount_min_days'];
$discountFeature = $discountSettings['discount_feature_name'];
?>

<h2 class="booking-title" id="bookingTitle">Make a reservation</h2>

<section class="booking-container">
    <form id="bookingForm" method="POST" action="/foresthavenhotel/app/posts/store.php">

        <!-- Select Dates -->
        <div class="date-fields">
            <div>
                <label for="arrival_date">Arrival date:</label>
                <input type="date" id="arrival_date" name="arrival_date" class="hidden-calendar" min="2025-01-01" max="2025-01-31" required>
            </div>
            <div>
                <label for="departure_date">Departure date:</label>
                <input type="date" id="departure_date" name="departure_date" class="hidden-calendar" min="2025-01-01" max="2025-01-31" required>
            </div>
        </div>

        <!-- Select Rooms -->
        <label for="room">Select room:</label>
        <select id="room" name="room" required>
            <?php foreach ($rooms as $room): ?>
                <option value="<?= $room['id'] ?>" data-price="<?= $room['price'] ?>">
                    <?= htmlspecialchars($room['name']) ?> - $<?= $room['price'] ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="info-icon" id="roomInfoIcon">
            <img src="assets/icons/info.svg" alt="Info" class="icon">
        </span>
        <div class="info-popup" id="roomInfoPopup">
            Room prices are per day.
        </div>

        <!-- Add features -->
        <div class="form-container">
            <fieldset>
                <legend>Add features:</legend>
                <?php foreach ($features as $feature): ?>
                    <label>
                        <input type="checkbox" name="features[]" value="<?= $feature['id'] ?>" data-price="<?= $feature['price'] ?>">
                        <?= htmlspecialchars($feature['name']) ?> - $<?= $feature['price'] ?>
                    </label><br>
                <?php endforeach; ?>
                <span class="info-icon" id="featureInfoIcon">
                    <img src="assets/icons/info.svg" alt="Info" class="icon">
                </span>
                <div class="info-popup" id="featureInfoPopup">
                    Feature prices are for the entire booking period.
                </div>
            </fieldset>

            <!-- Discount card -->
            <div class="discount-card">
                <div class="discount-header">
                    <h3>Discount Offer!</h3>
                </div>
                <div class="discount-body">
                    <p>Book one of the following rooms:</p>
                    <ul>
                        <?php foreach ($activeDiscountRooms as $roomName): ?>
                            <li><?= htmlspecialchars($roomName) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <p>Stay at least <span class="highlight"><?= htmlspecialchars($minDaysForDiscount) ?></span> days to qualify.</p>
                    <p>Discount includes: <span class="highlight"><?= htmlspecialchars($discountFeature) ?></span></p>
                </div>
            </div>
        </div>

        <!-- Guest Name & Transfer Code -->
        <div class="guest-info">
            <div>
                <label for="guest_name">Guest Name:</label>
                <input type="text" id="guest_name" name="guest_name" required>
            </div>
            <div>
                <label for="transfer_code">Transfer Code:</label>
                <input type="text" id="transfer_code" name="transfer_code" required>
            </div>
        </div>

        <!-- Total Cost Display -->
        <p>Total: $<span id="totalCost">0</span></p>

        <!-- Hidden input for total cost -->
        <input type="hidden" id="total_cost" name="total_cost" value="0">

        <!-- Confirm/Submit -->
        <button type="submit">Book room</button>

    </form>

    <section class="room-preview">
        <div class="room-card" id="room-1">
            <h3>Woodland Retreat</h3>
            <img src="assets/images/room-woodland-retreat.webp" alt="Overview of the budget room">
            <ul>
                <li>Surrounded by serene forest views for ultimate relaxation.</li>
                <li>Private balcony with cozy seating for stargazing.</li>
                <li>Complimentary access to hiking trails and guided tours.</li>
            </ul>
        </div>
        <div class="room-card hidden" id="room-2">
            <h3>Forest Haven</h3>
            <img src="assets/images/room-forest-haven.webp" alt="Overview of the standard room">
            <ul>
                <li>A harmonious blend of comfort and style with forest-inspired decor.</li>
                <li>Includes a spacious queen-sized bed and a cozy seating nook.</li>
                <li>Perfect for couples or small families looking for a balance of luxury and affordability.</li>
            </ul>
        </div>
        <div class="room-card hidden" id="room-3">
            <h3>Canopy Grand Suite</h3>
            <img src="assets/images/room-canopy-grand-suite.webp" alt="Overview of the luxury room">
            <ul>
                <li>Luxurious suite with high ceilings and panoramic forest views.</li>
                <li>Includes a king-sized bed, lounge area, and private jacuzzi.</li>
                <li>Designed for guests seeking elegance and adventure.</li>
            </ul>
        </div>
    </section>

</section>