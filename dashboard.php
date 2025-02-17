<?php
    require "Connection.php"; // Database connection

    function getTotalInspectors($con) {
        // SQL query to select the total number of inspectors
        $sql = "SELECT COUNT(*) as total_inspectors FROM inspector";
    
        // Execute the query
        $result = mysqli_query($con, $sql);
    
        // Check if the query was successful
        if ($result) {
            // Fetch the result
            $row = mysqli_fetch_assoc($result);
    
            // Return the total number of inspectors
            return $row['total_inspectors'];
        } else {
            // Return an error message if the query failed
            return "Error: " . mysqli_error($con);
        }
    }
    $totalInspectors = getTotalInspectors($con);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300&display=swap"> <!-- Linking Google Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
    <!-- Sidebar -->
    <?php include "sidebar.php"; ?>

    <!-- Main Dashboard Content -->
    <div class="dashboard">
        <header>
            <h2>Admin Dashboard</h2>
            <div class="buttons">
                <button class="add-btn"><i class="fa solid fa-plus"></i> Add New Audit</button>
                <button class="add-btn"><i class="fa solid fa-user-plus"></i> Add New Inspector</button>
            </div>
        </header>

        <!-- Overview Cards -->
        <div class="cards">
            <div class="card">
            <i class="fa-solid fa-list-check"></i>
                <h3>Total Audits</h3>
                <p>25</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-circle-notch"></i>
                <h3>Ongoing Audits</h3>
                <p>10</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-circle-check"></i>
                <h3>Completed Audits</h3>
                <p>15</p>
            </div>
            <div class="card">
                <i class="fa-solid fa-user-tie"></i>
                <h3>Total Inspectors</h3>
                <p><?php echo $totalInspectors; ?></p>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="recent-activities">
            <h3>Recent Activities</h3>
            <table>
                <tr>
                    <th>Audit ID</th>
                    <th>Inspector</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
                <tr>
                    <td>#A1001</td>
                    <td>John Doe</td>
                    <td><span class="ongoing">Ongoing</span></td>
                    <td>2025-02-15</td>
                </tr>
                <tr>
                    <td>#A1002</td>
                    <td>Jane Smith</td>
                    <td><span class="completed">Completed</span></td>
                    <td>2025-02-10</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
