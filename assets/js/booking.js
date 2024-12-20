function updateTotal() {
    let totalCost = 0;

    // Get room price
    const roomSelect = document.getElementById('room');
    const roomPrice = parseInt(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price')) || 0;

    // Get arrival and departure date
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');
    const arrivalDate = new Date(arrivalInput.value);
    const departureDate = new Date(departureInput.value);

    // Calculate number of days if both arrival and departure is selected
    let numberOfDays = 1; // Default to 1 day if no dates selected
    if (arrivalInput.value && departureInput.value && departureDate > arrivalDate) {
        const timeDifference = Math.abs(departureDate - arrivalDate);
        numberOfDays = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1; // Convert to number of days
    }

    // Adjust if arrival and departure are on the same day (1 day by default)
    if (arrivalDate.toDateString() === departureDate.toDateString()) {
        numberOfDays = 1;
    }

    // Room price multiplied by number of days
    totalCost += roomPrice * numberOfDays;

    // Add eventual features
    const featureCheckboxes = document.querySelectorAll('input[name="features[]"]:checked');
    featureCheckboxes.forEach(checkbox => {
        totalCost += parseInt(checkbox.getAttribute('data-price')) || 0;
    });

    // Update total cost in the DOM
    document.getElementById('totalCost').textContent = totalCost;

    // Update hidden total cost element for backend
    document.getElementById('total_cost').value = totalCost;
}

// Ensure that the price is updated immediately when the user selects the dates
document.addEventListener('DOMContentLoaded', () => {
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');

    // Listen to changes in arrival date and departure date
    arrivalInput.addEventListener('change', updateTotal);
    departureInput.addEventListener('change', updateTotal);
});

