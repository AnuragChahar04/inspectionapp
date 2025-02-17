<?php
require 'Connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Reports</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .audit-reports-container {
            margin-top: 50px;
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-left: 250px;
        }
        .audit-item {
            padding: 15px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            cursor: pointer;
        }
        .audit-item:hover {
            background-color: #f1f1f1;
        }
        .modal-content {
            padding: 20px;
            border-radius: 8px;
        }
        .close {
            float: right;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="audit-reports-container">
        <?php include "sidebar.php"; ?>
        <h2 class="text-center">Audit Reports</h2>
        
        <div class="audit-list">
            <!-- <?php
            $query = "SELECT * FROM audits ORDER BY start_date DESC";
            $result = mysqli_query($con, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='audit-item' onclick='openModal(" . $row['id'] . ", \"" . $row['client'] . "\", \"" . $row['product_name'] . "\", \"" . $row['start_date'] . "\", \"" . $row['end_date'] . "\", \"" . $row['audit_status'] . "\", \"" . $row['report_status'] . "\")'>";
                echo "<h3>" . $row['client'] . "</h3>";
                echo "<p>Product: " . $row['product_name'] . "</p>";
                echo "<p>Start Date: " . $row['start_date'] . "</p>";
                echo "<p>Status: " . $row['audit_status'] . "</p>";
                echo "</div>";
            }
            ?> -->
        </div>
    </div>

    <div id="auditModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Audit Report Details</h5>
                    <span class="close" onclick="closeModal()">&times;</span>
                </div>
                <div class="modal-body">
                    <p><strong>Client:</strong> <span id="client"></span></p>
                    <p><strong>Product Name:</strong> <span id="product_name"></span></p>
                    <p><strong>Start Date:</strong> <span id="start_date"></span></p>
                    <p><strong>End Date:</strong> <span id="end_date"></span></p>
                    <p><strong>Audit Status:</strong> <span id="audit_status"></span></p>
                    <p><strong>Report Status:</strong> <span id="report_status"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(id, client, product, start, end, status, report) {
            document.getElementById('client').innerText = client;
            document.getElementById('product_name').innerText = product;
            document.getElementById('start_date').innerText = start;
            document.getElementById('end_date').innerText = end;
            document.getElementById('audit_status').innerText = status;
            document.getElementById('report_status').innerText = report;
            $('#auditModal').modal('show');
        }

        function closeModal() {
            $('#auditModal').modal('hide');
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
