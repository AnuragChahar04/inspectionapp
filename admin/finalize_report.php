<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Handle AJAX requests for updating order
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_order'])) {
    $sections = json_decode($_POST['sections'], true);
    foreach ($sections as $order => $section_id) {
        $conn->query("UPDATE report_sections SET section_order = $order WHERE id = $section_id");
    }
    exit;
}

// Get all sections with their fields
$sections = $conn->query("SELECT * FROM report_sections ORDER BY section_order");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Finalize Report - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <style>
        .section-card {
            cursor: move;
            margin-bottom: 1rem;
        }
        .section-card:hover {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .preview-field {
            padding: 10px;
            border: 1px solid #ddd;
            margin: 5px 0;
            border-radius: 4px;
        }
        .preview-field.required::after {
            content: '*';
            color: red;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Finalize Report Template</h2>
            <button class="btn btn-success" id="saveOrder">
                <i class="fas fa-save"></i> Save Order
            </button>
        </div>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Drag and drop sections to reorder them in the final report.
        </div>

        <div id="sections-container">
            <?php while($section = $sections->fetch_assoc()): ?>
                <div class="card section-card" data-section-id="<?php echo $section['id']; ?>">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-grip-vertical mr-2"></i>
                            <?php echo htmlspecialchars($section['section_name']); ?>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $fields = $conn->query("SELECT * FROM report_fields WHERE section_id = {$section['id']} ORDER BY field_order");
                        while($field = $fields->fetch_assoc()):
                        ?>
                            <div class="preview-field <?php echo $field['is_required'] ? 'required' : ''; ?>">
                                <label><?php echo htmlspecialchars($field['field_name']); ?></label>
                                <?php if($field['field_type'] == 'text'): ?>
                                    <input type="text" class="form-control" disabled>
                                <?php elseif($field['field_type'] == 'number'): ?>
                                    <input type="number" class="form-control" disabled>
                                <?php elseif($field['field_type'] == 'image'): ?>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" disabled>
                                        <label class="custom-file-label">Choose file</label>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="mt-4">
            <a href="manage_template.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Template Management
            </a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sections-container').sortable({
                handle: '.card-header',
                placeholder: 'card section-card bg-light',
                tolerance: 'pointer'
            });

            $('#saveOrder').click(function() {
                var sections = [];
                $('.section-card').each(function() {
                    sections.push($(this).data('section-id'));
                });

                $.post('finalize_report.php', {
                    update_order: true,
                    sections: JSON.stringify(sections)
                }).done(function() {
                    alert('Report order saved successfully!');
                }).fail(function() {
                    alert('Error saving order. Please try again.');
                });
            });
        });
    </script>
</body>
</html>