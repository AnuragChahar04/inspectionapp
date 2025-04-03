<?php
session_start();
require_once '../config/database.php';
require_once '../vendor/tcpdf/tcpdf.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inspector') {
    http_response_code(403);
    exit('Unauthorized');
}

$inspection_id = (int)$_POST['inspection_id'];
$inspector_id = $_SESSION['user_id'];
$status = $_POST['status'];

// Verify inspection assignment
$check_sql = "SELECT id FROM inspections WHERE id = ? AND assigned_to = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('ii', $inspection_id, $inspector_id);
$check_stmt->execute();
if ($check_stmt->get_result()->num_rows === 0) {
    http_response_code(403);
    exit('Invalid inspection');
}

// Start transaction
$conn->begin_transaction();

try {
    // Update inspection status
    $update_sql = "UPDATE inspections SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param('si', $status, $inspection_id);
    $update_stmt->execute();

    // Handle text fields
    if (isset($_POST['field'])) {
        foreach ($_POST['field'] as $field_id => $value) {
            $sql = "INSERT INTO report_values (inspection_id, field_id, value) 
                    VALUES (?, ?, ?) 
                    ON DUPLICATE KEY UPDATE value = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iiss', $inspection_id, $field_id, $value, $value);
            $stmt->execute();
        }
    }

    // Handle image uploads
    if (isset($_FILES['image'])) {
        foreach ($_FILES['image']['tmp_name'] as $field_id => $tmp_name) {
            if (!empty($tmp_name)) {
                $filename = time() . '_' . $_FILES['image']['name'][$field_id];
                $upload_path = '../uploads/' . $filename;
                
                if (move_uploaded_file($tmp_name, $upload_path)) {
                    $sql = "INSERT INTO report_values (inspection_id, field_id, image_path) 
                            VALUES (?, ?, ?) 
                            ON DUPLICATE KEY UPDATE image_path = ?";
                    $stmt = $conn->prepare($sql);
                    $path = 'uploads/' . $filename;
                    $stmt->bind_param('iiss', $inspection_id, $field_id, $path, $path);
                    $stmt->execute();
                }
            }
        }
    }

    // Generate PDF if inspection is completed
    if ($status === 'completed') {
        $pdf_path = generatePDF($inspection_id, $conn);
        
        // Save PDF path in database
        $pdf_sql = "UPDATE inspections SET report_pdf = ? WHERE id = ?";
        $pdf_stmt = $conn->prepare($pdf_sql);
        $pdf_stmt->bind_param('si', $pdf_path, $inspection_id);
        $pdf_stmt->execute();
    }

    $conn->commit();
    http_response_code(200);
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

function generatePDF($inspection_id, $conn) {
    // Get inspection details
    $sql = "SELECT i.*, u.name as inspector_name 
            FROM inspections i 
            LEFT JOIN users u ON i.assigned_to = u.id 
            WHERE i.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $inspection_id);
    $stmt->execute();
    $inspection = $stmt->get_result()->fetch_assoc();

    // Create PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('Inspection App');
    $pdf->SetTitle('Inspection Report - ' . $inspection['title']);
    $pdf->SetHeaderData('', 0, 'Inspection Report', $inspection['title']);
    $pdf->setHeaderFont(['helvetica', '', 12]);
    $pdf->setFooterFont(['helvetica', '', 10]);
    $pdf->SetDefaultMonospacedFont('courier');
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    $pdf->AddPage();

    // Add inspection details
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'Inspection Details', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, 'PO Number: ' . $inspection['po_number'], 0, 1, 'L');
    $pdf->Cell(0, 8, 'Company: ' . $inspection['company_name'], 0, 1, 'L');
    $pdf->Cell(0, 8, 'Inspector: ' . $inspection['inspector_name'], 0, 1, 'L');
    $pdf->Cell(0, 8, 'Date: ' . date('Y-m-d'), 0, 1, 'L');
    $pdf->Ln(10);

    // Get sections and fields
    $sections_sql = "SELECT * FROM report_sections ORDER BY section_order";
    $sections = $conn->query($sections_sql);

    while ($section = $sections->fetch_assoc()) {
        $pdf->SetFont('helvetica', 'B', 13);
        $pdf->Cell(0, 10, $section['section_name'], 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 12);

        $fields_sql = "SELECT rf.*, rv.value, rv.image_path 
                      FROM report_fields rf 
                      LEFT JOIN report_values rv ON rf.id = rv.field_id 
                      AND rv.inspection_id = ? 
                      WHERE rf.section_id = ? 
                      ORDER BY rf.field_order";
        $fields_stmt = $conn->prepare($fields_sql);
        $fields_stmt->bind_param('ii', $inspection_id, $section['id']);
        $fields_stmt->execute();
        $fields = $fields_stmt->get_result();

        while ($field = $fields->fetch_assoc()) {
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(0, 8, $field['field_name'], 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 11);

            if ($field['field_type'] == 'image' && !empty($field['image_path'])) {
                $pdf->Image('../' . $field['image_path'], null, null, 100);
                $pdf->Ln(5);
            } else {
                $pdf->MultiCell(0, 8, $field['value'] ?? 'N/A', 0, 'L');
            }
            $pdf->Ln(5);
        }
        $pdf->AddPage();
    }

    // Save PDF
    $filename = 'report_' . $inspection_id . '_' . date('Ymd') . '.pdf';
    $pdf_path = 'reports/' . $filename;
    $pdf->Output('../' . $pdf_path, 'F');

    return $pdf_path;
}