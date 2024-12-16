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
        <option value="1" data-price="1">Woodland Retreat - $1</option>
        <option value="2" data-price="2">Forest Haven - $2</option>
        <option value="3" data-price="3">Canopy Grand Suite - $3</option>
    </select>

    <!-- Add features -->
    <fieldset>
        <legend>Add features:</legend>
        <label><input type="checkbox" name="features[]" value="1" data-price="1" onchange="updateTotal()"> Forest-Themed Breakfast Buffet - $1</label><br>
        <label><input type="checkbox" name="features[]" value="2" data-price="2" onchange="updateTotal()"> Nature-Inspired Spa Retreat - $2</label><br>
        <label><input type="checkbox" name="features[]" value="3" data-price="1" onchange="updateTotal()"> Guided Forest Hike - $1</label><br>
        <label><input type="checkbox" name="features[]" value="4" data-price="2" onchange="updateTotal()"> Tree Canopy Adventure Course - $2</label><br>
    </fieldset>

    <!-- Guest Name -->
    <label for="guest_name">Guest Name:</label>
    <input type="text" id="guest_name" name="guest_name" required>

    <!-- Transfer Code -->
    <label for="transfer_code">Transfer Code:</label>
    <input type="text" id="transfer_code" name="transfer_code">

    <!-- Total Cost Display -->
    <p>Total Cost: $<span id="totalCost">0.00</span></p>

    <!-- Confirm/Submit -->
    <button type="submit">Book room</button>
</form>