<?php
session_start();
require "Connection.php";

// Fetch pending reports for the logged-in inspector
$inspectorName = $_SESSION['inspectorloginid'];

$sql = "SELECT ItemNumber, Description, Vendor, PONumber, report_status
        FROM `product`
        WHERE `inspectorname` = '$inspectorName' AND `report_status` = 'Pending'";
        
$result = mysqli_query($con, $sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Pending Reports</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Item Number</th>
                    <th>Description</th>
                    <th>Vendor</th>
                    <th>PO Number</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo $row['ItemNumber']; ?></td>
                        <td><?php echo $row['Description']; ?></td>
                        <td><?php echo $row['Vendor']; ?></td>
                        <td><?php echo $row['PONumber']; ?></td>
                        <td><?php echo $row['report_status']; ?></td>
                        <td>
                            <!-- Link to resume filling out the report -->
                            <a href="resume_report.php?item_number=<?php echo $row['ItemNumber']; ?>">Resume</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending reports found for the inspector: <?php echo $inspectorName; ?></p>
    <?php endif; ?>
</body>
</html>
