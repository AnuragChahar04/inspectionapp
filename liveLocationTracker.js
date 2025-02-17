document.addEventListener('DOMContentLoaded', function() {
    if (navigator.geolocation) {
        // Function to handle location updates
        function updateLocation(position) {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            // Update the HTML with the current coordinates
            document.getElementById('latitude').textContent = latitude.toFixed(6);
            document.getElementById('longitude').textContent = longitude.toFixed(6);

            // Send the coordinates to the server if needed
            sendLocationToServer(latitude, longitude);
        }

        // Function to handle errors
        function handleError(error) {
            console.error('Error getting location:', error);
            document.getElementById('latitude').textContent = 'Error';
            document.getElementById('longitude').textContent = 'Error';
        }

        // Function to send location to the server
        function sendLocationToServer(latitude, longitude) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'save_location.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(`lat=${latitude}&lon=${longitude}`);
        }

        // Start watching the user's location
        navigator.geolocation.watchPosition(updateLocation, handleError, {
            enableHighAccuracy: true,
            maximumAge: 10000,
            timeout: 5000
        });
    } else {
        alert("Geolocation is not supported by this browser.");
    }
});
