<?php
session_start();
require_once '../config/database.php';
// require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inspector') {
    header('Location: ../auth/login.php');
    exit();
}

$inspector_id = $_SESSION['user_id'];

// Build the query with filters
$where_conditions = ["i.assigned_to = $inspector_id"];
$sql = "SELECT i.*, 
        CASE 
            WHEN i.status = 'completed' AND i.report_pdf IS NOT NULL THEN i.report_pdf 
            ELSE NULL 
        END as report_pdf 
        FROM inspections i";

if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $where_conditions[] = "i.status = '$status'";
}

if (!empty($_GET['state'])) {
    $state = $conn->real_escape_string($_GET['state']);
    $where_conditions[] = "i.state LIKE '%$state%'";
}

$sql .= " WHERE " . implode(" AND ", $where_conditions);
$sql .= " ORDER BY i.created_at DESC";
$result = $conn->query($sql);

// Get unique states
$states = $conn->query("SELECT DISTINCT state FROM inspections WHERE assigned_to = $inspector_id ORDER BY state");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inspector Dashboard - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .list-group-item {
            cursor: pointer;
        }
        .list-group-item:hover {
            background-color: #f8f9fa;
        }
        .badge {
            font-size: 0.9em;
        }
        .action-buttons {
            display: none;
        }
        .list-group-item:hover .action-buttons {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>My Inspections</h2>
            <div>
                <!-- <span class="mr-3">Welcome</span> -->
                <a href="../auth/logout.php" class="btn btn-secondary">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>State</label>
                            <select name="state" class="form-control">
                                <option value="">All States</option>
                                <?php while($state = $states->fetch_assoc()): ?>
                                    <option value="<?php echo htmlspecialchars($state['state']); ?>"
                                            <?php echo ($_GET['state'] ?? '') == $state['state'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($state['state']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="pending" <?php echo ($_GET['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="in_progress" <?php echo ($_GET['status'] ?? '') == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                <option value="completed" <?php echo ($_GET['status'] ?? '') == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Inspections List -->
        <div class="list-group">
            <?php if($result->num_rows === 0): ?>
                <div class="alert alert-info">No inspections found.</div>
            <?php endif; ?>
            
            <?php while($inspection = $result->fetch_assoc()): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1"><?php echo htmlspecialchars($inspection['title']); ?></h5>
                            <p class="mb-1">PO: <?php echo htmlspecialchars($inspection['po_number']); ?></p>
                            <small><?php echo htmlspecialchars($inspection['company_name']); ?></small>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-<?php 
                                echo $inspection['status'] == 'completed' ? 'success' : 
                                    ($inspection['status'] == 'in_progress' ? 'warning' : 'secondary');
                            ?>">
                                <?php echo ucfirst($inspection['status']); ?>
                            </span>
                            <div class="mt-2 action-buttons">
                                <?php if($inspection['status'] == 'completed' && !empty($inspection['report_pdf'])): ?>
                                    <a href="../download_report.php?id=<?php echo $inspection['id']; ?>" 
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i> Download Report
                                    </a>
                                <?php else: ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-primary view-inspection" 
                                            data-inspection='<?php echo json_encode($inspection); ?>'>
                                        <i class="fas fa-eye"></i> View Details
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Inspection Details Modal -->
    <div class="modal fade" id="inspectionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inspection Details</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>PO Number:</strong> <span id="modal-po"></span></p>
                            <p><strong>Title:</strong> <span id="modal-title"></span></p>
                            <p><strong>Company:</strong> <span id="modal-company"></span></p>
                            <p><strong>Contact Person:</strong> <span id="modal-contact"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong> <span id="modal-address"></span></p>
                            <p><strong>State:</strong> <span id="modal-state"></span></p>
                            <p><strong>Contact Number:</strong> <span id="modal-phone"></span></p>
                            <p><strong>Email:</strong> <span id="modal-email"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="startInspection" data-id="">
                        <i class="fas fa-play"></i> Start Inspection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    $(document).ready(function() {
        $('.view-inspection').click(function() {
            var inspection = $(this).data('inspection');
            
            $('#modal-po').text(inspection.po_number);
            $('#modal-title').text(inspection.title);
            $('#modal-company').text(inspection.company_name);
            $('#modal-contact').text(inspection.contact_person);
            $('#modal-address').text(inspection.address);
            $('#modal-state').text(inspection.state);
            $('#modal-phone').text(inspection.contact_number);
            $('#modal-email').text(inspection.email);
            $('#startInspection').data('id', inspection.id);
            
            $('#inspectionModal').modal('show');
        });

        $('#startInspection').click(function() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var inspectionId = $('#startInspection').data('id');
                    $.post('update_location.php', {
                        inspection_id: inspectionId,
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    }).done(function() {
                        window.location.href = 'fill_report.php?id=' + inspectionId;
                    }).fail(function() {
                        alert('Error updating location. Please try again.');
                    });
                }, function() {
                    alert('Please enable location services to start the inspection.');
                });
            } else {
                alert('Location services are not supported by your browser.');
            }
        });
    });
    </script>
</body>
</html>