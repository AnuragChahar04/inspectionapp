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

// Get all sections and fields with their values
$sections_sql = "SELECT * FROM report_sections ORDER BY section_order";
$sections = $conn->query($sections_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inspection Report - <?php echo htmlspecialchars($inspection['title']); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .page-break {
                page-break-after: always;
            }
        }
        .report-header {
            border-bottom: 2px solid #333;
            margin-bottom: 2rem;
        }
        .field-value {
            border-bottom: 1px solid #ddd;
            padding: 0.5rem 0;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="no-print mb-4">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
            <a href="manage_inspections.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Inspections
            </a>
        </div>

        <div class="report-header">
            <h2 class="text-center">Inspection Report</h2>
            <div class="row mt-4">
                <div class="col-md-6">
                    <p><strong>PO Number:</strong> <?php echo htmlspecialchars($inspection['po_number']); ?></p>
                    <p><strong>Title:</strong> <?php echo htmlspecialchars($inspection['title']); ?></p>
                    <p><strong>Inspector:</strong> <?php echo htmlspecialchars($inspection['inspector_name']); ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst($inspection['status']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Company:</strong> <?php echo htmlspecialchars($inspection['company_name']); ?></p>
                    <p><strong>Contact:</strong> <?php echo htmlspecialchars($inspection['contact_person']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($inspection['address']); ?></p>
                    <p><strong>State:</strong> <?php echo htmlspecialchars($inspection['state']); ?></p>
                </div>
            </div>
        </div>

        <?php while($section = $sections->fetch_assoc()): ?>
            <div class="section mb-4">
                <h4><?php echo htmlspecialchars($section['section_name']); ?></h4>
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
                    <div class="field-group">
                        <label><?php echo htmlspecialchars($field['field_name']); ?></label>
                        <div class="field-value">
                            <?php if($field['field_type'] == 'image' && !empty($field['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($field['image_path']); ?>" 
                                     class="img-fluid" style="max-width: 300px;">
                            <?php else: ?>
                                <?php echo htmlspecialchars($field['value'] ?? 'N/A'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <?php 
            $current_position = $sections->current_field;
            if($sections->fetch_assoc()): 
                if($current_position > 0) {
                    $sections->data_seek($current_position);
                }
            ?>
                <div class="page-break"></div>
            <?php endif; ?>
        <?php endwhile; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>