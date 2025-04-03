<?php
function sendInspectionNotification($inspector_email, $company_email, $inspection_details) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: UAC Inspection <noreply@uacinspection.com>' . "\r\n";

    // Inspector email
    $inspector_subject = "New Inspection Assignment - " . $inspection_details['title'];
    $inspector_message = "
        <h2>New Inspection Assignment</h2>
        <p>You have been assigned to a new inspection:</p>
        <p><strong>PO Number:</strong> {$inspection_details['po_number']}</p>
        <p><strong>Title:</strong> {$inspection_details['title']}</p>
        <p><strong>Company:</strong> {$inspection_details['company_name']}</p>
        <p><strong>Location:</strong> {$inspection_details['address']}, {$inspection_details['state']}</p>
    ";
    mail($inspector_email, $inspector_subject, $inspector_message, $headers);

    // Company email
    $company_subject = "Inspection Confirmation - " . $inspection_details['title'];
    $company_message = "
        <h2>Inspection Confirmation</h2>
        <p>An inspection has been scheduled for your company:</p>
        <p><strong>PO Number:</strong> {$inspection_details['po_number']}</p>
        <p><strong>Title:</strong> {$inspection_details['title']}</p>
        <p><strong>Date Created:</strong> {$inspection_details['created_at']}</p>
    ";
    mail($company_email, $company_subject, $company_message, $headers);
}
?>