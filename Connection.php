<?php
  $db_host = 'localhost';
  $db_user = 'root';
  $db_password = '';
  $db_db = 'adminlogin001';

  $con = new mysqli($db_host, $db_user, $db_password, $db_db);

  if ($con->connect_error) {
      die("Database connection failed: " . $con->connect_error);
  }
?>
