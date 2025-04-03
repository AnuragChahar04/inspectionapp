<?php
session_start();
require_once '../config/database.php';
require_once '../includes/header.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Get all inspectors
$sql = "SELECT * FROM users WHERE role = 'inspector' ORDER BY created_at DESC";
$result = $conn->query($sql);

// Handle all POST actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $contact = $conn->real_escape_string($_POST['contact_no']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        // Handle profile picture upload
        $profile_picture = '';
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $target_dir = "../assets/images/profiles/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $profile_picture = time() . '_' . basename($_FILES["profile_picture"]["name"]);
            move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_dir . $profile_picture);
        }

        $sql = "INSERT INTO users (name, email, password, contact_no, profile_picture, role) 
                VALUES ('$name', '$email', '$password', '$contact', '$profile_picture', 'inspector')";

        if ($conn->query($sql)) {
            // Send welcome email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                //Server settings
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'anuragchahar870@gmail.com';
                $mail->Password   = 'brvtftqvskymaviu';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port       = 587;

                //Recipients
                $mail->setFrom('anuragchahar870@gmail.com', 'Inspection App');
                $mail->addAddress($email, $name);

                //Content
                $mail->isHTML(true);
                $mail->Subject = "Welcome to Inspection App";
                $mail->Body    = "
                    <h2>Welcome to Inspection App</h2>
                    <p>Dear $name,</p>
                    <p>Welcome to the Inspection App. Your account has been created successfully.</p>
                    <p><strong>Your login credentials:</strong></p>
                    <ul>
                        <li>Email: $email</li>
                        <li>Password: " . $_POST['password'] . "</li>
                    </ul>
                    <p>Best regards,<br>UAC Team</p>
                ";

                $mail->send();
                $success = "Inspector added successfully and welcome email sent";
            } catch (Exception $e) {
                $success = "Inspector added successfully but failed to send email: {$mail->ErrorInfo}";
            }
        } else {
            $error = "Error adding inspector: " . $conn->error;
        }
    } elseif ($_POST['action'] == 'edit') {
        $id = $conn->real_escape_string($_POST['id']);
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $contact = $conn->real_escape_string($_POST['contact_no']);
        
        $update_sql = "UPDATE users SET name = '$name', email = '$email', contact_no = '$contact'";
        
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $update_sql .= ", password = '$password'";
        }
        
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $target_dir = "../assets/images/profiles/";
            $profile_picture = time() . '_' . basename($_FILES["profile_picture"]["name"]);
            move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_dir . $profile_picture);
            $update_sql .= ", profile_picture = '$profile_picture'";
        }
        
        $update_sql .= " WHERE id = '$id' AND role = 'inspector'";
        
        if ($conn->query($update_sql)) {
            $success = "Inspector updated successfully";
        } else {
            $error = "Error updating inspector: " . $conn->error;
        }
    } elseif ($_POST['action'] == 'delete') {
        $id = $conn->real_escape_string($_POST['id']);
        $delete_sql = "DELETE FROM users WHERE id = '$id' AND role = 'inspector'";
        
        if ($conn->query($delete_sql)) {
            $success = "Inspector deleted successfully";
        } else {
            $error = "Error deleting inspector: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Inspectors - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Inspectors</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#addInspectorModal">
                <i class="fas fa-plus"></i> Add New Inspector
            </button>
        </div>

        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Contact</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($inspector = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if ($inspector['profile_picture']): ?>
                                    <img src="../assets/images/profiles/<?php echo $inspector['profile_picture']; ?>" 
                                         class="rounded-circle" width="40" height="40">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-2x"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($inspector['name']); ?></td>
                            <td><?php echo htmlspecialchars($inspector['email']); ?></td>
                            <td><?php echo htmlspecialchars($inspector['contact_no']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($inspector['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info mr-1 edit-inspector"
                                    data-id="<?php echo $inspector['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($inspector['name']); ?>"
                                    data-email="<?php echo htmlspecialchars($inspector['email']); ?>"
                                    data-contact="<?php echo htmlspecialchars($inspector['contact_no']); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-inspector"
                                    data-id="<?php echo $inspector['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($inspector['name']); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Inspector Modal -->
    <div class="modal fade" id="addInspectorModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Inspector</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="add">
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
                        <button type="submit" class="btn btn-primary">Add Inspector</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Inspector Modal -->
    <div class="modal fade" id="editInspectorModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Inspector</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Password (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Contact Number</label>
                            <input type="text" name="contact_no" id="edit_contact" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Profile Picture</label>
                            <input type="file" name="profile_picture" class="form-control-file">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Inspector</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.edit-inspector').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var contact = $(this).data('contact');
                
                $('#edit_id').val(id);
                $('#edit_name').val(name);
                $('#edit_email').val(email);
                $('#edit_contact').val(contact);
                
                $('#editInspectorModal').modal('show');
            });
            
            $('.delete-inspector').click(function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                
                if (confirm('Are you sure you want to delete inspector: ' + name + '?')) {
                    var form = $('<form method="POST">')
                        .append($('<input>').attr('type', 'hidden').attr('name', 'action').val('delete'))
                        .append($('<input>').attr('type', 'hidden').attr('name', 'id').val(id));
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    </script>
</body>
</html>