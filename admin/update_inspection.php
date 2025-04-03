<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['inspection_id'];
    
    $sql = "UPDATE inspections SET 
            po_number = ?, 
            title = ?, 
            company_name = ?, 
            address = ?, 
            state = ?, 
            contact_person = ?, 
            contact_number = ?, 
            email = ?, 
            assigned_to = ?, 
            status = ? 
            WHERE id = ?";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssssi', 
        $_POST['po_number'],
        $_POST['title'],
        $_POST['company_name'],
        $_POST['address'],
        $_POST['state'],
        $_POST['contact_person'],
        $_POST['contact_number'],
        $_POST['email'],
        $_POST['assigned_to'],
        $_POST['status'],
        $id
    );

    $result = $stmt->execute();
    
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
    exit();
}