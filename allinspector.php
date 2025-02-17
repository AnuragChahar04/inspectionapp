<?php
  require "Connection.php";
  $sql = "SELECT * FROM `inspector`";
  $result = mysqli_query($con, $sql);

  $numofinspector =  mysqli_num_rows($result);
  echo  "<div class='info'>There are " . $numofinspector . " Inspector Details.</div>";
  echo "<br>";

  echo "<table>"; // Start the table

  // Table headers
  echo "<tr>
          <th>ID</th>
          <th>Inspector Name</th>
          <th>Inspector Number</th>
          <th>Inspector Email</th>
        </tr>";

  // Fetch each row from the result set
  while($row = mysqli_fetch_assoc($result)) {
      echo "<tr>"; // Start a new row for each record

      // Add table data (cells)
      echo "<td>" . $row['id'] . "</td>";
      echo "<td>" . $row['inspectorname'] . "</td>";
      echo "<td>" . $row['inspectornum'] . "</td>";
      echo "<td>" . $row['inspectormail'] . "</td>";

      echo "</tr>"; // End the row
  }

  echo "</table>"; // End the table
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inspector Details</title>
    <link rel="stylesheet" href="allinspector.css"> <!-- Link to External CSS -->
</head>
<body>
    <a href="adminpanel.php" class="back-button">Back to Admin Panel</a>
</body>
</html>
