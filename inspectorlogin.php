<?php
  require "Connection.php"
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <title>Login</title>
</head>
    <div class="login-form">
        <h2>Inspector Login Panel</h2>
        <form method="POST">
        <div class="input-field"> 
                <input type="id" placeholder="id" name="id">
            </div>
            <div class="input-field">
                <input type="text" placeholder="Inspector name" name="user">
            </div>
            <div class="input-field"> 
                <input type="password" placeholder="Password" name="pass">
            </div>
            <button type="Submit" name="signin">Sign In</button> 
            <a href="login.php">Login As Admin</a>
        </form>
    </div>
    <?php

    if(isset($_POST['signin'])){
        $query="SELECT * FROM `inspector` WHERE `id` = '$_POST[id]' AND `inspectorname` = '$_POST[user]' AND `password` = '$_POST[pass]'";
        $result=mysqli_query($con,$query);
     if(mysqli_num_rows($result)==1)
     {
            session_start();
            $_SESSION['inspectorloginid'] = $_POST['user'];
            header("location:inspection_status.php");
        }else{
            echo "<script>alert('Incorrect Password');</script>";
        }
    }
    ?>
    
</body>
</html>