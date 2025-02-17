<?php
// Include the database connection
require 'connection.php';

// Fetch inspectors from the database
$inspectors = [];
$result = $con->query("SELECT inspectorname FROM inspector");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inspectors[] = $row['inspectorname'];
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $companyName = $_POST['companyName'];
    $contactNo = $_POST['contactNo'];
    $email = $_POST['email'];
    $product = $_POST['product'];
    $location = $_POST['location_link']; // Location in the format "lat,lng"
    $inspector = $_POST['inspector_assigned'];

    $sql = "INSERT INTO inspection_details (company_name, contact_no, email, product, location_link, inspector_assigned) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssssss", $companyName, $contactNo, $email, $product, $location, $inspector);

    if ($stmt->execute()) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Inspection Form</title>
    <link rel="stylesheet" href="auditformstyle.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>
<body>
    <form id="inspectionForm" action="inspection.php" method="POST">
        <label for="companyName">Company Name:</label>
        <input type="text" id="companyName" name="companyName" required><br><br>

        <label for="contactNo">Contact Number:</label>
        <input type="tel" id="contactNo" name="contactNo" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="product">Product:</label>
        <input type="text" id="product" name="product" required><br><br>

        <label for="location">Location:</label>
        <div id="map"></div>
        <input type="hidden" id="location" name="location" required>
        <button type="button" id="getLocation">Get My Location</button><br><br>

        <label for="inspector">Inspector Assigned:</label>
        <select id="inspector" name="inspector" required>
            <?php foreach ($inspectors as $inspector): ?>
                <option value="<?php echo $inspector; ?>"><?php echo $inspector; ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <button type="submit">Save Details</button>
    </form>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([51.505, -0.09], 13);

        // Add a tile layer to the map
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Function to get the client's current location
        document.getElementById('getLocation').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;

                    // Center the map on the client's location
                    map.setView([lat, lng], 16);

                    // Add a marker at the client's location
                    L.marker([lat, lng]).addTo(map)
                        .bindPopup('Your Location')
                        .openPopup();

                    // Save the location to a hidden input field
                    document.getElementById('location').value = lat + ',' + lng;
                }, function(error) {
                    console.error('Error getting location:', error);
                    alert('Error getting location. Please enable location services.');
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        });
    </script>
</body>
</html>