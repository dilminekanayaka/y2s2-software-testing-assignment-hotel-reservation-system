<?php
require_once 'config.php';

/**
 * Send booking confirmation email
 */
function sendBookingConfirmationEmail($user_email, $user_name, $booking_details) {
    $subject = "Booking Confirmation - Flower Garden Hotels";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
            .detail-label { font-weight: bold; color: #667eea; }
            .detail-value { color: #333; }
            .total { background: #667eea; color: white; padding: 15px; border-radius: 8px; text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; }
            .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            .btn { display: inline-block; background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🌸 Flower Garden Hotels</h1>
                <h2>Booking Confirmation</h2>
            </div>
            
            <div class='content'>
                <p>Dear " . htmlspecialchars($user_name) . ",</p>
                
                <p>Thank you for choosing Flower Garden Hotels! We're delighted to confirm your booking.</p>
                
                <div class='booking-details'>
                    <h3>Booking Details</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Booking ID:</span>
                        <span class='detail-value'>#" . htmlspecialchars($booking_details['booking_id']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Hotel:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['hotel_name']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Room Type:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['room_type']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Check-in:</span>
                        <span class='detail-value'>" . date('M j, Y', strtotime($booking_details['check_in'])) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Check-out:</span>
                        <span class='detail-value'>" . date('M j, Y', strtotime($booking_details['check_out'])) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Guests:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['guests']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Nights:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['nights']) . "</span>
                    </div>";
    
    if (!empty($booking_details['coupon_code'])) {
        $message .= "
                    <div class='detail-row'>
                        <span class='detail-label'>Coupon Used:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['coupon_code']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Discount:</span>
                        <span class='detail-value'>-$" . number_format($booking_details['discount_amount'], 2) . "</span>
                    </div>";
    }
    
    $message .= "
                    <div class='total'>
                        Total Amount: $" . number_format($booking_details['final_amount'], 2) . "
                    </div>
                </div>
                
                <p><strong>Important Information:</strong></p>
                <ul>
                    <li>Please arrive at the hotel on your check-in date</li>
                    <li>Check-in time: 2:00 PM | Check-out time: 11:00 AM</li>
                    <li>Bring a valid ID for verification</li>
                    <li>Contact the hotel directly for any special requests</li>
                </ul>
                
                <p>We look forward to welcoming you to Flower Garden Hotels!</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/flowergarden/my_reservations.php' class='btn'>View My Reservations</a>
                    <a href='http://localhost/flowergarden/hotels.php' class='btn'>Book Another Stay</a>
                </div>
                
                <div class='footer'>
                    <p>If you have any questions, please contact us at support@flowergarden.com</p>
                    <p>Flower Garden Hotels - Your Perfect Stay Awaits</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
    return sendEmail($user_email, $subject, $message);
}

/**
 * Send booking cancellation email
 */
function sendBookingCancellationEmail($user_email, $user_name, $booking_details) {
    $subject = "Booking Cancellation - Flower Garden Hotels";
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
            .detail-label { font-weight: bold; color: #dc3545; }
            .detail-value { color: #333; }
            .refund-info { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 8px; margin: 20px 0; }
            .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            .btn { display: inline-block; background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🌸 Flower Garden Hotels</h1>
                <h2>Booking Cancellation</h2>
            </div>
            
            <div class='content'>
                <p>Dear " . htmlspecialchars($user_name) . ",</p>
                
                <p>We have successfully cancelled your booking as requested.</p>
                
                <div class='booking-details'>
                    <h3>Cancelled Booking Details</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Booking ID:</span>
                        <span class='detail-value'>#" . htmlspecialchars($booking_details['booking_id']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Hotel:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['hotel_name']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Room Type:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['room_type']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Check-in:</span>
                        <span class='detail-value'>" . date('M j, Y', strtotime($booking_details['check_in'])) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Check-out:</span>
                        <span class='detail-value'>" . date('M j, Y', strtotime($booking_details['check_out'])) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Cancellation Date:</span>
                        <span class='detail-value'>" . date('M j, Y H:i') . "</span>
                    </div>
                </div>
                
                <div class='refund-info'>
                    <h4>💰 Refund Information</h4>
                    <p><strong>Refund Amount:</strong> $" . number_format($booking_details['refund_amount'], 2) . "</p>
                    <p><strong>Refund Method:</strong> Original payment method</p>
                    <p><strong>Processing Time:</strong> 3-5 business days</p>
                    <p>Your refund will be processed automatically and credited to your original payment method within 3-5 business days.</p>
                </div>
                
                <p>We're sorry to see you go! If you change your mind, we'd be happy to help you make a new reservation.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/flowergarden/hotels.php' class='btn'>Book a New Stay</a>
                    <a href='http://localhost/flowergarden/home.php' class='btn'>Visit Our Website</a>
                </div>
                
                <div class='footer'>
                    <p>If you have any questions about this cancellation, please contact us at support@flowergarden.com</p>
                    <p>Flower Garden Hotels - We hope to welcome you back soon!</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
    return sendEmail($user_email, $subject, $message);
}

/**
 * Send email using PHP mail function
 */
function sendEmail($to, $subject, $message) {
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=UTF-8',
        'From: Flower Garden Hotels <noreply@flowergarden.com>',
        'Reply-To: support@flowergarden.com',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    $headers_string = implode("\r\n", $headers);
    
    try {
        $result = mail($to, $subject, $message, $headers_string);
        
        if ($result) {
            error_log("Email sent successfully to: $to");
            return true;
        } else {
            error_log("Failed to send email to: $to");
            return false;
        }
    } catch (Exception $e) {
        error_log("Email error: " . $e->getMessage());
        return false;
    }
}

/**
 * Send booking status update email
 */
function sendBookingStatusUpdateEmail($user_email, $user_name, $booking_details, $new_status) {
    $subject = "Booking Status Update - Flower Garden Hotels";
    
    $status_colors = [
        'confirmed' => '#28a745',
        'cancelled' => '#dc3545',
        'pending' => '#ffc107',
        'completed' => '#17a2b8'
    ];
    
    $status_color = $status_colors[$new_status] ?? '#667eea';
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, $status_color 0%, darken($status_color, 20%) 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
            .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
            .status-badge { background: $status_color; color: white; padding: 10px 20px; border-radius: 20px; display: inline-block; font-weight: bold; text-transform: uppercase; }
            .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
            .detail-row { display: flex; justify-content: space-between; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #eee; }
            .detail-label { font-weight: bold; color: $status_color; }
            .detail-value { color: #333; }
            .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            .btn { display: inline-block; background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin: 10px 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🌸 Flower Garden Hotels</h1>
                <h2>Booking Status Update</h2>
            </div>
            
            <div class='content'>
                <p>Dear " . htmlspecialchars($user_name) . ",</p>
                
                <p>Your booking status has been updated:</p>
                
                <div style='text-align: center; margin: 20px 0;'>
                    <span class='status-badge'>" . ucfirst($new_status) . "</span>
                </div>
                
                <div class='booking-details'>
                    <h3>Booking Details</h3>
                    <div class='detail-row'>
                        <span class='detail-label'>Booking ID:</span>
                        <span class='detail-value'>#" . htmlspecialchars($booking_details['booking_id']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Hotel:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['hotel_name']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Room Type:</span>
                        <span class='detail-value'>" . htmlspecialchars($booking_details['room_type']) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Check-in:</span>
                        <span class='detail-value'>" . date('M j, Y', strtotime($booking_details['check_in'])) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Check-out:</span>
                        <span class='detail-value'>" . date('M j, Y', strtotime($booking_details['check_out'])) . "</span>
                    </div>
                    <div class='detail-row'>
                        <span class='detail-label'>Status Updated:</span>
                        <span class='detail-value'>" . date('M j, Y H:i') . "</span>
                    </div>
                </div>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/flowergarden/my_reservations.php' class='btn'>View My Reservations</a>
                </div>
                
                <div class='footer'>
                    <p>If you have any questions, please contact us at support@flowergarden.com</p>
                    <p>Flower Garden Hotels - Your Perfect Stay Awaits</p>
                </div>
            </div>
        </div>
    </body>
    </html>";
    
    return sendEmail($user_email, $subject, $message);
}
?>
