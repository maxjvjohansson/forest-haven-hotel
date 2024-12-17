function updateTotal() {
    let totalCost = 0;

    // Hämta det valda rummets pris
    const roomSelect = document.getElementById('room');
    const roomPrice = parseFloat(roomSelect.options[roomSelect.selectedIndex].getAttribute('data-price')) || 0;
    totalCost += roomPrice;

    // Hämta priser för alla valda tillval
    const featureCheckboxes = document.querySelectorAll('input[name="features[]"]:checked');
    featureCheckboxes.forEach(checkbox => {
        totalCost += parseFloat(checkbox.getAttribute('data-price')) || 0;
    });

    // Uppdatera det totala priset i DOM
    document.getElementById('totalCost').textContent = totalCost.toFixed(2);

    // Uppdatera det dolda inputfältet för servern
    document.getElementById('total_cost').value = totalCost.toFixed(2);
}

// Kör funktionen vid start för att visa korrekt pris initialt
document.addEventListener('DOMContentLoaded', updateTotal);

// Kör funktionen när formulärets element ändras
document.getElementById('bookingForm').addEventListener('change', updateTotal);