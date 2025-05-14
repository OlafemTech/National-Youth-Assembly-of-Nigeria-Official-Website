<?php
/**
 * Newsletter Subscription Processing Script
 * 
 * This script handles newsletter subscription form submissions, validates input data,
 * stores the data in the database, and sends confirmation emails.
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
    $email = sanitize_input($_POST['email'] ?? '');
    $name = sanitize_input($_POST['name'] ?? ''); // Optional
    $source_page = sanitize_input($_POST['source_page'] ?? '');
    
    // Validate email
    if (empty($email) || !is_valid_email($email)) {
        $response['message'] = 'Please enter a valid email address.';
        $response['errors'][] = 'email';
        echo json_encode($response);
        exit;
    }
    
    // Get additional information for security
    $ip_address = get_client_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id, status FROM newsletter_subscribers WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $subscriber = $result->fetch_assoc();
        
        // If already subscribed and active
        if ($subscriber['status'] === 'active') {
            $response['success'] = true;
            $response['message'] = 'You are already subscribed to our newsletter.';
            echo json_encode($response);
            exit;
        } 
        // If previously unsubscribed, reactivate
        else if ($subscriber['status'] === 'unsubscribed') {
            $update_stmt = $conn->prepare("UPDATE newsletter_subscribers SET status = 'active', updated_at = NOW() WHERE id = ?");
            $update_stmt->bind_param("i", $subscriber['id']);
            
            if ($update_stmt->execute()) {
                $subscriber_id = $subscriber['id'];
                $update_stmt->close();
                
                // Success response
                $response['success'] = true;
                $response['message'] = 'Welcome back! Your subscription has been reactivated.';
                
                // Send confirmation email
                send_subscription_confirmation($email, $name, $subscriber_id);
            } else {
                // Log database error
                log_event('Database error: ' . $update_stmt->error, 'error');
                $response['message'] = 'Sorry, there was a problem processing your subscription. Please try again later.';
            }
            
            echo json_encode($response);
            exit;
        }
    }
    
    // Insert new subscriber
    $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email, name, source_page, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $email, $name, $source_page, $ip_address, $user_agent);
    
    if ($stmt->execute()) {
        $subscriber_id = $stmt->insert_id;
        $stmt->close();
        
        // Send confirmation email
        send_subscription_confirmation($email, $name, $subscriber_id);
        
        // Success response
        $response['success'] = true;
        $response['message'] = 'Thank you for subscribing to our newsletter!';
    } else {
        // Log database error
        log_event('Database error: ' . $stmt->error, 'error');
        $response['message'] = 'Sorry, there was a problem processing your subscription. Please try again later.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return JSON response
echo json_encode($response);

/**
 * Send subscription confirmation email
 * 
 * @param string $email Subscriber's email
 * @param string $name Subscriber's name (optional)
 * @param int $subscriber_id Subscriber's ID in the database
 * @return void
 */
function send_subscription_confirmation($email, $name, $subscriber_id) {
    // Prepare email data
    $email_data = [
        'to' => $email,
        'name' => !empty($name) ? $name : 'Subscriber',
        'subject' => 'Welcome to ' . SITE_NAME . ' Newsletter',
        'reference_id' => $subscriber_id,
        'reference_type' => 'newsletter',
        'type' => 'user_confirmation'
    ];
    
    // Send email using the newsletter_confirmation template
    $result = send_email($email_data, 'newsletter_confirmation');
    
    // Log any email sending issues
    if (!$result['success']) {
        log_event('Failed to send newsletter confirmation: ' . $result['message'], 'error');
    }
    
    // Also notify admin about new subscriber
    $admin_email_data = [
        'to' => ADMIN_EMAIL,
        'subject' => 'New Newsletter Subscriber',
        'subscriber_email' => $email,
        'subscriber_name' => !empty($name) ? $name : 'Not provided',
        'reference_id' => $subscriber_id,
        'reference_type' => 'newsletter',
        'type' => 'admin_notification'
    ];
    
    $admin_result = send_email($admin_email_data, 'admin_newsletter_notification');
    
    // Log any admin email sending issues
    if (!$admin_result['success']) {
        log_event('Failed to send admin newsletter notification: ' . $admin_result['message'], 'error');
    }
}
?>
