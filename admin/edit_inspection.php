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

// Get inspection details
$sql = "SELECT * FROM inspections WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $inspection_id);
$stmt->execute();
$inspection = $stmt->get_result()->fetch_assoc();

if (!$inspection) {
    header('Location: manage_inspections.php');
    exit();
}

// Get all inspectors
$inspectors = $conn->query("SELECT id, name FROM users WHERE role = 'inspector' ORDER BY name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $po_number = $conn->real_escape_string($_POST['po_number']);
    $title = $conn->real_escape_string($_POST['title']);
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $state = $conn->real_escape_string($_POST['state']);
    $contact_person = $conn->real_escape_string($_POST['contact_person']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $inspector_id = (int)$_POST['inspector_id'];
    $status = $conn->real_escape_string($_POST['status']);

    $update_sql = "UPDATE inspections SET 
                   po_number = ?, title = ?, company_name = ?, address = ?, 
                   state = ?, contact_person = ?, contact_number = ?, 
                   email = ?, assigned_to = ?, status = ? 
                   WHERE id = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('ssssssssssi', 
        $po_number, $title, $company_name, $address, $state,
        $contact_person, $contact_number, $email, $inspector_id, $status,
        $inspection_id
    );

    if ($update_stmt->execute()) {
        header('Location: manage_inspections.php?success=1');
        exit();
    } else {
        $error = "Error updating inspection: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Inspection - <?php echo htmlspecialchars($inspection['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">Edit Inspection</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>PO Number</label>
                                <input type="text" name="po_number" class="form-control" 
                                       value="<?php echo htmlspecialchars($inspection['po_number']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" 
                                       value="<?php echo htmlspecialchars($inspection['title']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Company Name</label>
                                <input type="text" name="company_name" class="form-control" 
                                       value="<?php echo htmlspecialchars($inspection['company_name']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="3" required><?php 
                                    echo htmlspecialchars($inspection['address']); 
                                ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="state" class="form-control" 
                                       value="<?php echo htmlspecialchars($inspection['state']); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" 
                                       value="<?php echo htmlspecialchars($inspection['contact_person']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="tel" name="contact_number" class="form-control" 
                                       value="<?php echo htmlspecialchars($inspection['contact_number']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" 
                                       value="<?php echo htmlspecialchars($inspection['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Assign Inspector</label>
                                <select name="inspector_id" class="form-control" required>
                                    <?php while($inspector = $inspectors->fetch_assoc()): ?>
                                        <option value="<?php echo $inspector['id']; ?>" 
                                                <?php echo $inspection['assigned_to'] == $inspector['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($inspector['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" <?php echo $inspection['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="in_progress" <?php echo $inspection['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="completed" <?php echo $inspection['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="manage_inspections.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>