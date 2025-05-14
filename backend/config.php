<?php
/**
 * Database Configuration
 * 
 * This file contains the database connection parameters for the application.
 * For security, in a production environment, this file should be placed outside
 * the web root directory or its permissions should be restricted.
 */

// Database credentials
define('DB_HOST', 'localhost');     // Database host (usually localhost for cPanel)
define('DB_USER', 'nyanorg3_admin');  // Your cPanel database username
define('DB_PASS', 'admin@nyan.org.ng');  // Your cPanel database password
define('DB_NAME', 'nyanorg3_nyan_form');  // Your database name

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper handling of special characters
$conn->set_charset("utf8mb4");

// Site configuration
define('SITE_NAME', 'Nigerian Youth Assembly for National Development (NYAN)');
define('ADMIN_EMAIL', 'admin@nyan.org.ng');
define('RECAPTCHA_SITE_KEY', '6LfpGDcrAAAAAJi1ENf4VyeW84wKYAonocJFoCdA');
define('RECAPTCHA_SECRET_KEY', '6LfpGDcrAAAAAK5PaXAr-RJKwR8rSbzuEWYkWld5');

// Error reporting (temporarily enabled for debugging)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set up error logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');
?>
