<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact_no = $conn->real_escape_string($_POST['contact_no']);
    
    // Handle file upload
    $profile_picture = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "../assets/images/profiles/";
        $profile_picture = time() . '_' . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_dir . $profile_picture);
    }

    $sql = "INSERT INTO users (name, email, password, contact_no, profile_picture, role) 
            VALUES ('$name', '$email', '$password', '$contact_no', '$profile_picture', 'inspector')";

    if ($conn->query($sql)) {
        // Send welcome email
        $to = $email;
        $subject = "Welcome to Inspection App";
        $message = "Dear $name,\n\nWelcome to the Inspection App. Your account has been created successfully.";
        $headers = "From: noreply@inspectionapp.com";

        mail($to, $subject, $message, $headers);

        header('Location: login.php');
        exit();
    } else {
        $error = "Registration failed";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Register as Inspector</div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" name="contact_no" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Profile Picture</label>
                                <input type="file" name="profile_picture" class="form-control-file">
                            </div>
                            <button type="submit" class="btn btn-primary">Register</button>
                            <a href="login.php" class="btn btn-link">Back to Login</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>