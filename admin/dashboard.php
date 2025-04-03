<?php
session_start();
require_once '../config/database.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Get total inspectors count
$inspectors_sql = "SELECT COUNT(*) as total FROM users WHERE role = 'inspector'";
$inspectors_result = $conn->query($inspectors_sql);
$inspectors_count = $inspectors_result->fetch_assoc()['total'];

// Get total inspections count
$inspections_sql = "SELECT COUNT(*) as total FROM inspections";
$inspections_result = $conn->query($inspections_sql);
$inspections_count = $inspections_result->fetch_assoc()['total'];

// Get recent inspections
$recent_sql = "SELECT i.*, u.name as inspector_name 
               FROM inspections i 
               LEFT JOIN users u ON i.assigned_to = u.id 
               ORDER BY i.created_at DESC LIMIT 5";
$recent_result = $conn->query($recent_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Inspection App</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_inspectors.php">Inspectors</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_inspections.php">Inspections</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../auth/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav> -->

    <div class="container mt-4">
        <h1>Admin Dashboard</h1>
        
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Inspectors</h5>
                        <p class="card-text display-4"><?php echo $inspectors_count; ?></p>
                        <a href="manage_inspectors.php" class="btn btn-light">Manage Inspectors</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Total Inspections</h5>
                        <p class="card-text display-4"><?php echo $inspections_count; ?></p>
                        <a href="manage_inspections.php" class="btn btn-light">Manage Inspections</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Quick Actions</h5>
                        <a href="manage_inspectors.php?action=add" class="btn btn-light mb-2 btn-block">Add New Inspector</a>
                        <a href="add_inspection.php?action=add" class="btn btn-light btn-block">Create New Inspection</a>
                        <a href="track_inspectors.php?action=add" class="btn btn-light btn-block">Track Inspector</a>
                        <a href="manage_template.php?action=add" class="btn btn-light btn-block">Create Template</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Recent Inspections
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Inspector</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($inspection = $recent_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inspection['title']); ?></td>
                                    <td><?php echo htmlspecialchars($inspection['inspector_name'] ?? 'Unassigned'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $inspection['status'] == 'completed' ? 'success' : 
                                                ($inspection['status'] == 'in_progress' ? 'warning' : 'secondary');
                                        ?>">
                                            <?php echo ucfirst($inspection['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($inspection['created_at'])); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>