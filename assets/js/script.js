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

// Observer that triggers everytime feature gets visible
const observer = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
        // If visible feature, add visible class to start animation
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            observer.unobserve(entry.target);  // Stop observe after visible feature
        }
    });
}, {
    threshold: 0.5  // Activate when 50% of section is visible
});

// Wait for site to load
document.querySelectorAll('.feature').forEach(feature => {
    observer.observe(feature);
});