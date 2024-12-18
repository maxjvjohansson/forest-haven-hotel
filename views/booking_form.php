<?php

// Get room prices from database
$query = $database->query("SELECT id, type, name, price FROM rooms");
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

// Get feature prices from database
$query = $database->query("SELECT id, name, price FROM features");
$features = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<form id="bookingForm" method="POST" action="../../app/posts/store.php">
    <h2>Make a reservation</h2>

    <!-- Dates -->
    <label for="arrival_date">Arrival date:</label>
    <input type="date" id="arrival_date" name="arrival_date" min="2025-01-01" max="2025-01-31" required>

    <label for="departure_date">Departure date:</label>
    <input type="date" id="departure_date" name="departure_date" min="2025-01-01" max="2025-01-31" required>

    <!-- Select Rooms -->
    <label for="room">Select room:</label>
    <select id="room" name="room" required onchange="updateTotal()">
        <?php foreach ($rooms as $room): ?>
            <option value="<?= $room['id'] ?>" data-price="<?= $room['price'] ?>">
                <?= htmlspecialchars($room['name']) ?> - $<?= number_format($room['price'], 2) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Add features -->
    <fieldset>
        <legend>Add features:</legend>
        <?php foreach ($features as $feature): ?>
            <label>
                <input type="checkbox" name="features[]" value="<?= $feature['id'] ?>" data-price="<?= $feature['price'] ?>" onchange="updateTotal()">
                <?= htmlspecialchars($feature['name']) ?> - $<?= number_format($feature['price'], 2) ?>
            </label><br>
        <?php endforeach; ?>
    </fieldset>

    <!-- Guest Name -->
    <label for="guest_name">Guest Name:</label>
    <input type="text" id="guest_name" name="guest_name" required>

    <!-- Transfer Code -->
    <label for="transfer_code">Transfer Code:</label>
    <input type="text" id="transfer_code" name="transfer_code">

    <!-- Total Cost Display -->
    <p><strong>Total:</strong> $<span id="totalCost">0.00</span></p>

    <!-- Hidden input for total cost -->
    <input type="hidden" id="total_cost" name="total_cost" value="0">

    <!-- Confirm/Submit -->
    <button type="submit">Book room</button>
</form>