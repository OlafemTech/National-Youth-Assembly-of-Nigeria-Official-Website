<?php
/**
 * Email Notification System using PHPMailer
 * 
 * This file handles email notifications for contact form submissions.
 */

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Define path to Composer's autoloader
require 'vendor/autoload.php';

/**
 * Send email notification
 * 
 * @param array $data Email data (to, subject, message, etc.)
 * @param string $template Email template to use
 * @return array Status and message
 */
function send_email($data, $template = 'default') {
    global $conn;
    
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                // Enable verbose debug output (set to 0 in production)
        $mail->isSMTP();                                         // Send using SMTP
        $mail->Host       = 'mail.nyan.org.ng';               // Set the SMTP server to send through (use your cPanel mail server)
        $mail->SMTPAuth   = true;                                // Enable SMTP authentication
        $mail->Username   = 'noreply@nyan.org.ng';            // SMTP username
        $mail->Password   = 'admin@nyan.org.ng';               // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;      // Enable TLS encryption
        $mail->Port       = 587;                                 // TCP port to connect to (587 for TLS, 465 for SSL)
        
        // Set sender
        $mail->setFrom('noreply@nyan.org.ng', SITE_NAME);
        
        // Add recipient
        $mail->addAddress($data['to'], $data['name'] ?? '');
        
        // Add CC or BCC if provided
        if (isset($data['cc']) && !empty($data['cc'])) {
            $mail->addCC($data['cc']);
        }
        
        if (isset($data['bcc']) && !empty($data['bcc'])) {
            $mail->addBCC($data['bcc']);
        }
        
        // Set email format to HTML
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        // Set subject
        $mail->Subject = $data['subject'];
        
        // Set content based on template
        $mail->Body = get_email_template($data, $template);
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $data['message']));
        
        // Send the email
        $mail->send();
        
        // Log successful notification if reference_id is provided
        if (isset($data['reference_id']) && $data['reference_id'] > 0) {
            $reference_id = (int)$data['reference_id'];
            $reference_type = $conn->real_escape_string($data['reference_type'] ?? 'contact');
            $recipient = $conn->real_escape_string($data['to']);
            $subject = $conn->real_escape_string($data['subject']);
            $notification_type = $conn->real_escape_string($data['type'] ?? 'user_confirmation');
            
            $sql = "INSERT INTO notification_logs (reference_id, reference_type, notification_type, recipient, subject, status) 
                    VALUES ($reference_id, '$reference_type', '$notification_type', '$recipient', '$subject', 'success')";
            $conn->query($sql);
        }
        
        return [
            'success' => true,
            'message' => 'Email has been sent successfully'
        ];
        
    } catch (Exception $e) {
        $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        error_log($error_message);
        
        // Log failed notification if reference_id is provided
        if (isset($data['reference_id']) && $data['reference_id'] > 0) {
            $reference_id = (int)$data['reference_id'];
            $reference_type = $conn->real_escape_string($data['reference_type'] ?? 'contact');
            $recipient = $conn->real_escape_string($data['to']);
            $subject = $conn->real_escape_string($data['subject']);
            $notification_type = $conn->real_escape_string($data['type'] ?? 'user_confirmation');
            $error = $conn->real_escape_string($mail->ErrorInfo);
            
            $sql = "INSERT INTO notification_logs (reference_id, reference_type, notification_type, recipient, subject, status, error_message) 
                    VALUES ($reference_id, '$reference_type', '$notification_type', '$recipient', '$subject', 'failed', '$error')";
            $conn->query($sql);
        }
        
        return [
            'success' => false,
            'message' => $error_message
        ];
    }
}

/**
 * Get email template with data populated
 * 
 * @param array $data Data to populate in the template
 * @param string $template Template name
 * @return string The HTML email content
 */
