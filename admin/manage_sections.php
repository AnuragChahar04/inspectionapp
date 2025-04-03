<?php
session_start();
require_once '../config/database.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $section_name = $conn->real_escape_string($_POST['section_name']);
            $section_order = (int)$_POST['section_order'];
            
            $sql = "INSERT INTO report_sections (section_name, section_order) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $section_name, $section_order);
            
            if ($stmt->execute()) {
                $success = "Section added successfully";
            } else {
                $error = "Error adding section: " . $conn->error;
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = (int)$_POST['id'];
            $section_name = $conn->real_escape_string($_POST['section_name']);
            $section_order = (int)$_POST['section_order'];
            
            $sql = "UPDATE report_sections SET section_name = ?, section_order = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sii', $section_name, $section_order, $id);
            
            if ($stmt->execute()) {
                $success = "Section updated successfully";
            } else {
                $error = "Error updating section: " . $conn->error;
            }
        } elseif ($_POST['action'] == 'delete') {
            $id = (int)$_POST['id'];
            $sql = "DELETE FROM report_sections WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id);
            
            if ($stmt->execute()) {
                $success = "Section deleted successfully";
            } else {
                $error = "Error deleting section: " . $conn->error;
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
    <title>Manage Report Sections - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manage Report Sections</h2>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addSectionModal">
                <i class="fas fa-plus"></i> Add New Section
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
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Section Name</th>
                            <th>Fields Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($section = $sections->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($section['section_order']); ?></td>
                                <td><?php echo htmlspecialchars($section['section_name']); ?></td>
                                <td>
                                    <?php 
                                    $field_count = $conn->query("SELECT COUNT(*) as count FROM report_fields WHERE section_id = " . $section['id'])->fetch_assoc();
                                    echo $field_count['count'];
                                    ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-section" 
                                            data-id="<?php echo $section['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($section['section_name']); ?>"
                                            data-order="<?php echo $section['section_order']; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-section"
                                            data-id="<?php echo $section['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($section['section_name']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <a href="manage_fields.php?section_id=<?php echo $section['id']; ?>" 
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-list"></i> Fields
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Section Modal -->
    <div class="modal fade" id="addSectionModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Section</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Section Name</label>
                            <input type="text" name="section_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="section_order" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Section</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Section Modal -->
    <div class="modal fade" id="editSectionModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Section</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_section_id">
                        <div class="form-group">
                            <label>Section Name</label>
                            <input type="text" name="section_name" id="edit_section_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Display Order</label>
                            <input type="number" name="section_order" id="edit_section_order" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Section</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-4">
            <a href="manage_template.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Template Management
            </a>
        </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.edit-section').click(function() {
                $('#edit_section_id').val($(this).data('id'));
                $('#edit_section_name').val($(this).data('name'));
                $('#edit_section_order').val($(this).data('order'));
                $('#editSectionModal').modal('show');
            });

            $('.delete-section').click(function() {
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