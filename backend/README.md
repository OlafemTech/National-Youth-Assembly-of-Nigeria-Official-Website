# Backend Setup Instructions

This document provides instructions for setting up the PHP backend for the Sariki Motor website on a cPanel hosting environment.

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- cPanel hosting account
- Composer (for dependency management)

## Installation Steps

### 1. Database Setup

1. Log in to your cPanel account
2. Navigate to MySQL Databases
3. Create a new database (e.g., `sariki_motor`)
4. Create a database user with a strong password
5. Add the user to the database with all privileges

### 2. Configuration

1. Edit the `config.php` file and update the following:
   - Database credentials (DB_HOST, DB_USER, DB_PASS, DB_NAME)
   - Admin email address (ADMIN_EMAIL)
   - reCAPTCHA keys (register at https://www.google.com/recaptcha/)

```php
// Database credentials
define('DB_HOST', 'localhost');     // Usually 'localhost' for cPanel
define('DB_USER', 'your_db_user');  // Your cPanel database username
define('DB_PASS', 'your_db_pass');  // Your cPanel database password
define('DB_NAME', 'your_db_name');  // Your database name

// Site configuration
define('ADMIN_EMAIL', 'your-email@example.com');
define('RECAPTCHA_SITE_KEY', 'your_recaptcha_site_key');
define('RECAPTCHA_SECRET_KEY', 'your_recaptcha_secret_key');
```

### 3. Install Dependencies

1. Upload all backend files to your server using FTP or cPanel File Manager
2. Connect to your server via SSH or use cPanel's Terminal
3. Navigate to the backend directory
4. Run `composer install` to install PHPMailer and other dependencies

### 4. Email Configuration

1. Update the SMTP settings in `mailer.php` to use your cPanel mail server:

```php
$mail->Host       = 'mail.yourdomain.com';  // Your cPanel mail server
$mail->Username   = 'noreply@yourdomain.com'; // Your email address
$mail->Password   = 'your_email_password';    // Your email password
```

### 5. Create Database Tables

1. Run the database setup script by visiting:
   `https://nyan.org.ng/backend/setup_database.php`
2. You should see a confirmation message that tables were created successfully

### 6. Update Contact Form

1. Edit the contact.html file to point the form action to the backend script:
   - Update the form action to `backend/process_contact.php`
   - Add CSRF token and reCAPTCHA fields
   - Add JavaScript for form validation and AJAX submission

### 7. Security Checks

1. Ensure the `.htaccess` file is properly uploaded to protect sensitive files
2. Verify that HTTPS is enabled for your domain
3. Test the contact form to ensure it's working correctly

## Troubleshooting

- Check PHP error logs in cPanel (Error Log)
- Verify database connection settings
- Ensure email credentials are correct
- Check file permissions (755 for directories, 644 for files)

## Support

For any issues or questions, please contact the developer at admin@nyan.org.ng
