
<?php 
session_start();

if(isset($_POST['logout'])){
    session_destroy();
    header('location:login.php');
    exit(); // Ensure the script stops execution after the header is sent
}

require "Connection.php";

$sql = "SELECT * FROM `product` WHERE inspectorname = '$_SESSION[inspectorloginid]'";

$result = mysqli_query($con, $sql);

$numofproduct = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspector Panel</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
        }

        .logout-button {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .logout-button:hover {
            background-color: #d32f2f;
        }

        .info {
            display: block;
            text-align: center;
            margin: 20px auto;
            font-size: 18px;
            text-decoration: none;
            color: #333;
            background-color: #4CAF50;
            padding: 10px 20px;
            border-radius: 5px;
            max-width: 250px;
        }

        .info:hover {
            background-color: #45a049;
            color: white;
        }

        .styled-table {
            margin: 20px auto;
            width: 90%;
            border-collapse: collapse;
            font-size: 18px;
            text-align: left;
        }

        .styled-table thead tr {
            background-color: #4CAF50;
            color: white;
        }

        .styled-table th, 
        .styled-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .styled-table tbody tr {
            background-color: #f9f9f9;
        }

        .styled-table tbody tr:nth-child(even) {
            background-color: #f1f1f1;
        }

        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .header h1 {
                font-size: 24px;
            }

            .info {
                font-size: 16px;
                padding: 10px;
            }

            .styled-table th, 
            .styled-table td {
                font-size: 14px;
                padding: 10px;
            }

            .styled-table {
                font-size: 14px;
                width: 100%;
            }
        }

        /* Further adjustments for very small screens */
        @media (max-width: 480px) {
            .header h1 {
                font-size: 20px;
            }

            .info {
                font-size: 14px;
                max-width: 200px;
            }

            .styled-table th, 
            .styled-table td {
                font-size: 12px;
                padding: 8px;
            }

            .logout-button {
                font-size: 14px;
                padding: 8px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Welcome To Inspector Panel - <?php echo $_SESSION['inspectorloginid'] ?> </h1>
        <form method="POST">
            <button name="logout" class="logout-button">Log Out</button>
        </form>
    </div>

    <a href="newinspection.php" class="info">New Inspection Here</a>
    <p class="info">There are <?php echo $numofproduct; ?> Products inspected By You.</p>

    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vendor</th>
                <th>Item Name</th>
                <th>Description</th>
                <th>PO Number</th>
                <th>Colour</th>
                <th>Total Quantity</th>
                <th>Quality Inspection</th>
                <th>Inspection Date</th>
                <th>Admin Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['ID']; ?></td>
                    <td><?php echo $row['Vendor']; ?></td>
                    <td><?php echo $row['ItemNumber']; ?></td>
                    <td><?php echo $row['Description']; ?></td>
                    <td><?php echo $row['PONumber']; ?></td>
                    <td><?php echo $row['Colour']; ?></td>
                    <td><?php echo $row['Quantity']; ?></td>
                    <td><?php echo $row['Quality']; ?></td>
                    <td><?php echo $row['DOI']; ?></td>
                    <td><?php echo $row['Admin_Name']; ?></td>
                    <td><?php echo $row['Status']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</body>

</html>
