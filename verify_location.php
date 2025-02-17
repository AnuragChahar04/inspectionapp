<?php
require "Connection.php";
session_start();

// Fetch user's coordinates sent via POST
$userLat = $_POST['lat'];
$userLon = $_POST['lon'];

// Fetch allowed coordinates from the database
$query = "SELECT allowed_latitude, allowed_longitude FROM product WHERE inspector_name = '$_SESSION[inspectorloginid]'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);

// Define allowed radius (in kilometers)
$allowedRadius = 0.010; // For example, 0.010 km radius

// Calculate the distance between user's location and allowed location
function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371)
{
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
}

$distance = haversineGreatCircleDistance($userLat, $userLon, $row['allowed_latitude'], $row['allowed_longitude']);

// If the user is outside the allowed radius, send an email
if ($distance > $allowedRadius) {
    // Send email to admin
    $adminEmail = "omk5982@gmail.com"; // Replace with your admin's email
    $subject = "Alert: Unauthorized Location Access Detected";
    $message = "User: $_SESSION[inspectorloginid] tried to access the page from unauthorized coordinates. Current Location: Latitude - $userLat, Longitude - $userLon.";
    $headers = "From: no-reply@example.com";

    mail($adminEmail, $subject, $message, $headers);

    echo "You are outside the allowed location. Admin has been notified.";
} else {     
    echo "You are within the allowed location.";
}