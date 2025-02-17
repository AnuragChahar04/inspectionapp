<?php
// Include the database connection
require 'connection.php';

// Get the unique identifier from the query parameter
$uniqueId = $_GET['uid'];

// Fetch the client's IP address
$clientIp = $_SERVER['REMOTE_ADDR'];

// Use an IP geolocation API to fetch the client's location
$apiUrl = "http://ipinfo.io/{$clientIp}/json";

$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// Extract latitude and longitude
$location = [
    'lat' => $data['loc'] ? explode(',', $data['loc'])[0] : 51.505,
    'lng' => $data['loc'] ? explode(',', $data['loc'])[1] : -0.09
];

// Return the location as JSON
header('Content-Type: application/json');
echo json_encode($location);

$conn->close();
?>