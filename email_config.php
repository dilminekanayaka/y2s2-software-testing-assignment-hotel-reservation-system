<?php
/**
 * Email Configuration for Flower Garden Hotels
 * 
 * This file contains email configuration settings.
 * For production use, configure your SMTP settings here.
 */

// Email Configuration
define('EMAIL_FROM_NAME', 'Flower Garden Hotels');
define('EMAIL_FROM_EMAIL', 'noreply@flowergarden.com');
define('EMAIL_REPLY_TO', 'support@flowergarden.com');

// SMTP Configuration (for production use)
// Uncomment and configure these for SMTP email sending
/*
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_SECURE', 'tls');
*/

// Email Templates Configuration
define('EMAIL_TEMPLATE_HEADER_COLOR_CONFIRMATION', '#667eea');
define('EMAIL_TEMPLATE_HEADER_COLOR_CANCELLATION', '#dc3545');
define('EMAIL_TEMPLATE_HEADER_COLOR_STATUS_UPDATE', '#17a2b8');

// Website URLs for email links
define('WEBSITE_URL', 'http://localhost/flowergarden');
define('MY_RESERVATIONS_URL', WEBSITE_URL . '/my_reservations.php');
define('HOTELS_URL', WEBSITE_URL . '/hotels.php');
define('HOME_URL', WEBSITE_URL . '/home.php');

/**
 * Get email configuration
 */
function getEmailConfig() {
    return [
        'from_name' => EMAIL_FROM_NAME,
        'from_email' => EMAIL_FROM_EMAIL,
        'reply_to' => EMAIL_REPLY_TO,
        'website_url' => WEBSITE_URL,
        'my_reservations_url' => MY_RESERVATIONS_URL,
        'hotels_url' => HOTELS_URL,
        'home_url' => HOME_URL
    ];
}

/**
 * Test email functionality
 */
function testEmailFunctionality() {
    $test_email = 'test@example.com';
    $test_subject = 'Test Email - Flower Garden Hotels';
    $test_message = '<h1>Test Email</h1><p>This is a test email to verify email functionality.</p>';
    
    return sendEmail($test_email, $test_subject, $test_message);
}

/**
 * Email debugging information
 */
function getEmailDebugInfo() {
    return [
        'php_mail_function' => function_exists('mail'),
        'sendmail_path' => ini_get('sendmail_path'),
        'smtp_host' => ini_get('SMTP'),
        'smtp_port' => ini_get('smtp_port'),
        'mail_log' => ini_get('mail.log'),
        'error_log' => ini_get('log_errors')
    ];
}
?>
