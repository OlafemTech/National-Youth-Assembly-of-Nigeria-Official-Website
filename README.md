# Sariki Motor Website

This is the Sariki Motor website with PHP backend functionality for contact forms, membership applications, and newsletter subscriptions.

## Features

- Contact form with email notifications
- Membership application form with file uploads
- Newsletter subscription system
- MySQL database integration
- Security features (CSRF protection, reCAPTCHA, input validation)

## Setup Instructions

### Prerequisites

- PHP 7.4 or higher
- MySQL database
- Composer for dependency management

### Installation

1. Clone the repository or extract the project files to your web server directory.

2. Install PHP dependencies using Composer:
   ```
   cd /path/to/project
   composer install
   ```

3. Create a MySQL database for the application.

4. Configure the database connection in `backend/config.php`:
   - Update the database credentials
   - Set your site name and admin email
   - Configure reCAPTCHA keys (if using reCAPTCHA)

5. Run the database setup script to create the required tables:
   ```
   php backend/setup_database.php
   ```

6. Ensure the `uploads` directory has write permissions:
   ```
   chmod 755 uploads
   mkdir -p uploads/photos uploads/ids
   chmod 755 uploads/photos uploads/ids
   ```

7. Configure your web server to point to the project directory.

## Security Features

- Server-side input validation
- CSRF protection for all forms
- reCAPTCHA integration (optional)
- Prepared SQL statements to prevent SQL injection
- Input sanitization
- Secure file upload handling

## Email Configuration

The application uses PHPMailer for sending emails. Configure your SMTP settings in `backend/config.php`:

```php
// SMTP Configuration
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@example.com');
define('SMTP_PASSWORD', 'your-password');
define('SMTP_ENCRYPTION', 'tls');
```

## File Structure

- `backend/` - PHP backend scripts
  - `config.php` - Configuration settings
  - `setup_database.php` - Database setup script
  - `process_contact.php` - Contact form processing
  - `process_membership.php` - Membership application processing
  - `process_newsletter.php` - Newsletter subscription processing
  - `mailer.php` - Email handling
  - `utils.php` - Utility functions
- `css/` - Stylesheet files
- `js/` - JavaScript files
- `uploads/` - Directory for uploaded files
  - `photos/` - Passport photos
  - `ids/` - ID documents
- `plugins/` - Third-party libraries and plugins

## Troubleshooting

If you encounter issues with email sending, check:
1. SMTP credentials in `config.php`
2. Server firewall settings for SMTP ports
3. PHP error logs for detailed error messages

For database connection issues:
1. Verify database credentials in `config.php`
2. Ensure MySQL service is running
3. Check database user permissions
