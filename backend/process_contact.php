<?php
/**
 * Contact Form Processing Script
 * 
 * This script handles the contact form submissions, validates input data,
 * stores the data in the database, and sends email notifications.
 */

// Start session for CSRF protection
session_start();

// Include required files
require_once 'config.php';
require_once 'utils.php';
require_once 'mailer.php';

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Check CSRF token
    if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) {
        $response['message'] = 'Security validation failed. Please refresh the page and try again.';
        echo json_encode($response);
        exit;
    }
    
    // reCAPTCHA validation has been removed
    
    // Get and sanitize form data
    $name = sanitize_input($_POST['name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $subject = sanitize_input($_POST['subject'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    
    // Validate form data
    $is_valid = true;
    
    if (empty($name)) {
        $is_valid = false;
        $response['errors'][] = 'name';
    }
    
    if (empty($email) || !is_valid_email($email)) {
        $is_valid = false;
        $response['errors'][] = 'email';
    }
    
    if (empty($subject)) {
        $is_valid = false;
        $response['errors'][] = 'subject';
    }
    
    if (empty($message)) {
        $is_valid = false;
        $response['errors'][] = 'message';
    }
    
    // If validation fails, return error response
    if (!$is_valid) {
        $response['message'] = 'Please fill in all required fields correctly.';
        echo json_encode($response);
        exit;
    }
    
    // Get additional information for security
    $ip_address = get_client_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $subject, $message, $ip_address, $user_agent);
    
    // Execute the statement
    if ($stmt->execute()) {
        $contact_id = $stmt->insert_id;
        $stmt->close();
        
        // Send email notification to admin
        $admin_email_data = [
            'to' => ADMIN_EMAIL,
            'subject' => 'New Contact Form Submission: ' . $subject,
            'contact_name' => $name,
            'contact_email' => $email,
            'contact_subject' => $subject,
            'contact_message' => $message,
            'reference_id' => $contact_id,
            'reference_type' => 'contact',
            'type' => 'admin_email'
        ];
        
        $admin_notification = send_email($admin_email_data, 'admin_notification');
        
        // Send confirmation email to user
        $user_email_data = [
            'to' => $email,
            'name' => $name,
            'subject' => 'Thank you for contacting ' . SITE_NAME,
            'message' => $message,
            'reference_id' => $contact_id,
            'reference_type' => 'contact',
            'type' => 'user_confirmation'
        ];
        
        $user_notification = send_email($user_email_data, 'user_confirmation');
        
        // Set success response
        $response['success'] = true;
        $response['message'] = 'Thank you for your message! We will get back to you soon.';
        
        // Log any email sending issues
        if (!$admin_notification['success']) {
            log_event('Failed to send admin notification: ' . $admin_notification['message'], 'error');
        }
        
        if (!$user_notification['success']) {
            log_event('Failed to send user confirmation: ' . $user_notification['message'], 'error');
        }
    } else {
        // Log database error
        log_event('Database error: ' . $stmt->error, 'error');
        $response['message'] = 'Sorry, there was a problem processing your request. Please try again later.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return JSON response
echo json_encode($response);
?>
