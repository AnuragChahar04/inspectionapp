<?php
session_start();
require "Connection.php"; // Assuming you have a file to connect to the database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $inspector_name = $_SESSION['inspectorloginid'];
    $item_number = mysqli_real_escape_string($con, $_POST['item_number']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $vendor = mysqli_real_escape_string($con, $_POST['vendor']);
    $po_number = mysqli_real_escape_string($con, $_POST['po_number']);

    // Handle file uploads
    $product_front_view = uploadFile('product_front_view');
    $product_back_view = uploadFile('product_back_view');
    $product_side_view = uploadFile('product_side_view');
    $gift_box_front_view = uploadFile('gift_box_front_view');
    $product_barcode = uploadFile('product_barcode');
    $inner_box_barcode = uploadFile('inner_box_barcode');
    $outer_box_barcode = uploadFile('outer_box_barcode');
    $inner_box_front_view = uploadFile('inner_box_front_view');
    $master_carton_front_view = uploadFile('master_carton_front_view');

    // Checkbox values (using ternary operators to handle the checkbox status)
    $right_item = isset($_POST['right_item']) ? 1 : 0;
    $wrong_item = isset($_POST['wrong_item']) ? 1 : 0;
    $right_desc = isset($_POST['right_desc']) ? 1 : 0;
    $wrong_desc = isset($_POST['wrong_desc']) ? 1 : 0;
    $right_vendor = isset($_POST['right_vendor']) ? 1 : 0;
    $wrong_vendor = isset($_POST['wrong_vendor']) ? 1 : 0;
    $right_po = isset($_POST['right_po']) ? 1 : 0;
    $wrong_po = isset($_POST['wrong_po']) ? 1 : 0;
    $right_product_details = isset($_POST['right_product_details']) ? 1 : 0;
    $wrong_product_details = isset($_POST['wrong_product_details']) ? 1 : 0;
    $right_gift_box = isset($_POST['right_gift_box']) ? 1 : 0;
    $wrong_gift_box = isset($_POST['wrong_gift_box']) ? 1 : 0;
    $product_barcode_confirmed = isset($_POST['product_barcode_confirmed']) ? 1 : 0;
    $inner_box_barcode_confirmed = isset($_POST['inner_box_barcode_confirmed']) ? 1 : 0;
    $outer_box_barcode_confirmed = isset($_POST['outer_box_barcode_confirmed']) ? 1 : 0;
    $right_inner_box = isset($_POST['right_inner_box']) ? 1 : 0;
    $wrong_inner_box = isset($_POST['wrong_inner_box']) ? 1 : 0;
    $right_master_carton = isset($_POST['right_master_carton']) ? 1 : 0;
    $wrong_master_carton = isset($_POST['wrong_master_carton']) ? 1 : 0;

    // Determine form status
    if (!empty($item_number) && !empty($description) && !empty($vendor) && !empty($po_number)) {
        $form_status = 'completed';
    } else {
        $form_status = 'pending';
    }

    // Insert or update data into the database
    $query = "INSERT INTO inspections 
              (inspector_name, item_number, description, vendor, po_number, 
              product_front_view, product_back_view, product_side_view, gift_box_front_view, 
              product_barcode, inner_box_barcode, outer_box_barcode, inner_box_front_view, 
              master_carton_front_view, right_item, wrong_item, right_desc, wrong_desc, 
              right_vendor, wrong_vendor, right_po, wrong_po, right_product_details, wrong_product_details, 
              right_gift_box, wrong_gift_box, product_barcode_confirmed, inner_box_barcode_confirmed, 
              outer_box_barcode_confirmed, right_inner_box, wrong_inner_box, right_master_carton, 
              wrong_master_carton, status) 
              VALUES 
              ('$inspector_name', '$item_number', '$description', '$vendor', '$po_number', 
              '$product_front_view', '$product_back_view', '$product_side_view', '$gift_box_front_view', 
              '$product_barcode', '$inner_box_barcode', '$outer_box_barcode', '$inner_box_front_view', 
              '$master_carton_front_view', '$right_item', '$wrong_item', '$right_desc', '$wrong_desc', 
              '$right_vendor', '$wrong_vendor', '$right_po', '$wrong_po', '$right_product_details', '$wrong_product_details', 
              '$right_gift_box', '$wrong_gift_box', '$product_barcode_confirmed', '$inner_box_barcode_confirmed', 
              '$outer_box_barcode_confirmed', '$right_inner_box', '$wrong_inner_box', '$right_master_carton', 
              '$wrong_master_carton', '$form_status')";

    if (mysqli_query($con, $query)) {
        // Redirect based on form status
        if ($form_status == 'completed') {
            header("Location: complete.php");
        } else {
            header("Location: pending.php");
        }
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
} else {
    // If no submit button was clicked, redirect to new.php
    header("Location: new.php");
    exit();
    
}

// Function to handle file upload
function uploadFile($fieldName) {
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] == 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES[$fieldName]['name']);

        // Ensure the uploads directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $uploadFile)) {
            return $uploadFile; // Return the path of the uploaded file
        } else {
            echo "Failed to upload " . $fieldName;
            return null;
        }
    }
    return null;
}


