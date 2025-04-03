<?php
session_start();
require_once '../config/database.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Get all inspectors for filter
$inspectors = $conn->query("SELECT id, name FROM users WHERE role = 'inspector' ORDER BY name");

// Build the query with filters
$where_conditions = [];
$sql = "SELECT i.*, u.name as inspector_name 
        FROM inspections i 
        LEFT JOIN users u ON i.assigned_to = u.id";

if (!empty($_GET['status'])) {
    $status = $conn->real_escape_string($_GET['status']);
    $where_conditions[] = "i.status = '$status'";
}

if (!empty($_GET['state'])) {
    $state = $conn->real_escape_string($_GET['state']);
    $where_conditions[] = "i.state LIKE '%$state%'";
}

if (!empty($_GET['inspector'])) {
    $inspector = (int)$_GET['inspector'];
    $where_conditions[] = "i.assigned_to = $inspector";
}

if (!empty($where_conditions)) {
    $sql .= " WHERE " . implode(" AND ", $where_conditions);
}

$sql .= " ORDER BY i.created_at DESC";
$result = $conn->query($sql);

// Get unique states for filter
$states = $conn->query("SELECT DISTINCT state FROM inspections ORDER BY state");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Inspections - Inspection App</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Inspections</h2>
            <a href="add_inspection.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Inspection
            </a>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row">
                    <div class="col-md-3">
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
                    <div class="col-md-3">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Inspector</label>
                            <select name="inspector" class="form-control">
                                <option value="">All Inspectors</option>
                                <?php 
                                $inspectors->data_seek(0);
                                while($inspector = $inspectors->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $inspector['id']; ?>"
                                            <?php echo ($_GET['inspector'] ?? '') == $inspector['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($inspector['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                <a href="manage_inspections.php" class="btn btn-secondary">
                                    <i class="fas fa-undo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Title</th>
                                <th>Company</th>
                                <th>Inspector</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($inspection = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($inspection['po_number']); ?></td>
                                    <td><?php echo htmlspecialchars($inspection['title']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($inspection['company_name']); ?>
                                        <small class="d-block text-muted">
                                            <?php echo htmlspecialchars($inspection['contact_person']); ?>
                                        </small>
                                    </td>
                                    <td><?php echo htmlspecialchars($inspection['inspector_name']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo $inspection['status'] == 'completed' ? 'success' : 
                                                ($inspection['status'] == 'in_progress' ? 'warning' : 'secondary');
                                        ?>">
                                            <?php echo ucfirst($inspection['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($inspection['created_at'])); ?></td>
                                    <td>
                                        <a href="view_inspection.php?id=<?php echo $inspection['id']; ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_inspection.php?id=<?php echo $inspection['id']; ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="view_inspection.php?id=<?php echo $inspection['id']; ?>" 
                                           class="btn btn-sm btn-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_inspection.php?id=<?php echo $inspection['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php if($inspection['status'] === 'completed' && !empty($inspection['report_pdf'])): ?>
                                            <a href="../download_report.php?id=<?php echo $inspection['id']; ?>" 
                                               class="btn btn-sm btn-success" title="Download Report">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="view_report.php?id=<?php echo $inspection['id']; ?>" 
                                           class="btn btn-sm btn-success" title="View Report">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>