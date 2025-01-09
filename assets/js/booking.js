document.addEventListener('DOMContentLoaded', () => {
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');
    const roomSelect = document.getElementById('room');
    const totalCostDisplay = document.getElementById('totalCost');
    const totalCostInput = document.getElementById('total_cost');

    // Store discount information globally
    let discountInfo = null;

    // Helper function to normalize strings for comparison
    function normalizeString(str) {
        return str.trim().toLowerCase().replace(/\s+/g, ' ');
    }

    // Helper function to extract room name from option text
    function getRoomNameFromOption(optionElement) {
        // The format is "Room Name - $price"
        return optionElement.textContent.split('-')[0].trim();
    }

    // Helper function to get feature name from checkbox label
    function getFeatureNameFromCheckbox(checkbox) {
        const label = checkbox.closest('label');
        if (label) {
            // The format is "Feature Name - $price"
            // Get everything before the last occurrence of " - $"
            const lastDashIndex = label.textContent.lastIndexOf(' - $');
            if (lastDashIndex !== -1) {
                return label.textContent.substring(0, lastDashIndex).trim();
            }
        }
        return '';
    }

    // Fetch discount information on page load
    async function fetchDiscountInfo() {
        try {
            const response = await fetch('app/get_discount_info.php');
            discountInfo = await response.json();
        } catch (error) {
            console.error('Error fetching discount info:', error);
        }
    }

    // Check if discount should be applied based on conditions
    function shouldApplyDiscount(numberOfDays, selectedRoomName) {
        if (!discountInfo) return false;

        const meetsMinDays = numberOfDays >= discountInfo.min_days_for_discount;
        const normalizedRoomName = normalizeString(selectedRoomName);
        const isEligibleRoom = discountInfo.discount_rooms.some(room => 
            normalizeString(room) === normalizedRoomName
        );
        return meetsMinDays && isEligibleRoom;
    }

    // Function to update total cost of booking including discount logic
    function updateTotal() {
        let totalCost = 0;

        // Get room price and name
        const selectedOption = roomSelect.options[roomSelect.selectedIndex];
        const roomPrice = parseInt(selectedOption.getAttribute('data-price')) || 0;
        const selectedRoomName = getRoomNameFromOption(selectedOption);

        // Calculate number of days
        const arrivalDate = arrivalInput._flatpickr ? arrivalInput._flatpickr.selectedDates[0] : null;
        const departureDate = departureInput._flatpickr ? departureInput._flatpickr.selectedDates[0] : null;

        let numberOfDays = 0;
        if (arrivalDate && departureDate) {
            const timeDifference = departureDate - arrivalDate;
            numberOfDays = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)) + 1;
            totalCost += roomPrice * numberOfDays;
        }

        // Handle features and discounts
        const featureCheckboxes = document.querySelectorAll('input[name="features[]"]:checked');
        featureCheckboxes.forEach(checkbox => {
            const featureName = getFeatureNameFromCheckbox(checkbox);
            const featurePrice = parseInt(checkbox.getAttribute('data-price')) || 0;
            
            const shouldDiscount = shouldApplyDiscount(numberOfDays, selectedRoomName);
            const isDiscountFeature = normalizeString(featureName) === 
                                    normalizeString(discountInfo?.discount_feature || '');

            // Check if this feature should be free according to discount rules
            if (shouldDiscount && isDiscountFeature) {
                // Feature is free - don't add the price
            } else {
                totalCost += featurePrice;
            }
        });

        // Update total cost in DOM and server-side
        totalCostDisplay.textContent = totalCost;
        totalCostInput.value = totalCost;
    }

    // Function to get unavailable dates for each room
    function getUnavailableDatesForRoom(roomId) {
        return fetch('app/get_bookings.php')
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
            // Initialize flatpickr calendar and disable already booked dates for each room
            const arrivalCalendar = flatpickr(arrivalInput, {
                dateFormat: "Y-m-d",
                onReady: () => arrivalInput.classList.remove('hidden-calendar'),
                minDate: "2025-01-01",
                maxDate: "2025-01-31",
                weekNumbers: true, // Add number of week to the calendar
                locale: { firstDayOfWeek: 1 }, // Set Monday as the first day of the week
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
                locale: { firstDayOfWeek: 1 }, // Set Monday as the first day of the week
                disable: unavailableDates, 
                onChange: updateTotal
            });
        });
    }

    // Function to ensure arrival date is set before departure date
    function syncDepartureMinDate() {
        const selectedArrival = arrivalInput._flatpickr.selectedDates[0];
        if (selectedArrival) {
            departureInput._flatpickr.set("minDate", selectedArrival);
        }
    }

    // Event listeners
    roomSelect.addEventListener('change', () => {
        updateCalendar();  // Update calendar when room changes
        updateTotal();     // Update total cost on room change
    });

    const featureCheckboxes = document.querySelectorAll('input[name="features[]"]');
    featureCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotal);  // Update total cost if features are added
    });

    // Initialize everything
    fetchDiscountInfo().then(() => {
    updateCalendar();
    updateTotal();
    });
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