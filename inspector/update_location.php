<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'inspector') {
    http_response_code(403);
    exit('Unauthorized');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$inspector_id = $_SESSION['user_id'];
$inspection_id = (int)$_POST['inspection_id'];
$latitude = (float)$_POST['lat'];
$longitude = (float)$_POST['lng'];

// Verify this inspection belongs to this inspector
$check_sql = "SELECT id FROM inspections WHERE id = ? AND assigned_to = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param('ii', $inspection_id, $inspector_id);
$check_stmt->execute();
$result = $check_stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    exit('Invalid inspection');
}

// Update inspection status to in_progress
$update_sql = "UPDATE inspections SET status = 'in_progress' WHERE id = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param('i', $inspection_id);
$update_stmt->execute();

// Insert location
$sql = "INSERT INTO inspector_locations (inspector_id, inspection_id, latitude, longitude) 
        VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iidd', $inspector_id, $inspection_id, $latitude, $longitude);

if ($stmt->execute()) {
    http_response_code(200);
    echo 'Location updated successfully';
} else {
    http_response_code(500);
    echo 'Error updating location';
}