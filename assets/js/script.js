// Function to autoscroll to booking form when book button is pressed
function scrollToBooking() {
    const bookingSection = document.getElementById("bookingTitle");
    bookingSection.scrollIntoView({ behavior: "smooth" });
}

// Function to let the hero slogan text slide in/out
function handleScroll() {
    const heroText = document.querySelector('.hero h1');
    const scrollPosition = window.scrollY;

    // If scrollposition is greater than 100px, let the text slide
    if (scrollPosition > 100) {
        heroText.classList.add('slide-left');
    } else {
        heroText.classList.remove('slide-left');
    }
}

// Scroll eventlistener
window.addEventListener('scroll', handleScroll);