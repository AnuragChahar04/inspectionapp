<?php
require 'Connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspector Management</title>
    <link rel="stylesheet" href="inspector.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="inspector-container">
        <h2 class="page_head">Inspector Management</h2>
        <button class="add-inspector-btn" onclick="openInspectorForm()">+ New Inspector</button>
        
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Assigned Audits</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT * FROM inspector ORDER BY inspectorname ASC";
                $result = mysqli_query($con, $query);
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['inspectorname'] . "</td>";
                    echo "<td>" . $row['contact'] . "</td>";
                    echo "<td>" . $row['assigned_audits'] . "</td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        function openInspectorForm() {
            window.location.href = 'add_inspector.php';
        }
    </script>
</body>
</html>
