<?php

require_once __DIR__ . '/app/autoload.php';
require_once __DIR__ . '/views/header.php'; ?>

<main>
    <section class="our-rooms">
        <h2>Our Rooms</h2>
        <div class="room-cards">
            <div class="room-card" id="room-1">
                <h3>Woodland Retreat</h3>
                <img src="assets/images/room-woodland-retreat.webp" alt="Overview of the budget room">
                <ul>
                    <li>Surrounded by serene forest views for ultimate relaxation.</li>
                    <li>Private balcony with cozy seating for stargazing.</li>
                    <li>Complimentary access to hiking trails and guided tours.</li>
                </ul>
            </div>
            <div class="room-card" id="room-2">
                <h3>Forest Haven</h3>
                <img src="assets/images/room-forest-haven.webp" alt="Overview of the standard room">
                <ul>
                    <li>A harmonious blend of comfort and style with forest-inspired decor.</li>
                    <li>Includes a spacious queen-sized bed and a cozy seating nook.</li>
                    <li>Perfect for couples or small families looking for a balance of luxury and affordability.</li>
                </ul>
            </div>
            <div class="room-card" id="room-3">
                <h3>Canopy Grand Suite</h3>
                <img src="assets/images/room-canopy-grand-suite.webp" alt="Overview of the luxury room">
                <ul>
                    <li>Luxurious suite with high ceilings and panoramic forest views.</li>
                    <li>Includes a king-sized bed, lounge area, and private jacuzzi.</li>
                    <li>Designed for guests seeking elegance and adventure.</li>
                </ul>
            </div>
        </div>
    </section>

    <?php
    require_once __DIR__ . '/views/booking_form.php'; ?>

    <section class="our-features">
        <h2>Our features</h2>
        <div class="features">
            <!-- Feature 1 -->
            <article class="feature">
                <div class="feature-image">
                    <img src="assets/images/forest-haven-spa.webp" alt="Picture of spa outside the hotel">
                </div>
                <div class="feature-text">
                    <h3>Find Your Bliss at Foresthaven Spa</h3>
                    <p>Rejuvenate in our tranquil spa, surrounded by nature's beauty. Indulge in organic treatments, soothing massages, and serene hot springs for the ultimate escape.</p>
                    <p><span class="accent">Relax. Refresh. Reconnect.</span></p>
                </div>
            </article>

            <!-- Feature 2 -->
            <article class="feature reverse">
                <div class="feature-text">
                    <h3>Savor the Forest at our Breakfast Buffet</h3>
                    <p>Start your day with a feast inspired by nature. Enjoy fresh, locally-sourced ingredients, hearty baked goods, and vibrant seasonal fruits in a serene, forest-themed setting.</p>
                    <p><span class="accent">Fuel. Delight. Explore.</span></p>
                </div>
                <div class="feature-image">
                    <img src="assets/images/forest-haven-breakfast.webp" alt="Picture of the breakfast buffet at the restaurant of the hotel">
                </div>
            </article>

            <!-- Feature 3 -->
            <article class="feature">
                <div class="feature-image">
                    <img src="assets/images/canopy-course.webp" alt="Picture of the tree canopy adventure course">
                </div>
                <div class="feature-text">
                    <h3>Elevate Your Stay with the Canopy Adventure</h3>
                    <p>Experience the thrill of exploring treetops on our breathtaking canopy course. Glide through zip lines, navigate rope bridges, and take in stunning views of Lindenwood Isle's lush forests.</p>
                    <p><span class="accent">Climb. Soar. Discover.</span></p>
                </div>
            </article>
        </div>
    </section>



</main>

<?php
require_once __DIR__ . '/views/footer.php'; ?>