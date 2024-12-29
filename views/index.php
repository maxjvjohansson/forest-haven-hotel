<?php

require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/header.php'; ?>

<main>
    <section class="our-rooms">
        <h2>Our Rooms</h2>
        <div class="room-cards">
            <div class="room-card">
                <h3>Woodland Retreat</h3>
                <img src="/assets/images/room-woodland-retreat.png" alt="Room 1">
                <ul>
                    <li>Surrounded by serene forest views for ultimate relaxation.</li>
                    <li>Private balcony with cozy seating for stargazing.</li>
                    <li>Complimentary access to hiking trails and guided tours.</li>
                </ul>
            </div>
            <div class="room-card">
                <h3>Forest Haven</h3>
                <img src="/assets/images/room-forest-haven.png" alt="Room 2">
                <ul>
                    <li>A harmonious blend of comfort and style with forest-inspired decor.</li>
                    <li>Includes a spacious queen-sized bed and a cozy seating nook.</li>
                    <li>Perfect for couples or small families looking for a balance of luxury and affordability.</li>
                </ul>
            </div>
            <div class="room-card">
                <h3>Canopy Grand Suite</h3>
                <img src="/assets/images/room-canopy-grand-suite.png" alt="Room 3">
                <ul>
                    <li>Luxurious suite with high ceilings and panoramic forest views.</li>
                    <li>Includes a king-sized bed, lounge area, and private jacuzzi.</li>
                    <li>Designed for guests seeking elegance and adventure.</li>
                </ul>
            </div>
        </div>
    </section>

    <?php
    require_once __DIR__ . '/booking_form.php'; ?>

    <section class="our-features">
        <h2>Our features</h2>
    </section>

</main>

<?php
require_once __DIR__ . '/footer.php'; ?>