document.addEventListener('DOMContentLoaded', () => {
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');
    const roomSelect = document.getElementById('room');
    const totalCostDisplay = document.getElementById('totalCost');
    const totalCostInput = document.getElementById('total_cost');

    // Function to update totalcost of a booking
    function updateTotal() {
        let totalCost = 0;

        // Get correct price from room
        const roomPrice = parseInt(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price')) || 0;

        // Make sure flatpickr is initiated before proceeding
        const arrivalDate = arrivalInput._flatpickr ? arrivalInput._flatpickr.selectedDates[0] : null;
        const departureDate = departureInput._flatpickr ? departureInput._flatpickr.selectedDates[0] : null;

        if (arrivalDate && departureDate) {
            const timeDifference = departureDate - arrivalDate;
            const numberOfDays = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;
            totalCost += roomPrice * numberOfDays;
        }

        // Add fixed feature price for each booking
        const featureCheckboxes = document.querySelectorAll('input[name="features[]"]:checked');
        featureCheckboxes.forEach(checkbox => {
            totalCost += parseInt(checkbox.getAttribute('data-price')) || 0;
        });

        // Update totalcost in DOM and serverside
        totalCostDisplay.textContent = totalCost;
        totalCostInput.value = totalCost;
    }

    // Function to get unavailable dates for each room
    function getUnavailableDatesForRoom(roomId) {
        return fetch('../app/get_bookings.php')
            .then(response => response.json())
            .then(bookingsData => {
                // Filter booked dates based on room id
                const roomBookings = bookingsData.filter(booking => booking.room_id === parseInt(roomId));
                
                // Create an array containing booked dates/rooms
                return roomBookings.map(booking => ({
                    from: booking.from,
                    to: booking.to
                }));
            })
            .catch(error => {
                console.error('Error fetching bookings:', error);
                return []; 
            });
    }

    // Function to update calendar when room changes on input
    function updateCalendar() {
        const roomId = roomSelect.value;
        
        getUnavailableDatesForRoom(roomId).then(unavailableDates => {
            // Initiate flatpickr calendar and disable already booked dates for each room
            const arrivalCalendar = flatpickr(arrivalInput, {
                dateFormat: "Y-m-d",
                onReady: () => arrivalInput.classList.remove('hidden-calendar'),
                minDate: "2025-01-01",
                maxDate: "2025-01-31",
                weekNumbers: true, // Add number of week to the calendar
                disable: unavailableDates,
                onChange: () => {
                    syncDepartureMinDate();
                    updateTotal();
                }
            });

            const departureCalendar = flatpickr(departureInput, {
                dateFormat: "Y-m-d",
                onReady: () => departureInput.classList.remove('hidden-calendar'),
                minDate: "2025-01-01",
                maxDate: "2025-01-31",
                weekNumbers: true, // Add number of week to the calendars
                disable: unavailableDates, 
                onChange: updateTotal
            });
        });
    }

    // Function to check that arrival date needs to be set before departure date
    function syncDepartureMinDate() {
        const selectedArrival = arrivalInput._flatpickr.selectedDates[0];
        if (selectedArrival) {
            departureInput._flatpickr.set("minDate", selectedArrival);
        }
    }

    roomSelect.addEventListener('change', () => {
        updateCalendar();  // Update calendar when room changes
        updateTotal();  // Update totalcost on room change
    });

    const featureCheckboxes = document.querySelectorAll('input[name="features[]"]');
    featureCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotal);  // Update totalcost if features are added
    });

    // Initiate calendar on refresh
    updateCalendar();

    // Update totalcost on refresh
    updateTotal();
});

// Function to update the room preview based on selected option
document.addEventListener("DOMContentLoaded", function () {
    const roomSelect = document.getElementById("room");
    const roomCards = document.querySelectorAll(".room-card");

    function updateRoomPreview() {
        // Get the selected room ID
        const selectedRoomId = roomSelect.value;

        // Loop through all room cards and show/hide them
        roomCards.forEach(card => {
            if (card.id === `room-${selectedRoomId}`) {
                card.classList.remove("hidden"); // Show the matching card
            } else {
                card.classList.add("hidden"); // Hide non-matching cards
            }
        });
    }

    // Add event listener to the room select dropdown
    roomSelect.addEventListener("change", updateRoomPreview);

    // Initialize preview on page load
    updateRoomPreview();
});

// Function to toggle visibility of info popup
function toggleInfoPopup(iconId, popupId) {
    const icon = document.getElementById(iconId);
    const popup = document.getElementById(popupId);

    icon.addEventListener('click', (event) => {
        event.stopPropagation();
        popup.classList.toggle('visible');
    });

    // Close the popup if clicking outside
    document.addEventListener('click', (event) => {
        if (!popup.contains(event.target) && event.target !== icon) {
            popup.classList.remove('visible');
        }
    });
}

// Initialize the popups
toggleInfoPopup('roomInfoIcon', 'roomInfoPopup');
toggleInfoPopup('featureInfoIcon', 'featureInfoPopup');