function get_email_template($data, $template) {
    $html = '';
    
    switch ($template) {
        case 'admin_notification':
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>New Contact Form Submission</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #087418; color: #fff; padding: 15px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>New Contact Form Submission</h2>
                    </div>
                    <div class="content">
                        <p>Dear Administrator,</p>
                        <p>A new message has been submitted through the contact form on the website.</p>
                        <p><strong>Details:</strong></p>
                        <ul>
                            <li><strong>Name:</strong> ' . htmlspecialchars($data['contact_name']) . '</li>
                            <li><strong>Email:</strong> ' . htmlspecialchars($data['contact_email']) . '</li>
                            <li><strong>Subject:</strong> ' . htmlspecialchars($data['contact_subject']) . '</li>
                            <li><strong>Date:</strong> ' . date('Y-m-d H:i:s') . '</li>
                        </ul>
                        <p><strong>Message:</strong></p>
                        <p>' . nl2br(htmlspecialchars($data['contact_message'])) . '</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from your website contact form.</p>
                        <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '</p>
                    </div>
                </div>
            </body>
            </html>';
            break;
        
        case 'admin_membership_notification':
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>New Membership Application</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #087418; color: #fff; padding: 15px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                    .btn { display: inline-block; padding: 10px 20px; background-color: #087418; color: #fff; text-decoration: none; border-radius: 5px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>New Membership Application</h2>
                    </div>
                    <div class="content">
                        <p>Dear Administrator,</p>
                        <p>A new membership application has been submitted on the website.</p>
                        <p><strong>Applicant Details:</strong></p>
                        <ul>
                            <li><strong>Name:</strong> ' . htmlspecialchars($data['applicant_name']) . '</li>
                            <li><strong>Email:</strong> ' . htmlspecialchars($data['applicant_email']) . '</li>
                            <li><strong>Phone:</strong> ' . htmlspecialchars($data['applicant_phone']) . '</li>
                            <li><strong>State:</strong> ' . htmlspecialchars($data['applicant_state']) . '</li>
                            <li><strong>Date:</strong> ' . date('Y-m-d H:i:s') . '</li>
                        </ul>
                        <p>Please log in to the admin panel to review the complete application details and attached documents.</p>
                        <p style="text-align: center; margin-top: 30px;">
                            <a href="https://nyan.org.ng/admin/applications.php?id=' . $data['reference_id'] . '" class="btn">View Application</a>
                        </p>
                    </div>
                    <div class="footer">
                        <p>This is an automated message from your website membership system.</p>
                        <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '</p>
                    </div>
                </div>
            </body>
            </html>';
            break;
            
        case 'user_confirmation':
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Thank You for Contacting Us</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #087418; color: #fff; padding: 15px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Thank You for Contacting Us</h2>
                    </div>
                    <div class="content">
                        <p>Dear ' . htmlspecialchars($data['name']) . ',</p>
                        <p>Thank you for reaching out to ' . SITE_NAME . '. We have received your message and will get back to you as soon as possible.</p>
                        <p>For your reference, here is a copy of your message:</p>
                        <p><strong>Subject:</strong> ' . htmlspecialchars($data['subject']) . '</p>
                        <p><strong>Message:</strong></p>
                        <p>' . nl2br(htmlspecialchars($data['message'])) . '</p>
                        <p>If you have any additional information to provide, please reply to this email.</p>
                        <p>Best regards,<br>The ' . SITE_NAME . ' Team</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated confirmation message. Please do not reply to this email.</p>
                        <p>&copy; ' . date('Y') . ' ' . SITE_NAME . '</p>
                    </div>
                </div>
            </body>
            </html>';
            break;
            
        case 'user_membership_confirmation':
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Your NYAN Membership Application</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #087418; color: #fff; padding: 15px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                    .next-steps { background-color: #f9f9f9; padding: 15px; margin: 20px 0; border-left: 4px solid #087418; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Membership Application Received</h2>
                    </div>
                    <div class="content">
                        <p>Dear ' . htmlspecialchars($data["name"]) . ',</p>
                        <p>Thank you for applying to become a member of the Nigerian Youth Assembly for National Development (NYAN). We have received your application and it is currently under review.</p>
                        <p>Your application reference number is: <strong>NYAN-' . str_pad($data["reference_id"], 6, "0", STR_PAD_LEFT) . '</strong></p>
                        <div class="next-steps">
                            <h3>Next Steps:</h3>
                            <ol>
                                <li>Our membership committee will review your application within 7-14 business days.</li>
                                <li>You may be contacted for additional information or verification if needed.</li>
                                <li>Once approved, you will receive an official acceptance email with further instructions.</li>
                            </ol>
                        </div>
                        <p>If you have any questions about your application, please contact our membership department at membership@nyan.org.ng or call (+234) 809-123-4567.</p>
                        <p>We appreciate your interest in joining NYAN and your commitment to youth empowerment and national development.</p>
                        <p>Best regards,<br>The Membership Committee<br>' . SITE_NAME . '</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated confirmation message. Please do not reply to this email.</p>
                        <p>&copy; ' . date("Y") . ' ' . SITE_NAME . '</p>
                    </div>
                </div>
            </body>
            </html>';
            break;
            
        case 'newsletter_confirmation':
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Newsletter Subscription Confirmation</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #087418; color: #fff; padding: 15px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                    .social-links { text-align: center; margin: 20px 0; }
                    .social-links a { display: inline-block; margin: 0 10px; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #087418; color: #fff; text-decoration: none; border-radius: 5px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Newsletter Subscription Confirmed</h2>
                    </div>
                    <div class="content">
                        <p>Dear ' . htmlspecialchars($data["name"]) . ',</p>
                        <p>Thank you for subscribing to the ' . SITE_NAME . ' newsletter! You will now receive regular updates about our activities, events, and initiatives.</p>
                        <p>Here\'s what you can expect from our newsletter:</p>
                        <ul>
                            <li>Updates on upcoming events and programs</li>
                            <li>News about our community engagement initiatives</li>
                            <li>Youth development opportunities</li>
                            <li>Success stories from our members</li>
                            <li>Important announcements and updates</li>
                        </ul>
                        <p>We\'re excited to have you join our community of passionate young Nigerians committed to national development.</p>
                        <p>Follow us on social media to stay even more connected:</p>
                        <div class="social-links">
                            <a href="https://www.facebook.com/share/1EZDgP2woy/?mibextid=qi2Omg" class="button">Facebook</a>
                            <a href="https://x.com/Nyan_Nigeria?t=Yc-KOd2ZhUYNLzrXkrYcnQ&s=09" class="button">Twitter</a>
                            <a href="https://www.instagram.com/nyan_nigeria/" class="button">Instagram</a>
                        </div>
                        <p>If you ever wish to unsubscribe, you can do so by clicking the unsubscribe link at the bottom of any newsletter email.</p>
                        <p>Best regards,<br>The ' . SITE_NAME . ' Team</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated confirmation message. Please do not reply to this email.</p>
                        <p>&copy; ' . date("Y") . ' ' . SITE_NAME . '</p>
                    </div>
                </div>
            </body>
            </html>';
            break;
            
        case 'admin_newsletter_notification':
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>New Newsletter Subscriber</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #087418; color: #fff; padding: 15px; text-align: center; }
                    .content { padding: 20px; border: 1px solid #ddd; }
                    .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>New Newsletter Subscriber</h2>
                    </div>
                    <div class="content">
                        <p>Dear Administrator,</p>
                        <p>A new user has subscribed to the ' . SITE_NAME . ' newsletter.</p>
                        <p><strong>Subscriber Details:</strong></p>
                        <ul>
                            <li><strong>Email:</strong> ' . htmlspecialchars($data["subscriber_email"]) . '</li>
                            <li><strong>Name:</strong> ' . htmlspecialchars($data["subscriber_name"]) . '</li>
                            <li><strong>Date:</strong> ' . date("Y-m-d H:i:s") . '</li>
                        </ul>
                        <p>You can view and manage all newsletter subscribers in the admin panel.</p>
                    </div>
                    <div class="footer">
                        <p>This is an automated notification from your website newsletter system.</p>
                        <p>&copy; ' . date("Y") . ' ' . SITE_NAME . '</p>
                    </div>
                </div>
            </body>
            </html>';
            break;
            
        default:
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>' . htmlspecialchars($data['subject']) . '</title>
            </head>
            <body>
                <div style="max-width: 600px; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif;">
                    ' . $data['message'] . '
                </div>
            </body>
            </html>';
    }
    
    return $html;
}
?>
