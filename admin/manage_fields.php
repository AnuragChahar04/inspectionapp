<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $section_id = (int)$_POST['section_id'];
            $field_name = $conn->real_escape_string($_POST['field_name']);
            $field_type = $conn->real_escape_string($_POST['field_type']);
            $is_required = isset($_POST['is_required']) ? 1 : 0;
            $field_order = (int)$_POST['field_order'];
            
            $sql = "INSERT INTO report_fields (section_id, field_name, field_type, is_required, field_order) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('issis', $section_id, $field_name, $field_type, $is_required, $field_order);
            
            if ($stmt->execute()) {
                $success = "Field added successfully";
            } else {
                $error = "Error adding field: " . $conn->error;
            }
        } elseif ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            $sql = "DELETE FROM report_fields WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id);
            
            if ($stmt->execute()) {
                $success = "Field deleted successfully";
            } else {
                $error = "Error deleting field: " . $conn->error;
            }
        }
    }
}

// Get all sections
$sections = $conn->query("SELECT * FROM report_sections ORDER BY section_order");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Field Management - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Field Management</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addFieldModal">
                <i class="fas fa-plus"></i> Add New Field
            </button>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <?php while($section = $sections->fetch_assoc()): ?>
                    <h5 class="mb-3"><?php echo htmlspecialchars($section['section_name']); ?></h5>
                    <?php
                    $fields = $conn->query("SELECT * FROM report_fields WHERE section_id = {$section['id']} ORDER BY field_order");
                    if ($fields->num_rows > 0):
                    ?>
                    <table class="table table-bordered mb-4">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Field Name</th>
                                <th>Type</th>
                                <th>Required</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($field = $fields->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($field['field_order']); ?></td>
                                <td><?php echo htmlspecialchars($field['field_name']); ?></td>
                                <td><?php echo htmlspecialchars($field['field_type']); ?></td>
                                <td><?php echo $field['is_required'] ? 'Yes' : 'No'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-danger delete-field" 
                                            data-id="<?php echo $field['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($field['field_name']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p class="text-muted">No fields added yet.</p>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="manage_template.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Template Management
            </a>
        </div>
    </div>

    <!-- Add Field Modal -->
    <div class="modal fade" id="addFieldModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Field</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Section</label>
                            <select name="section_id" class="form-control" required>
                                <?php
                                $sections->data_seek(0);
                                while($section = $sections->fetch_assoc()):
                                ?>
                                <option value="<?php echo $section['id']; ?>">
                                    <?php echo htmlspecialchars($section['section_name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Field Name</label>
                            <input type="text" name="field_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Field Type</label>
                            <select name="field_type" class="form-control" required>
                                <option value="text">Text</option>
                                <option value="number">Number</option>
                                <option value="image">Image</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="field_order" class="form-control" required>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_required" class="form-check-input" id="isRequired">
                            <label class="form-check-label" for="isRequired">Required Field</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Field</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.delete-field').click(function() {
                if (confirm('Are you sure you want to delete "' + $(this).data('name') + '"?')) {
                    var form = $('<form method="post">')
                        .append($('<input>').attr('type', 'hidden').attr('name', 'action').val('delete'))
                        .append($('<input>').attr('type', 'hidden').attr('name', 'id').val($(this).data('id')));
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>