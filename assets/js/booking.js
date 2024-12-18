function updateTotal() {
    let totalCost = 0;

    // Get room price from the selected option
    const roomSelect = document.getElementById('room');
    const roomPrice = parseFloat(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price')) || 0;

    // Get arrival and departure dates
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');
    const arrivalDate = new Date(arrivalInput.value);
    const departureDate = new Date(departureInput.value);

    // Calculate number of days if both dates are selected
    let numberOfDays = 1; // Default to 1 day if no valid dates are selected
    if (arrivalInput.value && departureInput.value && departureDate > arrivalDate) {
        const timeDifference = Math.abs(departureDate - arrivalDate);
        numberOfDays = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
    }

    // Calculate room cost
    totalCost += roomPrice * numberOfDays;

    // Add feature prices (fixed cost for each booking, not for each day)
    const featureCheckboxes = document.querySelectorAll('input[name="features[]"]:checked');
    featureCheckboxes.forEach(checkbox => {
        totalCost += parseFloat(checkbox.getAttribute('data-price')) || 0;
    });

    // Update total price in the DOM
    document.getElementById('totalCost').textContent = totalCost.toFixed(2);

    // Update the hidden total_cost input (serverside)
    document.getElementById('total_cost').value = totalCost.toFixed(2);
}

// Automatically calculate total cost on page load
document.addEventListener('DOMContentLoaded', () => {
    updateTotal();
});

// Recalculate total cost whenever inputs change
document.getElementById('bookingForm').addEventListener('change', updateTotal);

