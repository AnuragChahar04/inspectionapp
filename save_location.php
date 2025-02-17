<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = isset($_POST['lat']) ? $_POST['lat'] : null;
    $longitude = isset($_POST['lon']) ? $_POST['lon'] : null;

    // Validate and sanitize input data
    if ($latitude && $longitude) {
        $latitude = filter_var($latitude, FILTER_VALIDATE_FLOAT);
        $longitude = filter_var($longitude, FILTER_VALIDATE_FLOAT);

        if ($latitude !== false && $longitude !== false) {
            // Here you can save the coordinates to a database or log file
            // Example: save to a file
            $file = 'locations.log';
            $data = "Latitude: $latitude, Longitude: $longitude\n";
            file_put_contents($file, $data, FILE_APPEND);

            // Respond with success
            echo "Location saved.";
        } else {
            echo "Invalid coordinates.";
        }
    } else {
        echo "Missing coordinates.";
    }
} else {
    echo "Invalid request.";
}
