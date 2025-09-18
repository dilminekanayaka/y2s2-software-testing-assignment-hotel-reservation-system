<?php
require_once 'auth.php';
require_once 'config.php';
require_once 'email_functions.php';

// Redirect if not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Validate that the user exists in the database
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND is_active = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $user_exists = $stmt->fetch();
    
    if (!$user_exists) {
        // User doesn't exist or is inactive, clear session and redirect
        session_destroy();
        header("Location: login.php?error=invalid_user");
        exit;
    }
} catch (PDOException $e) {
    $error = "Error validating user: " . $e->getMessage();
}

$success = '';
$hotel = null;
$room_type = null;

// Get booking parameters
$hotel_id = $_GET['hotel_id'] ?? '';
$room_type_id = $_GET['room_type_id'] ?? '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$guests = $_GET['guests'] ?? 1;

// Validate required parameters
if (!$hotel_id || !$room_type_id || !$check_in || !$check_out) {
    $error = "Missing booking parameters. Please try again.";
    $error .= "<br><br><a href='home.php' class='btn btn-primary'>Go Back to Search</a>";
} else {
    // Get hotel and room type details
    try {
        $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ? AND status = 'active'");
        $stmt->execute([$hotel_id]);
        $hotel = $stmt->fetch();
        
        if (!$hotel) {
            $error = "Hotel not found.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM room_types WHERE id = ? AND hotel_id = ? AND status = 'active'");
            $stmt->execute([$room_type_id, $hotel_id]);
            $room_type = $stmt->fetch();
            
            if (!$room_type) {
                $error = "Room type not found.";
            }
        }
    } catch (PDOException $e) {
        $error = "Error loading booking details: " . $e->getMessage();
    }
}

