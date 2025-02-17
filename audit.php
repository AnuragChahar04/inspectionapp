<?php
require 'Connection.php';

// Fetch all inspections
$query = "SELECT * FROM inspections";
$result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspection List</title>
    <link rel="stylesheet" href="audit.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <?php require 'sidebar.php'; ?>
</head>
<body>

    <div class="container">
        <h2>Inspection List</h2>
        <button classs="add-inspection-btn" onclick="window.location.href='addnewaudit.php'">Add New Inspection</button>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Company Name</th>
                    <th>Product</th>
                    <th>Inspector</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr onclick="openModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['company_name']; ?></td>
                        <td><?php echo $row['product']; ?></td>
                        <td><?php echo $row['inspector']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><button onclick="openModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">View</button></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h3>Inspection Details</h3>
            <p><b>Company Name:</b> <span id="modalCompany"></span></p>
            <p><b>Contact No.:</b> <span id="modalContact"></span></p>
            <p><b>Email:</b> <span id="modalEmail"></span></p>
            <p><b>Product:</b> <span id="modalProduct"></span></p>
            <p><b>Inspector:</b> <span id="modalInspector"></span></p>
            <p><b>Status:</b> <span id="modalStatus"></span></p>
            <h4>Inspection Location</h4>
            <div id="map"></div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let map, marker;

        function openModal(data) {
            document.getElementById("modalCompany").innerText = data.company_name;
            document.getElementById("modalContact").innerText = data.contact_no;
            document.getElementById("modalEmail").innerText = data.email;
            document.getElementById("modalProduct").innerText = data.product;
            document.getElementById("modalInspector").innerText = data.inspector;
            document.getElementById("modalStatus").innerText = data.status;

            document.getElementById("modal").style.display = "block";

            if (!map) {
                map = L.map('map').setView([data.client_lat, data.client_lng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                marker = L.marker([data.client_lat, data.client_lng]).addTo(map);
            } else {
                map.setView([data.client_lat, data.client_lng], 13);
                marker.setLatLng([data.client_lat, data.client_lng]);
            }
        }

        function closeModal() {
            document.getElementById("modal").style.display = "none";
        }
    </script>

</body>
</html>
