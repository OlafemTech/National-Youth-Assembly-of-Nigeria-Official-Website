<?php
/**
 * CSRF Token Generator
 * 
 * This script generates and returns a CSRF token for form submissions.
 * It is called via AJAX when the contact form page loads.
 */

// Start session
session_start();

// Include utility functions
require_once 'utils.php';

// Set content type to JSON
header('Content-Type: application/json');

// Generate CSRF token
$csrf_token = generate_csrf_token();

// Return token as JSON
echo json_encode(['csrf_token' => $csrf_token]);
?>
