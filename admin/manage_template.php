<?php
session_start();
require_once '../config/database.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Template Management - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2>Report Template Management</h2>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Section Management</h5>
                                <p class="card-text">Create and organize report sections</p>
                                <a href="manage_sections.php" class="btn btn-primary">
                                    <i class="fas fa-layer-group"></i> Manage Sections
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Field Management</h5>
                                <p class="card-text">Add and configure fields within sections</p>
                                <a href="manage_fields.php" class="btn btn-success">
                                    <i class="fas fa-list-alt"></i> Manage Fields
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Finalize Report</h5>
                                <p class="card-text">Arrange and customize final report layout</p>
                                <a href="finalize_report.php" class="btn btn-info">
                                    <i class="fas fa-file-alt"></i> Finalize Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>