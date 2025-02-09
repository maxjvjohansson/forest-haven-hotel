<?php

require_once __DIR__ . '/../app/functions.php';

// Get actual number of stars for the hotel
$stars = getHotelStars($database);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forest Haven Hotel</title>

    <link rel="icon" href="assets/icons/logo-black.svg">

    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/main-content.css">
    <link rel="stylesheet" href="assets/css/booking.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>

<body>

    <header>
        <div class="menu-container">
            <a href="#" class="logo">
                <img src="assets/icons/logo-white.svg" alt="Forest Haven Logo" class="logo-img">
                <p>Forest Haven Hotel</p>
            </a>
            <nav>
                <ul class="menu-items">
                    <li class="book-now">
                        <a href="#bookingForm">Book Now</a>
                    </li>
                    <li class="about">
                        <a href="#">About Us</a>
                    </li>
                    <li class="activities">
                        <a href="#">Activities</a>
                    </li>
                    <li class="contact">
                        <a href="#">Contact</a>
                    </li>
                </ul>
            </nav>
        </div>

        <section class="hero">
            <div class="hero-container">
                <img src="assets/images/hero-forest-haven-hotel.webp">
            </div>

            <div class="hotel-stars">
                <?php for ($i = 0; $i < $stars; $i++): ?>
                    <img src="assets/icons/star.svg" alt="Star" class="star-icon">
                <?php endfor; ?>
            </div>

            <h1> Escape to the Forest Haven</h1>
            <button class="book-btn" onclick="scrollToBooking()">Book Now</button>
        </section>
    </header>