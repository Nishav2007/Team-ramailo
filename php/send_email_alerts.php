<?php
/**
 * EMAIL SENDING FUNCTIONS
 * Handles sending water arrival notifications via email
 */

/**
 * Send water arrival alert email
 * 
 * For XAMPP testing, this uses PHP's mail() function.
 * For production with Gmail, uncomment PHPMailer code below.
 */
function sendWaterAlert($to_email, $to_name, $location_name) {
    // Email subject
    $subject = "💧 Water Alert - " . $location_name;
    
    // Email body
    $current_time = date('g:i A, F j, Y');
    
    $message = "Dear {$to_name},\n\n";
    $message .= "Good news! Melamchi water has arrived in {$location_name} area.\n\n";
    $message .= "Time: {$current_time}\n\n";
    $message .= "Please collect water immediately as supply duration may be limited.\n\n";
    $message .= "Thank you for using Melamchi Water Alert System.\n\n";
    $message .= "────────────────────────────────\n";
    $message .= "View water history: " . SITE_URL . "/history.html\n";
    $message .= "Manage your account: " . SITE_URL . "/dashboard.html\n";
    
    // Email headers
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    
    // Send email using PHP mail() - works with XAMPP if mail is configured
    $sent = mail($to_email, $subject, $message, $headers);
    
    if ($sent) {
        return [
            'success' => true,
            'message' => 'Email sent successfully'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to send email'
        ];
    }
}

/**
 * ALTERNATIVE: PHPMailer with Gmail SMTP
 * 
 * Uncomment this function and use it instead of the above for production.
 * You'll need to download PHPMailer and place it in the php/ folder.
 * 
 * Download from: https://github.com/PHPMailer/PHPMailer
 */

/*
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendWaterAlertWithGmail($to_email, $to_name, $location_name) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to_email, $to_name);
        
        // Content
        $current_time = date('g:i A, F j, Y');
        
        $mail->isHTML(false);
        $mail->Subject = "💧 Water Alert - " . $location_name;
        $mail->Body = "Dear {$to_name},\n\n";
        $mail->Body .= "Good news! Melamchi water has arrived in {$location_name} area.\n\n";
        $mail->Body .= "Time: {$current_time}\n\n";
        $mail->Body .= "Please collect water immediately as supply duration may be limited.\n\n";
        $mail->Body .= "Thank you for using Melamchi Water Alert System.\n\n";
        $mail->Body .= "────────────────────────────────\n";
        $mail->Body .= "View water history: " . SITE_URL . "/history.html\n";
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Email sent successfully via Gmail'
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => "Email could not be sent. Error: {$mail->ErrorInfo}"
        ];
    }
}
*/
?>

