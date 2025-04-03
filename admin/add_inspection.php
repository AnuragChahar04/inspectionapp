<?php
session_start();
require_once '../config/database.php';
require_once '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Get all inspectors
$inspectors = $conn->query("SELECT id, name FROM users WHERE role = 'inspector' ORDER BY name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $po_number = $conn->real_escape_string($_POST['po_number']);
    $title = $conn->real_escape_string($_POST['title']);
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $address = $conn->real_escape_string($_POST['address']);
    $state = $conn->real_escape_string($_POST['state']);
    $contact_person = $conn->real_escape_string($_POST['contact_person']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    $email = $conn->real_escape_string($_POST['email']);
    $inspector_id = (int)$_POST['inspector_id'];
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "INSERT INTO inspections (po_number, title, company_name, address, state, contact_person, 
            contact_number, email, assigned_to, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssss', $po_number, $title, $company_name, $address, $state, 
                      $contact_person, $contact_number, $email, $inspector_id, $status);

    if ($stmt->execute()) {
        // Get inspector's email
        $inspector_sql = "SELECT email, name FROM users WHERE id = ?";
        $inspector_stmt = $conn->prepare($inspector_sql);
        $inspector_stmt->bind_param('i', $inspector_id);
        $inspector_stmt->execute();
        $inspector = $inspector_stmt->get_result()->fetch_assoc();
    
        // Send emails using PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'anuragchahar870@gmail.com';
            $mail->Password = 'brvtftqvskymaviu';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            // Send email to inspector
            $mail->setFrom('noreply@inspectionapp.com', 'Inspection App');
            $mail->addAddress($inspector['email'], $inspector['name']);
            $mail->Subject = "New Inspection Assignment - " . $title;
    
            $inspector_body = "Dear " . $inspector['name'] . ",\n\n";
            $inspector_body .= "You have been assigned a new inspection:\n\n";
            $inspector_body .= "Title: " . $title . "\n";
            $inspector_body .= "PO Number: " . $po_number . "\n";
            $inspector_body .= "Company: " . $company_name . "\n";
            $inspector_body .= "Location: " . $address . ", " . $state . "\n\n";
            $inspector_body .= "Please log in to the inspection app to view the full details.\n";
    
            $mail->Body = nl2br($inspector_body);
            $mail->AltBody = $inspector_body;
            $mail->send();
    
            // Clear recipients for new email
            $mail->clearAddresses();
    
            // Send email to company
            $mail->addAddress($email, $contact_person);
            $mail->Subject = "Upcoming Inspection Notification - " . $title;
    
            $company_body = "Dear " . $contact_person . ",\n\n";
            $company_body .= "This email is to inform you that an inspection has been scheduled:\n\n";
            $company_body .= "Title: " . $title . "\n";
            $company_body .= "PO Number: " . $po_number . "\n";
            $company_body .= "Location: " . $address . ", " . $state . "\n";
            $company_body .= "Inspector: " . $inspector['name'] . "\n\n";
            $company_body .= "Our inspector will contact you to confirm the inspection details.\n";
            $company_body .= "If you have any questions, please reply to this email.\n";
    
            $mail->Body = nl2br($company_body);
            $mail->AltBody = $company_body;
            $mail->send();
    
        } catch (Exception $e) {
            error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }

        header('Location: manage_inspections.php?success=1');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Inspection - Inspection App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Add New Inspection</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="form-group">
                                <label>PO Number</label>
                                <input type="text" name="po_number" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Company Name</label>
                                <input type="text" name="company_name" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="form-group">
                                <label>State</label>
                                <input type="text" name="state" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Contact Person</label>
                                <input type="text" name="contact_person" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="tel" name="contact_number" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label>Assign Inspector</label>
                                <select name="inspector_id" class="form-control" required>
                                    <option value="">Select Inspector</option>
                                    <?php while($inspector = $inspectors->fetch_assoc()): ?>
                                        <option value="<?php echo $inspector['id']; ?>">
                                            <?php echo htmlspecialchars($inspector['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Create Inspection
                                </button>
                                <a href="manage_inspections.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>