// Process booking form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hotel && $room_type) {
    try {
        // Validate dates
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $today = new DateTime();
        
        if ($check_in_date < $today) {
            throw new Exception("Check-in date cannot be in the past.");
        }
        
        if ($check_out_date <= $check_in_date) {
            throw new Exception("Check-out date must be after check-in date.");
        }
        
        // Validate and process coupon
        $coupon_code = sanitizeInput($_POST['coupon_code'] ?? '');
        $coupon_id = null;
        $discount_amount = 0.00;
        
        if ($coupon_code) {
            try {
                $stmt = $pdo->prepare("
                    SELECT * FROM coupons 
                    WHERE code = ? AND status = 'active' 
                    AND CURDATE() BETWEEN valid_from AND valid_until
                    AND (usage_limit IS NULL OR used_count < usage_limit)
                ");
                $stmt->execute([$coupon_code]);
                $coupon = $stmt->fetch();
                
                if ($coupon) {
                    $coupon_id = $coupon['id'];
                    
                    // Calculate discount
                    if ($coupon['discount_type'] === 'percentage') {
                        $discount_amount = ($total_amount * $coupon['discount_value']) / 100;
                        if ($coupon['max_discount'] && $discount_amount > $coupon['max_discount']) {
                            $discount_amount = $coupon['max_discount'];
                        }
                    } else {
                        $discount_amount = $coupon['discount_value'];
                    }
                    
                    // Check minimum amount requirement
                    if ($total_amount < $coupon['min_amount']) {
                        throw new Exception("Minimum booking amount of $" . $coupon['min_amount'] . " required for this coupon.");
                    }
                    
                    // Ensure discount doesn't exceed total amount
                    if ($discount_amount > $total_amount) {
                        $discount_amount = $total_amount;
                    }
                } else {
                    throw new Exception("Invalid or expired coupon code.");
                }
            } catch (Exception $e) {
                throw new Exception("Coupon error: " . $e->getMessage());
            }
        }
        
        // Calculate final amount
        $final_amount = $total_amount - $discount_amount;
        
        // Validate payment details
        $card_number = sanitizeInput($_POST['card_number'] ?? '');
        $card_name = sanitizeInput($_POST['card_name'] ?? '');
        $card_expiry = sanitizeInput($_POST['card_expiry'] ?? '');
        $card_cvv = sanitizeInput($_POST['card_cvv'] ?? '');
        $billing_address = sanitizeInput($_POST['billing_address'] ?? '');
        
        if (!$card_number || !$card_name || !$card_expiry || !$card_cvv || !$billing_address) {
            throw new Exception("Please fill in all payment details.");
        }
        
        // Validate card number (basic validation)
        $card_number = preg_replace('/\s+/', '', $card_number);
        if (!preg_match('/^\d{13,19}$/', $card_number)) {
            throw new Exception("Please enter a valid card number.");
        }
        
        // Validate expiry date
        if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $card_expiry)) {
            throw new Exception("Please enter expiry date in MM/YY format.");
        }
        
        // Validate CVV
        if (!preg_match('/^\d{3,4}$/', $card_cvv)) {
            throw new Exception("Please enter a valid CVV.");
        }
        
        // Calculate total amount
        $nights = $check_in_date->diff($check_out_date)->days;
        $total_amount = $room_type['base_price'] * $nights;
        
        // Generate booking reference
        $booking_reference = 'FG' . date('Ymd') . rand(1000, 9999);
        
        // Double-check user exists before inserting reservation
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        $user_check = $stmt->fetch();
        
        if (!$user_check) {
            throw new Exception("User session is invalid. Please log in again.");
        }
        
        // Insert reservation
        $stmt = $pdo->prepare("
            INSERT INTO reservations 
            (user_id, hotel_id, room_type_id, check_in_date, check_out_date, num_guests, total_amount, coupon_id, coupon_code, discount_amount, final_amount, status, special_requests, booking_reference, payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', ?, ?, 'paid')
        ");
        
        $stmt->execute([
            $_SESSION['user_id'],
            $hotel_id,
            $room_type_id,
            $check_in,
            $check_out,
            $guests,
            $total_amount,
            $coupon_id,
            $coupon_code,
            $discount_amount,
            $final_amount,
            sanitizeInput($_POST['special_requests'] ?? ''),
            $booking_reference
        ]);
        
        $reservation_id = $pdo->lastInsertId();
        
        // Update coupon usage count if coupon was used
        if ($coupon_id) {
            $stmt = $pdo->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE id = ?");
            $stmt->execute([$coupon_id]);
        }
        
        // Store payment details (in a real application, this would be encrypted and stored securely)
        $stmt = $pdo->prepare("
            INSERT INTO payments 
            (reservation_id, card_number, card_name, card_expiry, card_cvv, billing_address, amount, payment_status, payment_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'completed', NOW())
        ");
        
        // Mask card number for storage (show only last 4 digits)
        $masked_card = '**** **** **** ' . substr($card_number, -4);
        
        $stmt->execute([
            $reservation_id,
            $masked_card,
            $card_name,
            $card_expiry,
            '***', // Don't store CVV
            $billing_address,
            $final_amount
        ]);
        
        // Send confirmation email
        try {
            $booking_details = [
                'booking_id' => $booking_reference,
                'hotel_name' => $hotel['name'],
                'room_type' => $room_type['type_name'],
                'check_in' => $check_in,
                'check_out' => $check_out,
                'guests' => $guests,
                'nights' => (new DateTime($check_in))->diff(new DateTime($check_out))->days,
                'coupon_code' => $coupon_code,
                'discount_amount' => $discount_amount,
                'final_amount' => $final_amount
            ];
            
            $email_sent = sendBookingConfirmationEmail($_SESSION['user_email'], $_SESSION['user_name'], $booking_details);
            
            if ($email_sent) {
                error_log("Confirmation email sent successfully to: " . $_SESSION['user_email']);
            } else {
                error_log("Failed to send confirmation email to: " . $_SESSION['user_email']);
            }
        } catch (Exception $email_error) {
            error_log("Email error: " . $email_error->getMessage());
            // Don't fail the booking if email fails
        }
        
        // Redirect to confirmation page
        header("Location: booking_confirmation.php?id=" . $reservation_id);
        exit;
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Hotel - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .booking-section {
            padding: 8rem 5% 5rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .booking-container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .booking-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .booking-header h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .booking-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 3rem;
        }
        
        .booking-form {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .booking-summary {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            height: fit-content;
            position: sticky;
            top: 8rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .booking-form h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin: 2rem 0 1rem 0;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e1e5e9;
        }
        
        .booking-form h3:first-child {
            margin-top: 0;
        }
        
        .payment-security {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #28a745;
            font-size: 0.9rem;
            margin: 1rem 0;
            padding: 1rem;
            background: #f8fff9;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
        }
        
        .payment-security i {
            font-size: 1.1rem;
        }
        
        .coupon-input-group {
            display: flex;
            gap: 0.5rem;
        }
        
        .coupon-input-group input {
            flex: 1;
        }
        
        .btn-apply-coupon {
            background: #28a745;
            color: white;
            border: none;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-apply-coupon:hover {
            background: #218838;
            transform: translateY(-1px);
        }
        
        .btn-apply-coupon:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        
        .coupon-message {
            margin-top: 0.5rem;
            padding: 0.5rem;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 500;
            display: none;
        }
        
        .coupon-message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .coupon-message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* User avatar dropdown styling */
        .user-avatar-dropdown {
            position: relative;
            margin-left: 1rem;
        }
        
        .user-avatar-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .user-avatar-dropdown:hover .user-avatar .dropdown-arrow {
            transform: rotate(180deg);
        }
        
        .user-avatar {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 30px;
            border: 1px solid rgba(102, 126, 234, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            color: #667eea;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .user-avatar:hover {
            background: rgba(102, 126, 234, 0.2);
            transform: translateY(-1px);
        }
        
        .user-avatar i.fa-user-circle {
            font-size: 1.5rem;
        }
        
        .dropdown-arrow {
            font-size: 0.8rem;
            transition: transform 0.3s ease;
        }
        
        .user-avatar.active .dropdown-arrow {
            transform: rotate(180deg);
        }
        
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #e1e5e9;
            min-width: 250px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            margin-top: 0.5rem;
        }
        
        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-header {
            padding: 1rem;
            border-bottom: 1px solid #f1f3f4;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .user-info i {
            font-size: 2rem;
            color: #667eea;
        }
        
        .user-full-name {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }
        
        .user-email {
            color: #666;
            font-size: 0.85rem;
        }
        
        .dropdown-divider {
            height: 1px;
            background: #f1f3f4;
            margin: 0.5rem 0;
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }
        
        .dropdown-item:hover {
            background: #f8f9fa;
            color: #667eea;
        }
        
        .dropdown-item.logout {
            color: #dc3545;
        }
        
        .dropdown-item.logout:hover {
            background: #f8d7da;
            color: #721c24;
        }
        
        .dropdown-item i {
            width: 16px;
            text-align: center;
        }
        
        .btn-book {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-book:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .summary-hotel {
            margin-bottom: 2rem;
        }
        
        .summary-hotel h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .summary-location {
            color: #666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .summary-room {
            border-top: 1px solid #e1e5e9;
            padding-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .summary-room h4 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .summary-details {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .summary-dates {
            border-top: 1px solid #e1e5e9;
            padding-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .date-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .date-label {
            color: #666;
        }
        
        .date-value {
            font-weight: 600;
            color: #333;
        }
        
        .summary-total {
            border-top: 2px solid #667eea;
            padding-top: 1.5rem;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .total-label {
            color: #666;
        }
        
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        @media (max-width: 768px) {
            .booking-section {
                padding: 6rem 2% 3rem;
            }
            
            .booking-header h1 {
                font-size: 2rem;
            }
            
            .booking-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .booking-summary {
                position: static;
            }
        }
    </style>
</head>
<body>
    <section class="header">
        <a href="home.php" class="logo">Flower Garden</a>
        <nav class="navbar">
            <a href="home.php">home</a>
            <a href="hotels.php">hotels</a>
            <?php if (isUserLoggedIn()): ?>
                <div class="user-avatar-dropdown">
                    <div class="user-avatar" onclick="toggleDropdown()">
                        <i class="fas fa-user-circle"></i>
                        <span class="user-name"><?= htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]) ?></span>
                        <i class="fas fa-chevron-down dropdown-arrow"></i>
                    </div>
                    <div class="dropdown-menu" id="userDropdown">
                        <div class="dropdown-header">
                            <div class="user-info">
                                <i class="fas fa-user-circle"></i>
                                <div>
                                    <div class="user-full-name"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                                    <div class="user-email"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a href="user_settings.php" class="dropdown-item">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <a href="my_reservations.php" class="dropdown-item">
                            <i class="fas fa-calendar-check"></i> My Reservations
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php">login</a>
                <a href="register.php">register</a>
            <?php endif; ?>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <section class="booking-section">
        <div class="booking-container">
            <div class="booking-header">
                <h1>Complete Your Booking</h1>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($hotel && $room_type): ?>
                <div class="booking-grid">
                    <div class="booking-form">
                        <h2>Booking Details</h2>
                        
                        <form method="POST" action="" id="bookingForm">
                            <h3>Guest Information</h3>
                            <div class="form-group">
                                <label for="guests">Number of Guests</label>
                                <select id="guests" name="guests" required>
                                    <?php for ($i = 1; $i <= $room_type['max_occupancy']; $i++): ?>
                                        <option value="<?= $i ?>" <?= $guests == $i ? 'selected' : '' ?>>
                                            <?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="special_requests">Special Requests (Optional)</label>
                                <textarea id="special_requests" name="special_requests" 
                                          placeholder="Any special requests or notes for your stay..."></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label for="coupon_code">Coupon Code (Optional)</label>
                                <div class="coupon-input-group">
                                    <input type="text" id="coupon_code" name="coupon_code" 
                                           placeholder="Enter coupon code" maxlength="50">
                                    <button type="button" id="apply_coupon" class="btn-apply-coupon">
                                        <i class="fas fa-tag"></i> Apply
                                    </button>
                                </div>
                                <div id="coupon_message" class="coupon-message"></div>
                            </div>
                            
                            <h3>Payment Information</h3>
                            <div class="form-group">
                                <label for="card_name">Cardholder Name</label>
                                <input type="text" id="card_name" name="card_name" 
                                       placeholder="Name as it appears on card" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="card_number">Card Number</label>
                                <input type="text" id="card_number" name="card_number" 
                                       placeholder="1234 5678 9012 3456" maxlength="19" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="card_expiry">Expiry Date</label>
                                    <input type="text" id="card_expiry" name="card_expiry" 
                                           placeholder="MM/YY" maxlength="5" required>
                                </div>
                                <div class="form-group">
                                    <label for="card_cvv">CVV</label>
                                    <input type="text" id="card_cvv" name="card_cvv" 
                                           placeholder="123" maxlength="4" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="billing_address">Billing Address</label>
                                <textarea id="billing_address" name="billing_address" 
                                          placeholder="Enter your billing address" required></textarea>
                            </div>
                            
                            <div class="payment-security">
                                <i class="fas fa-shield-alt"></i>
                                <span>Your payment information is secure and encrypted</span>
                            </div>
                            
                            <button type="submit" class="btn-book">
                                <i class="fas fa-credit-card"></i> Complete Payment & Book Now
                            </button>
                        </form>
                    </div>
                    
                    <div class="booking-summary">
                        <div class="summary-hotel">
                            <h3><?= htmlspecialchars($hotel['name']) ?></h3>
                            <div class="summary-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($hotel['city'] . ', ' . $hotel['country']) ?>
                            </div>
                        </div>
                        
                        <div class="summary-room">
                            <h4><?= htmlspecialchars($room_type['type_name']) ?></h4>
                            <div class="summary-details">
                                <div><i class="fas fa-users"></i> Up to <?= $room_type['max_occupancy'] ?> guests</div>
                                <?php if ($room_type['bed_type']): ?>
                                    <div><i class="fas fa-bed"></i> <?= htmlspecialchars($room_type['bed_type']) ?></div>
                                <?php endif; ?>
                                <?php if ($room_type['room_size']): ?>
                                    <div><i class="fas fa-expand-arrows-alt"></i> <?= htmlspecialchars($room_type['room_size']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="summary-dates">
                            <div class="date-item">
                                <span class="date-label">Check-in:</span>
                                <span class="date-value"><?= date('M j, Y', strtotime($check_in)) ?></span>
                            </div>
                            <div class="date-item">
                                <span class="date-label">Check-out:</span>
                                <span class="date-value"><?= date('M j, Y', strtotime($check_out)) ?></span>
                            </div>
                            <div class="date-item">
                                <span class="date-label">Nights:</span>
                                <span class="date-value"><?= (new DateTime($check_in))->diff(new DateTime($check_out))->days ?></span>
                            </div>
                        </div>
                        
                        <div class="summary-total">
                            <div class="total-row">
                                <span class="total-label">Room Rate:</span>
                                <span>$<?= number_format($room_type['base_price'], 2) ?> per night</span>
                            </div>
                            <div class="total-row">
                                <span class="total-label">Total:</span>
                                <span class="total-amount">$<?= number_format($room_type['base_price'] * (new DateTime($check_in))->diff(new DateTime($check_out))->days, 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="script.js"></script>
    <script>
        // Card number formatting
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            if (formattedValue.length > 19) formattedValue = formattedValue.substr(0, 19);
            e.target.value = formattedValue;
        });
        
        // Expiry date formatting
        document.getElementById('card_expiry').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });
        
        // CVV formatting (numbers only)
        document.getElementById('card_cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });
        
        // Coupon validation
        document.getElementById('apply_coupon').addEventListener('click', function() {
            const couponCode = document.getElementById('coupon_code').value.trim();
            const messageDiv = document.getElementById('coupon_message');
            const applyBtn = document.getElementById('apply_coupon');
            
            if (!couponCode) {
                showCouponMessage('Please enter a coupon code.', 'error');
                return;
            }
            
            applyBtn.disabled = true;
            applyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Checking...';
            
            // Send AJAX request to validate coupon
            fetch('validate_coupon.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'coupon_code=' + encodeURIComponent(couponCode) + '&total_amount=' + encodeURIComponent(document.querySelector('.total-amount').textContent.replace('$', '').replace(',', ''))
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCouponMessage(data.message, 'success');
                    updateTotalAmount(data.discount_amount, data.final_amount);
                } else {
                    showCouponMessage(data.message, 'error');
                }
            })
            .catch(error => {
                showCouponMessage('Error validating coupon. Please try again.', 'error');
            })
            .finally(() => {
                applyBtn.disabled = false;
                applyBtn.innerHTML = '<i class="fas fa-tag"></i> Apply';
            });
        });
        
        function showCouponMessage(message, type) {
            const messageDiv = document.getElementById('coupon_message');
            messageDiv.textContent = message;
            messageDiv.className = 'coupon-message ' + type;
            messageDiv.style.display = 'block';
        }
        
        function updateTotalAmount(discountAmount, finalAmount) {
            const totalElement = document.querySelector('.total-amount');
            const originalAmount = parseFloat(totalElement.textContent.replace('$', '').replace(',', ''));
            
            // Add discount row if it doesn't exist
            let discountRow = document.querySelector('.discount-row');
            if (!discountRow) {
                discountRow = document.createElement('div');
                discountRow.className = 'total-row discount-row';
                discountRow.innerHTML = '<span class="total-label">Discount:</span><span class="discount-amount">-$' + discountAmount.toFixed(2) + '</span>';
                totalElement.parentNode.insertBefore(discountRow, totalElement.parentNode.lastElementChild);
            } else {
                discountRow.querySelector('.discount-amount').textContent = '-$' + discountAmount.toFixed(2);
            }
            
            // Update final amount
            totalElement.textContent = '$' + finalAmount.toFixed(2);
        }
        
        // Form validation
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const cardNumber = document.getElementById('card_number').value.replace(/\s+/g, '');
            const cardExpiry = document.getElementById('card_expiry').value;
            const cardCvv = document.getElementById('card_cvv').value;
            
            if (cardNumber.length < 13 || cardNumber.length > 19) {
                e.preventDefault();
                alert('Please enter a valid card number.');
                return;
            }
            
            if (!/^(0[1-9]|1[0-2])\/\d{2}$/.test(cardExpiry)) {
                e.preventDefault();
                alert('Please enter expiry date in MM/YY format.');
                return;
            }
            
            if (cardCvv.length < 3 || cardCvv.length > 4) {
                e.preventDefault();
                alert('Please enter a valid CVV.');
                return;
            }
            
            // Show loading state
            const submitBtn = document.querySelector('.btn-book');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Payment...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
