<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'inspector'])) {
    header('Location: auth/login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'inspector/dashboard.php'));
    exit();
}

$inspection_id = (int)$_GET['id'];

// Verify access rights
$sql = "SELECT report_pdf FROM inspections WHERE id = ? AND (status = 'completed'";
if ($_SESSION['role'] === 'inspector') {
    $sql .= " AND assigned_to = " . $_SESSION['user_id'];
}
$sql .= ")";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $inspection_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result || empty($result['report_pdf'])) {
    header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'inspector/dashboard.php'));
    exit();
}

$file_path = __DIR__ . '/' . $result['report_pdf'];

if (file_exists($file_path)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="inspection_report_' . $inspection_id . '.pdf"');
    header('Cache-Control: must-revalidate');
    readfile($file_path);
    exit();
}

header('Location: ' . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'inspector/dashboard.php'));