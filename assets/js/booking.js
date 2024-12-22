document.addEventListener('DOMContentLoaded', () => {
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');
    const roomSelect = document.getElementById('room');
    const totalCostDisplay = document.getElementById('totalCost');
    const totalCostInput = document.getElementById('total_cost');

    // Initiate flatpickr for datefields
    const arrivalCalendar = flatpickr(arrivalInput, {
        dateFormat: "Y-m-d",
        minDate: "2025-01-01",
        maxDate: "2025-01-31",
        onChange: () => {
            syncDepartureMinDate();
            updateTotal();
        },
    });

    const departureCalendar = flatpickr(departureInput, {
        dateFormat: "Y-m-d",
        minDate: "2025-01-01",
        maxDate: "2025-01-31",
        onChange: updateTotal,
    });

    // Function to 
    function syncDepartureMinDate() {
        const selectedArrival = arrivalInput._flatpickr.selectedDates[0];
        if (selectedArrival) {
            departureInput._flatpickr.set("minDate", selectedArrival);
        }
    }

    // Function to calculate total booking cost
    function updateTotal() {
        let totalCost = 0;

        // Get price from room
        const roomPrice = parseInt(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price')) || 0;

        // Get dates from flatpickr calendar
        const arrivalDate = arrivalInput._flatpickr.selectedDates[0];
        const departureDate = departureInput._flatpickr.selectedDates[0];

        if (arrivalDate && departureDate) {
            const timeDifference = departureDate - arrivalDate;
            const numberOfDays = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;

            totalCost += roomPrice * numberOfDays;
        }

        // Add fixed feature price per booking 
        const featureCheckboxes = document.querySelectorAll('input[name="features[]"]:checked');
        featureCheckboxes.forEach(checkbox => {
            totalCost += parseInt(checkbox.getAttribute('data-price')) || 0;
        });

        // Update in DOM and serverside
        totalCostDisplay.textContent = totalCost;
        totalCostInput.value = totalCost;
    }

    // Eventlistener to change/display cost depending on user choices
    roomSelect.addEventListener('change', updateTotal);

    const featureCheckboxes = document.querySelectorAll('input[name="features[]"]');
    featureCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotal);
    });

    updateTotal();
});
