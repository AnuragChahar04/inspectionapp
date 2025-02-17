<?php
  require_once "Connection.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-form">
        <h2>Admin Login Panel</h2>
        <form method="POST">
            <div class="input-field">
                <input type="text" placeholder="ID" name="id" required>
            </div>
            <div class="input-field">
                <input type="text" placeholder="Admin Name" name="user" required>
            </div>
            <div class="input-field">
                <input type="password" placeholder="Password" name="pass" required>
            </div>
            <button type="submit" name="signin">Sign In</button>
            <a href="inspectorlogin.php">Login As Inspector</a>
        </form>
    </div>

    <?php
    if (isset($_POST['signin'])) {
        $id = mysqli_real_escape_string($con, $_POST['id']);
        $user = mysqli_real_escape_string($con, $_POST['user']);
        $pass = mysqli_real_escape_string($con, $_POST['pass']);

        $query = "SELECT * FROM `admins` WHERE `Id` = '$id' AND `Username` = '$user' AND `Password` = '$pass'";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            session_start();
            $_SESSION['adminloginid'] = $user;
            header("location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect ID, Username, or Password');</script>";
        }
    }
    ?>
</body>
</html>
