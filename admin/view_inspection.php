<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: manage_inspections.php');
    exit();
}

$inspection_id = (int)$_GET['id'];

// Get inspection details with inspector name
$sql = "SELECT i.*, u.name as inspector_name 
        FROM inspections i 
        LEFT JOIN users u ON i.assigned_to = u.id 
        WHERE i.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $inspection_id);
$stmt->execute();
$inspection = $stmt->get_result()->fetch_assoc();

if (!$inspection) {
    header('Location: manage_inspections.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Inspection - <?php echo htmlspecialchars($inspection['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Inspection Details</h4>
                <div>
                    <a href="edit_inspection.php?id=<?php echo $inspection['id']; ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="manage_inspections.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5>Basic Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>PO Number</th>
                                <td><?php echo htmlspecialchars($inspection['po_number']); ?></td>
                            </tr>
                            <tr>
                                <th>Title</th>
                                <td><?php echo htmlspecialchars($inspection['title']); ?></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td><span class="badge badge-<?php 
                                    echo $inspection['status'] == 'completed' ? 'success' : 
                                        ($inspection['status'] == 'in_progress' ? 'warning' : 'secondary');
                                    ?>"><?php echo ucfirst($inspection['status']); ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td><?php echo date('M d, Y H:i:s', strtotime($inspection['created_at'])); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Company Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Company Name</th>
                                <td><?php echo htmlspecialchars($inspection['company_name']); ?></td>
                            </tr>
                            <tr>
                                <th>Contact Person</th>
                                <td><?php echo htmlspecialchars($inspection['contact_person']); ?></td>
                            </tr>
                            <tr>
                                <th>Contact Number</th>
                                <td><?php echo htmlspecialchars($inspection['contact_number']); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo htmlspecialchars($inspection['email']); ?></td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td><?php echo htmlspecialchars($inspection['address']); ?></td>
                            </tr>
                            <tr>
                                <th>State</th>
                                <td><?php echo htmlspecialchars($inspection['state']); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        function updateLocation(inspectionId) {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    $.ajax({
                        url: 'update_location.php',
                        type: 'POST',
                        data: {
                            inspection_id: inspectionId,
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        },
                        success: function(response) {
                            console.log('Location updated successfully');
                        },
                        error: function(xhr, status, error) {
                            console.error('Error updating location:', error);
                        }
                    });
                });
            }
        }

        // Initial location update when starting inspection
        updateLocation(<?php echo $inspection_id; ?>);

        // Update location every 5 minutes
        setInterval(function() {
            updateLocation(<?php echo $inspection_id; ?>);
        }, 300000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>