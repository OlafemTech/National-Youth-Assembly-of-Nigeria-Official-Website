<?php
/**
 * Membership Application Processing Script
 * 
 * This script handles the membership application form submissions, validates input data,
 * stores the data in the database, uploads files, and sends email notifications.
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
    $first_name = sanitize_input($_POST['firstName'] ?? '');
    $last_name = sanitize_input($_POST['lastName'] ?? '');
    $middle_name = sanitize_input($_POST['middleName'] ?? '');
    $gender = sanitize_input($_POST['gender'] ?? '');
    $date_of_birth = sanitize_input($_POST['dateOfBirth'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $whatsapp = sanitize_input($_POST['whatsapp'] ?? '');
    $state = sanitize_input($_POST['state'] ?? '');
    $lga = sanitize_input($_POST['lga'] ?? '');
    $polling_unit = sanitize_input($_POST['pollingUnit'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');
    $occupation = sanitize_input($_POST['occupation'] ?? '');
    $education = sanitize_input($_POST['education'] ?? '');
    $skills = sanitize_input($_POST['skills'] ?? '');
    $interests = sanitize_input($_POST['interests'] ?? '');
    $motivation = sanitize_input($_POST['motivation'] ?? '');
    $agreement = isset($_POST['agreement']) ? 1 : 0;
    
    // Validate form data
    $is_valid = true;
    $required_fields = [
        'firstName' => 'first_name',
        'lastName' => 'last_name',
        'gender' => 'gender',
        'dateOfBirth' => 'date_of_birth',
        'email' => 'email',
        'phone' => 'phone',
        'state' => 'state',
        'lga' => 'lga',
        'pollingUnit' => 'polling_unit',
        'address' => 'address',
        'occupation' => 'occupation',
        'education' => 'education',
        'motivation' => 'motivation'
    ];
    
    foreach ($required_fields as $field_name => $error_key) {
        if (empty($_POST[$field_name])) {
            $is_valid = false;
            $response['errors'][] = $field_name;
        }
    }
    
    // Validate email
    if (!empty($email) && !is_valid_email($email)) {
        $is_valid = false;
        $response['errors'][] = 'email';
    }
    
    // Validate date of birth (must be between 18-40 years old)
    if (!empty($date_of_birth)) {
        $dob = new DateTime($date_of_birth);
        $today = new DateTime();
        $age = $today->diff($dob)->y;
        
        if ($age < 18 || $age > 40) {
            $is_valid = false;
            $response['errors'][] = 'dateOfBirth';
            $response['message'] = 'You must be between 18 and 40 years old to apply for membership.';
        }
    }
    
    // Validate agreement checkbox
    if (!$agreement) {
        $is_valid = false;
        $response['errors'][] = 'agreement';
    }
    
    // Validate file uploads
    $upload_dir = '../uploads/';
    $photo_filename = '';
    $id_filename = '';
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Process passport photo upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            $is_valid = false;
            $response['errors'][] = 'photo';
            $response['message'] = 'Passport photo must be a JPG or PNG image.';
        } elseif ($_FILES['photo']['size'] > $max_size) {
            $is_valid = false;
            $response['errors'][] = 'photo';
            $response['message'] = 'Passport photo must be less than 2MB in size.';
        } else {
            // Generate unique filename
            $photo_ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $photo_filename = 'photo_' . time() . '_' . uniqid() . '.' . $photo_ext;
        }
    } else {
        $is_valid = false;
        $response['errors'][] = 'photo';
    }
    
    // Process ID document upload
    if (isset($_FILES['identification']) && $_FILES['identification']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($_FILES['identification']['type'], $allowed_types)) {
            $is_valid = false;
            $response['errors'][] = 'identification';
            $response['message'] = 'ID document must be a JPG, PNG, or PDF file.';
        } elseif ($_FILES['identification']['size'] > $max_size) {
            $is_valid = false;
            $response['errors'][] = 'identification';
            $response['message'] = 'ID document must be less than 2MB in size.';
        } else {
            // Generate unique filename
            $id_ext = pathinfo($_FILES['identification']['name'], PATHINFO_EXTENSION);
            $id_filename = 'id_' . time() . '_' . uniqid() . '.' . $id_ext;
        }
    } else {
        $is_valid = false;
        $response['errors'][] = 'identification';
    }
    
    // If validation fails, return error response
    if (!$is_valid) {
        if (empty($response['message'])) {
            $response['message'] = 'Please fill in all required fields correctly and upload the required documents.';
        }
        echo json_encode($response);
        exit;
    }
    
    // Move uploaded files to destination directory
    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_filename)) {
        $response['message'] = 'Failed to upload passport photo. Please try again.';
        echo json_encode($response);
        exit;
    }
    
    if (!move_uploaded_file($_FILES['identification']['tmp_name'], $upload_dir . $id_filename)) {
        // Remove the already uploaded photo if ID upload fails
        if (file_exists($upload_dir . $photo_filename)) {
            unlink($upload_dir . $photo_filename);
        }
        
        $response['message'] = 'Failed to upload ID document. Please try again.';
        echo json_encode($response);
        exit;
    }
    
    // Get additional information for security
    $ip_address = get_client_ip();
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO membership_applications (
        first_name, last_name, middle_name, gender, date_of_birth, 
        email, phone, whatsapp, state, lga, polling_unit, address, 
        occupation, education, skills, interests, motivation, 
        photo_filename, id_filename, ip_address, user_agent
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "sssssssssssssssssssss",
        $first_name, $last_name, $middle_name, $gender, $date_of_birth,
        $email, $phone, $whatsapp, $state, $lga, $polling_unit, $address,
        $occupation, $education, $skills, $interests, $motivation,
        $photo_filename, $id_filename, $ip_address, $user_agent
    );
    
    // Execute the statement
    if ($stmt->execute()) {
        $application_id = $stmt->insert_id;
        $stmt->close();
        
        // Send email notification to admin
        $admin_email_data = [
            'to' => ADMIN_EMAIL,
            'subject' => 'New Membership Application: ' . $first_name . ' ' . $last_name,
            'applicant_name' => $first_name . ' ' . $last_name,
            'applicant_email' => $email,
            'applicant_phone' => $phone,
            'applicant_state' => $state,
            'reference_id' => $application_id,
            'reference_type' => 'membership',
            'type' => 'admin_email'
        ];
        
        $admin_notification = send_email($admin_email_data, 'admin_membership_notification');
        
        // Send confirmation email to applicant
        $user_email_data = [
            'to' => $email,
            'name' => $first_name . ' ' . $last_name,
            'subject' => 'Your NYAN Membership Application',
            'reference_id' => $application_id,
            'reference_type' => 'membership',
            'type' => 'user_confirmation'
        ];
        
        $user_notification = send_email($user_email_data, 'user_membership_confirmation');
        
        // Set success response
        $response['success'] = true;
        $response['message'] = 'Your application has been submitted successfully! We will review your application and contact you soon.';
        
        // Log any email sending issues
        if (!$admin_notification['success']) {
            log_event('Failed to send admin membership notification: ' . $admin_notification['message'], 'error');
        }
        
        if (!$user_notification['success']) {
            log_event('Failed to send user membership confirmation: ' . $user_notification['message'], 'error');
        }
    } else {
        // Log database error
        log_event('Database error: ' . $stmt->error, 'error');
        
        // Remove uploaded files if database insertion fails
        if (file_exists($upload_dir . $photo_filename)) {
            unlink($upload_dir . $photo_filename);
        }
        
        if (file_exists($upload_dir . $id_filename)) {
            unlink($upload_dir . $id_filename);
        }
        
        $response['message'] = 'Sorry, there was a problem processing your application. Please try again later.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return JSON response
echo json_encode($response);
?>
