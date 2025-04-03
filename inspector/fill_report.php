<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inspector') {
    header('Location: ../auth/login.php');
    exit();
}

$inspection_id = (int)$_GET['id'];
$inspector_id = $_SESSION['user_id'];

// Verify inspection assignment
$sql = "SELECT i.*, u.name as inspector_name 
        FROM inspections i 
        LEFT JOIN users u ON i.assigned_to = u.id 
        WHERE i.id = ? AND i.assigned_to = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $inspection_id, $inspector_id);
$stmt->execute();
$inspection = $stmt->get_result()->fetch_assoc();

if (!$inspection) {
    header('Location: dashboard.php');
    exit();
}

// Get all sections and their fields
$sections_sql = "SELECT * FROM report_sections ORDER BY section_order";
$sections = $conn->query($sections_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fill Inspection Report - <?php echo htmlspecialchars($inspection['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Inspection Report</h2>
            <div>
                <button type="button" class="btn btn-warning" id="pauseInspection">
                    <i class="fas fa-pause"></i> Pause
                </button>
                <button type="button" class="btn btn-success" id="saveInspection">
                    <i class="fas fa-check"></i> Complete & Save
                </button>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>PO Number:</strong> <?php echo htmlspecialchars($inspection['po_number']); ?></p>
                        <p><strong>Title:</strong> <?php echo htmlspecialchars($inspection['title']); ?></p>
                        <p><strong>Inspector:</strong> <?php echo htmlspecialchars($inspection['inspector_name']); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($inspection['company_name']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($inspection['address']); ?></p>
                        <p><strong>State:</strong> <?php echo htmlspecialchars($inspection['state']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <form id="reportForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="inspection_id" value="<?php echo $inspection_id; ?>">
            
            <?php while($section = $sections->fetch_assoc()): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo htmlspecialchars($section['section_name']); ?></h5>
                    </div>
                    <div class="card-body">
                        <?php
                        $fields_sql = "SELECT rf.*, rv.value, rv.image_path 
                                     FROM report_fields rf 
                                     LEFT JOIN report_values rv ON rf.id = rv.field_id 
                                     AND rv.inspection_id = $inspection_id 
                                     WHERE rf.section_id = {$section['id']} 
                                     ORDER BY rf.field_order";
                        $fields = $conn->query($fields_sql);
                        while($field = $fields->fetch_assoc()):
                        ?>
                            <div class="form-group">
                                <label><?php echo htmlspecialchars($field['field_name']); ?></label>
                                <?php if($field['field_type'] == 'text'): ?>
                                    <input type="text" 
                                           class="form-control" 
                                           name="field[<?php echo $field['id']; ?>]"
                                           value="<?php echo htmlspecialchars($field['value'] ?? ''); ?>">
                                           
                                <?php elseif($field['field_type'] == 'textarea'): ?>
                                    <textarea class="form-control" 
                                              name="field[<?php echo $field['id']; ?>]"
                                              rows="3"><?php echo htmlspecialchars($field['value'] ?? ''); ?></textarea>
                                              
                                <?php elseif($field['field_type'] == 'image'): ?>
                                    <?php if(!empty($field['image_path'])): ?>
                                        <div class="mb-2">
                                            <img src="<?php echo htmlspecialchars($field['image_path']); ?>" 
                                                 class="img-fluid" style="max-width: 200px;">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" 
                                           class="form-control-file" 
                                           name="image[<?php echo $field['id']; ?>]"
                                           accept="image/*">
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
    $(document).ready(function() {
        function saveReport(status) {
            var formData = new FormData($('#reportForm')[0]);
            formData.append('status', status);

            $.ajax({
                url: 'save_report.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if(status === 'completed') {
                        window.location.href = 'dashboard.php?success=1';
                    } else {
                        alert('Progress saved successfully');
                    }
                },
                error: function() {
                    alert('Error saving report. Please try again.');
                }
            });
        }

        $('#pauseInspection').click(function() {
            saveReport('in_progress');
        });

        $('#saveInspection').click(function() {
            if(confirm('Are you sure you want to complete this inspection? You won\'t be able to make changes after this.')) {
                saveReport('completed');
            }
        });

        // Auto-save every 5 minutes
        setInterval(function() {
            saveReport('in_progress');
        }, 300000);
    });
    </script>
</body>
</html>