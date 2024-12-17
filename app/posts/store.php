<?php

require_once __DIR__ . '/../autoload.php';

if (isset($_POST['room'], $_POST['arrival_date'], $_POST['departure_date'], $_POST['guest_name'], $_POST['transfer_code'])) {
    $features = isset($_POST['features']) ? $_POST['features'] : [];

    $room = filter_var(trim($_POST['room']), FILTER_SANITIZE_NUMBER_INT);
    $arrivalDate = htmlspecialchars(trim($_POST['arrival_date']));
    $departureDate = htmlspecialchars(trim($_POST['departure_date']));
    $guestName = htmlspecialchars(trim($_POST['guest_name']));
    $transferCode = htmlspecialchars(trim($_POST['transfer_code']));
}
