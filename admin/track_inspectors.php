<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Get active inspectors (those who have started inspections today)
$sql = "SELECT DISTINCT u.id, u.name, i.title as inspection_title,
        il.latitude, il.longitude, il.created_at
        FROM users u
        JOIN inspector_locations il ON u.id = il.inspector_id
        JOIN inspections i ON il.inspection_id = i.id
        WHERE DATE(il.created_at) = CURDATE()
        AND i.status = 'in_progress'
        ORDER BY il.created_at DESC";
$result = $conn->query($sql);

// Add error handling
if (!$result) {
    die("Error fetching locations: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track Inspectors - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <style>
        #map {
            height: 600px;
            width: 100%;
        }
        .inspector-list {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-3">
                <h4>Active Inspectors</h4>
                <div class="list-group inspector-list">
                    <?php while($inspector = $result->fetch_assoc()): ?>
                        <a href="#" class="list-group-item list-group-item-action locate-inspector"
                           data-lat="<?php echo $inspector['latitude']; ?>"
                           data-lng="<?php echo $inspector['longitude']; ?>"
                           data-name="<?php echo htmlspecialchars($inspector['name']); ?>"
                           data-inspection="<?php echo htmlspecialchars($inspector['inspection_title']); ?>">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1"><?php echo htmlspecialchars($inspector['name']); ?></h6>
                                <small><?php echo date('H:i', strtotime($inspector['created_at'])); ?></small>
                            </div>
                            <small class="text-muted">
                                <?php echo htmlspecialchars($inspector['inspection_title']); ?>
                            </small>
                        </a>
                    <?php endwhile; ?>
                </div>
                <div class="mt-3">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize map
            var map = L.map('map').setView([0, 0], 2);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            var markers = {};

            // Add markers for each inspector
            $('.locate-inspector').each(function() {
                var lat = $(this).data('lat');
                var lng = $(this).data('lng');
                var name = $(this).data('name');
                var inspection = $(this).data('inspection');

                var marker = L.marker([lat, lng])
                    .bindPopup(name + '<br><small>' + inspection + '</small>')
                    .addTo(map);
                
                markers[name] = marker;
            });

            // Center map on first inspector if exists
            if ($('.locate-inspector').length > 0) {
                var first = $('.locate-inspector').first();
                map.setView([first.data('lat'), first.data('lng')], 13);
            }

            // Click handler for inspector list
            $('.locate-inspector').click(function(e) {
                e.preventDefault();
                var lat = $(this).data('lat');
                var lng = $(this).data('lng');
                var name = $(this).data('name');

                map.setView([lat, lng], 13);
                markers[name].openPopup();
            });

            // Auto-refresh every 5 minutes
            setInterval(function() {
                location.reload();
            }, 300000);
        });
    </script>
</body>
</html>