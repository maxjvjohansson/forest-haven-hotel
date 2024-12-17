function updateTotal() {
    let totalCost = 0;

    // Get correct price from each room
    const roomSelect = document.getElementById('room');
    const roomPrice = parseFloat(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price')) || 0;
    totalCost += roomPrice;

    // Get correct price from each feature if selected
    const featureCheckboxes = document.querySelectorAll('input[name="features[]"]:checked');
    featureCheckboxes.forEach(checkbox => {
        totalCost += parseFloat(checkbox.getAttribute('data-price')) || 0;
    });

    // Update total price in the DOM
    document.getElementById('totalCost').textContent = totalCost.toFixed(2);

    // Update the hidden total_cost input
    document.getElementById('total_cost').value = totalCost.toFixed(2);
}

// Run function directly to show total cost when the page loads
document.addEventListener('DOMContentLoaded', updateTotal);

// Run function when elements change (room selection or feature checkboxes)
document.getElementById('bookingForm').addEventListener('change', updateTotal);
