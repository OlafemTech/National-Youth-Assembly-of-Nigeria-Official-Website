<?php
/**
 * Database Setup Script
 * 
 * This script creates the necessary tables for the contact form and membership systems.
 * Run this script once to set up your database structure.
 */

// Include database configuration
require_once 'config.php';

// Create contacts table
$sql_contacts = "CREATE TABLE IF NOT EXISTS contacts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied', 'spam') DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// Create membership applications table
$sql_members = "CREATE TABLE IF NOT EXISTS membership_applications (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    gender ENUM('male', 'female', 'other') NOT NULL,
    date_of_birth DATE NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    whatsapp VARCHAR(20),
    state VARCHAR(50) NOT NULL,
    lga VARCHAR(100) NOT NULL,
    polling_unit VARCHAR(255) NOT NULL,
    address TEXT NOT NULL,
    occupation VARCHAR(100) NOT NULL,
    education VARCHAR(50) NOT NULL,
    skills TEXT,
    interests VARCHAR(50),
    motivation TEXT NOT NULL,
    photo_filename VARCHAR(255) NOT NULL,
    id_filename VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// Create newsletter subscribers table
$sql_newsletter = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(100),
    source_page VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// Create notifications log table
$sql_notifications = "CREATE TABLE IF NOT EXISTS notification_logs (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    reference_id INT(11) UNSIGNED,
    reference_type ENUM('contact', 'membership') NOT NULL,
    notification_type ENUM('admin_email', 'user_confirmation') NOT NULL,
    recipient VARCHAR(100) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('success', 'failed') NOT NULL,
    error_message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

// Execute queries
if ($conn->query($sql_contacts) === TRUE) {
    echo "Table 'contacts' created successfully<br>";
} else {
    echo "Error creating table 'contacts': " . $conn->error . "<br>";
}

if ($conn->query($sql_members) === TRUE) {
    echo "Table 'membership_applications' created successfully<br>";
} else {
    echo "Error creating table 'membership_applications': " . $conn->error . "<br>";
}

if ($conn->query($sql_newsletter) === TRUE) {
    echo "Table 'newsletter_subscribers' created successfully<br>";
} else {
    echo "Error creating table 'newsletter_subscribers': " . $conn->error . "<br>";
}

if ($conn->query($sql_notifications) === TRUE) {
    echo "Table 'notification_logs' created successfully<br>";
} else {
    echo "Error creating table 'notification_logs': " . $conn->error . "<br>";
}

// Close connection
$conn->close();

echo "<p>Database setup completed.</p>";
echo "<p><a href='../index.html'>Return to Home Page</a></p>";
?>
