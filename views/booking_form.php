<?php

// Get room prices from database
$query = $database->query("SELECT id, type, name, price FROM rooms");
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

// Get feature prices from database
$query = $database->query("SELECT id, name, price FROM features");
$features = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<h2 class="booking-title">Make a reservation</h2>

<div class="booking-container">
    <form id="bookingForm" method="POST" action="../../app/posts/store.php">

        <!-- Dates -->
        <label for="arrival_date">Arrival date:</label>
        <input type="date" id="arrival_date" name="arrival_date" class="hidden-calendar" min="2025-01-01" max="2025-01-31" required>

        <label for="departure_date">Departure date:</label>
        <input type="date" id="departure_date" name="departure_date" class="hidden-calendar" min="2025-01-01" max="2025-01-31" required>

        <!-- Select Rooms -->
        <label for="room">Select room:</label>
        <select id="room" name="room" required>
            <?php foreach ($rooms as $room): ?>
                <option value="<?= $room['id'] ?>" data-price="<?= $room['price'] ?>">
                    <?= htmlspecialchars($room['name']) ?> - $<?= $room['price'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Add features -->
        <fieldset>
            <legend>Add features:</legend>
            <?php foreach ($features as $feature): ?>
                <label>
                    <input type="checkbox" name="features[]" value="<?= $feature['id'] ?>" data-price="<?= $feature['price'] ?>">
                    <?= htmlspecialchars($feature['name']) ?> - $<?= $feature['price'] ?>
                </label><br>
            <?php endforeach; ?>
        </fieldset>

        <!-- Guest Name -->
        <label for="guest_name">Guest Name:</label>
        <input type="text" id="guest_name" name="guest_name" required>

        <!-- Transfer Code -->
        <label for="transfer_code">Transfer Code:</label>
        <input type="text" id="transfer_code" name="transfer_code" required>

        <!-- Total Cost Display -->
        <p><strong>Total:</strong> $<span id="totalCost">0</span></p>

        <!-- Hidden input for total cost -->
        <input type="hidden" id="total_cost" name="total_cost" value="0">

        <!-- Confirm/Submit -->
        <button type="submit">Book room</button>

        <div id="selectedRoomPreview" class="room-preview">
            <h3>Select a room to see details</h3>
        </div>

    </form>
</div>