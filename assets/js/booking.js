document.addEventListener('DOMContentLoaded', () => {
    const arrivalInput = document.getElementById('arrival_date');
    const departureInput = document.getElementById('departure_date');
    const roomSelect = document.getElementById('room');

    // Initiera Flatpickr för datumfält
    const arrivalCalendar = flatpickr(arrivalInput, {
        dateFormat: "Y-m-d",
        minDate: "2025-01-01",
        maxDate: "2025-01-31",
        onChange: function(selectedDates) {
            if (selectedDates.length > 0) {
                departureInput._flatpickr.set("minDate", selectedDates[0]);
                updateTotal();
            }
        },
    });

    const departureCalendar = flatpickr(departureInput, {
        dateFormat: "Y-m-d",
        minDate: "2025-01-01",
        maxDate: "2025-01-31",
        onChange: function() {
            updateTotal();
        },
    });

    // Funktion för att uppdatera otillgängliga datum via AJAX
    function updateUnavailableDates() {
        const roomId = roomSelect.value;
    
        // Kontrollera att ett rum är valt
        if (!roomId) return;
    
        fetch(`/app/get_available_rooms.php?room_id=${roomId}`)
            .then(response => response.json())  // Förvänta oss ett JSON-svar
            .then(data => {
                if (data.error) {
                    throw new Error(data.error);
                }
    
                console.log('Unavailable dates:', data); // Logga otillgängliga datum för att debugga
    
                // Uppdatera Flatpickr med otillgängliga datum
                arrivalCalendar.set('disable', data);  // Disabla de otillgängliga datumen
                departureCalendar.set('disable', data);  // Samma här för avresedatum
            })
            .catch(error => {
                console.error('Error fetching unavailable dates:', error);
                alert('An error occurred while fetching unavailable dates. Please try again.');
            });
    }

    // Lyssna på ändringar i rumsvalet
    roomSelect.addEventListener('change', () => {
        updateUnavailableDates();
        updateTotal();
    });

    // Funktion för att beräkna total kostnad
    function updateTotal() {
        let totalCost = 0;

        // Hämta rumspris
        const roomPrice = parseInt(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price')) || 0;

        // Hämta valda datum från Flatpickr
        const arrivalDate = arrivalInput._flatpickr.selectedDates[0];
        const departureDate = departureInput._flatpickr.selectedDates[0];

        // Kontrollera om ankomstdatum är före avresedatum
        if (arrivalDate && departureDate && arrivalDate > departureDate) {
            alert('Arrival date cannot be after departure date.');
            arrivalInput._flatpickr.clear();
            departureInput._flatpickr.clear();
            return;
        }

        // Beräkna antal dagar
        let numberOfDays = 1; // Standardvärde
        if (arrivalDate && departureDate) {
            const timeDifference = Math.abs(departureDate - arrivalDate);
            numberOfDays = Math.ceil(timeDifference / (1000 * 60 * 60 * 24)); // Omvandla till dagar
        }

        // Lägg till rumspris multiplicerat med antal dagar
        totalCost += roomPrice * numberOfDays;

        // Lägg till pris för valda tillval
        const featureCheckboxes = document.querySelectorAll('input[name="features[]"]:checked');
        featureCheckboxes.forEach(checkbox => {
            totalCost += parseInt(checkbox.getAttribute('data-price')) || 0;
        });

        // Uppdatera totalpris i DOM
        document.getElementById('totalCost').textContent = totalCost;

        // Uppdatera dold totalprisinmatning för backend
        document.getElementById('total_cost').value = totalCost;
    }

    // Initiera feature-checkbox-händelser
    const featureCheckboxes = document.querySelectorAll('input[name="features[]"]');
    featureCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotal);
    });

    // Uppdatera totalpris och otillgängliga datum initialt
    updateUnavailableDates();
    updateTotal();
});
