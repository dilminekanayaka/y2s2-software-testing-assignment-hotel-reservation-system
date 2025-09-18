<?php
require_once 'auth.php';
require_once 'config.php';

// Redirect if not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}

$reservation_id = $_GET['id'] ?? '';
$reservation = null;
$hotel = null;
$room_type = null;
$error = '';

if ($reservation_id) {
    try {
        // Get reservation details with hotel and room type info
        $stmt = $pdo->prepare("
            SELECT r.*, h.name as hotel_name, h.city, h.country, h.address, h.phone as hotel_phone, h.email as hotel_email,
                   rt.type_name, rt.description as room_description, rt.bed_type, rt.room_size, rt.amenities, rt.images,
                   u.first_name, u.last_name, u.email as user_email, u.phone as user_phone,
                   c.name as coupon_name, c.description as coupon_description
            FROM reservations r
            INNER JOIN hotels h ON r.hotel_id = h.id
            INNER JOIN room_types rt ON r.room_type_id = rt.id
            INNER JOIN users u ON r.user_id = u.id
            LEFT JOIN coupons c ON r.coupon_id = c.id
            WHERE r.id = ? AND r.user_id = ?
        ");
        $stmt->execute([$reservation_id, $_SESSION['user_id']]);
        $reservation = $stmt->fetch();
        
        // Get payment details if reservation exists
        if ($reservation) {
            $stmt = $pdo->prepare("
                SELECT * FROM payments 
                WHERE reservation_id = ? 
                ORDER BY payment_date DESC 
                LIMIT 1
            ");
            $stmt->execute([$reservation_id]);
            $payment = $stmt->fetch();
        }
        
        if (!$reservation) {
            $error = "Reservation not found.";
        }
        
    } catch (PDOException $e) {
        $error = "Error loading reservation: " . $e->getMessage();
    }
} else {
    $error = "Invalid reservation ID.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .confirmation-section {
            padding: 8rem 5% 5rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .confirmation-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .confirmation-header h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .confirmation-header p {
            color: #666;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        
        .booking-reference-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 15px;
            font-size: 1.1rem;
            margin-top: 1rem;
            display: inline-block;
        }
        
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        
        .confirmation-card {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .booking-reference {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .reference-number {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .reference-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .booking-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .detail-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e1e5e9;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            padding: 0.5rem 0;
        }
        
        .detail-label {
            color: #666;
            font-weight: 500;
        }
        
        .detail-value {
            color: #333;
            font-weight: 600;
        }
        
        .hotel-info {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .hotel-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .hotel-location {
            color: #666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .hotel-address {
            color: #666;
            font-size: 0.9rem;
        }
        
        .booking-summary {
            background: #f0f2ff;
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        
        .summary-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            color: #667eea;
            border-top: 2px solid #667eea;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        
        .discount-item {
            color: #28a745;
        }
        
        .discount-amount {
            color: #28a745;
            font-weight: 600;
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
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }
        
        .btn-secondary:hover {
            background: #667eea;
            color: white;
        }
        
        .btn-tertiary {
            background: #17a2b8;
            color: white;
        }
        
        .btn-tertiary:hover {
            background: #138496;
            transform: translateY(-2px);
        }
        
        .amenities-section {
            margin-top: 1rem;
        }
        
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .amenity-tag {
            background: #f0f2ff;
            color: #667eea;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-paid {
            background: #d1ecf1;
            color: #0c5460;
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
        
        @media (max-width: 768px) {
            .confirmation-section {
                padding: 6rem 2% 3rem;
            }
            
            .confirmation-header h1 {
                font-size: 2rem;
            }
            
            .confirmation-card {
                padding: 2rem;
            }
            
            .booking-details {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
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

    <section class="confirmation-section">
        <div class="confirmation-container">
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php elseif ($reservation): ?>
                <div class="confirmation-header">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1>Booking Confirmed!</h1>
                    <p>Your reservation at <?= htmlspecialchars($reservation['hotel_name']) ?> has been successfully confirmed.</p>
                    <div class="booking-reference-header">
                        <strong>Booking Reference:</strong> #<?= str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) ?>
                    </div>
                </div>

                <div class="confirmation-card">
                    <div class="booking-reference">
                        <div class="reference-number"><?= htmlspecialchars($reservation['booking_reference']) ?></div>
                        <div class="reference-label">Booking Reference Number</div>
                    </div>

                    <div class="hotel-info">
                        <div class="hotel-name"><?= htmlspecialchars($reservation['hotel_name']) ?></div>
                        <div class="hotel-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars($reservation['city'] . ', ' . $reservation['country']) ?>
                        </div>
                        <div class="hotel-address"><?= htmlspecialchars($reservation['address']) ?></div>
                        <?php if ($reservation['hotel_phone']): ?>
                            <div class="hotel-address">
                                <i class="fas fa-phone"></i> <?= htmlspecialchars($reservation['hotel_phone']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="booking-details">
                        <div class="detail-section">
                            <h3>Reservation Details</h3>
                            <div class="detail-item">
                                <span class="detail-label">Room Type:</span>
                                <span class="detail-value"><?= htmlspecialchars($reservation['type_name']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Guests:</span>
                                <span class="detail-value"><?= $reservation['num_guests'] ?> <?= $reservation['num_guests'] === 1 ? 'Guest' : 'Guests' ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status:</span>
                                <span class="status-badge status-confirmed"><?= ucfirst($reservation['status']) ?></span>
                            </div>
                        </div>

                        <div class="detail-section">
                            <h3>Check-in & Check-out</h3>
                            <div class="detail-item">
                                <span class="detail-label">Check-in:</span>
                                <span class="detail-value"><?= date('M j, Y', strtotime($reservation['check_in_date'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Check-out:</span>
                                <span class="detail-value"><?= date('M j, Y', strtotime($reservation['check_out_date'])) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Nights:</span>
                                <span class="detail-value"><?= (new DateTime($reservation['check_in_date']))->diff(new DateTime($reservation['check_out_date']))->days ?></span>
                            </div>
                        </div>
                    </div>

                    <?php if ($reservation['room_description'] || $reservation['bed_type'] || $reservation['room_size']): ?>
                        <div class="detail-section">
                            <h3>Room Details</h3>
                            <?php if ($reservation['room_description']): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Description:</span>
                                    <span class="detail-value"><?= htmlspecialchars($reservation['room_description']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($reservation['bed_type']): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Bed Type:</span>
                                    <span class="detail-value"><?= htmlspecialchars($reservation['bed_type']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if ($reservation['room_size']): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Room Size:</span>
                                    <span class="detail-value"><?= htmlspecialchars($reservation['room_size']) ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if ($reservation['amenities']): ?>
                                <div class="amenities-section">
                                    <span class="detail-label">Amenities:</span>
                                    <div class="amenities-list">
                                        <?php 
                                        $amenities = json_decode($reservation['amenities'], true) ?? [];
                                        foreach ($amenities as $amenity): 
                                        ?>
                                            <span class="amenity-tag"><?= htmlspecialchars($amenity) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="detail-section">
                        <h3>Guest Information</h3>
                        <div class="detail-item">
                            <span class="detail-label">Guest Name:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['user_email']) ?></span>
                        </div>
                        <?php if ($reservation['user_phone']): ?>
                            <div class="detail-item">
                                <span class="detail-label">Phone:</span>
                                <span class="detail-value"><?= htmlspecialchars($reservation['user_phone']) ?></span>
                            </div>
                        <?php endif; ?>
                        <div class="detail-item">
                            <span class="detail-label">Booking Date:</span>
                            <span class="detail-value"><?= date('M j, Y g:i A', strtotime($reservation['created_at'])) ?></span>
                        </div>
                    </div>

                    <?php if (isset($payment) && $payment): ?>
                        <div class="detail-section">
                            <h3>Payment Information</h3>
                            <div class="detail-item">
                                <span class="detail-label">Payment Status:</span>
                                <span class="status-badge status-paid"><?= ucfirst($payment['payment_status']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Payment Method:</span>
                                <span class="detail-value"><?= htmlspecialchars($payment['card_name']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Card Number:</span>
                                <span class="detail-value"><?= htmlspecialchars($payment['card_number']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Expiry Date:</span>
                                <span class="detail-value"><?= htmlspecialchars($payment['card_expiry']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Amount Paid:</span>
                                <span class="detail-value">$<?= number_format($payment['amount'], 2) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Payment Date:</span>
                                <span class="detail-value"><?= date('M j, Y g:i A', strtotime($payment['payment_date'])) ?></span>
                            </div>
                            <?php if ($payment['transaction_id']): ?>
                                <div class="detail-item">
                                    <span class="detail-label">Transaction ID:</span>
                                    <span class="detail-value"><?= htmlspecialchars($payment['transaction_id']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($reservation['special_requests']): ?>
                        <div class="detail-section">
                            <h3>Special Requests</h3>
                            <p style="color: #666; font-style: italic;"><?= htmlspecialchars($reservation['special_requests']) ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="booking-summary">
                        <div class="summary-title">Booking Summary</div>
                        <div class="summary-item">
                            <span>Room Rate:</span>
                            <span>$<?= number_format($reservation['total_amount'] / (new DateTime($reservation['check_in_date']))->diff(new DateTime($reservation['check_out_date']))->days, 2) ?> per night</span>
                        </div>
                        <div class="summary-item">
                            <span>Nights:</span>
                            <span><?= (new DateTime($reservation['check_in_date']))->diff(new DateTime($reservation['check_out_date']))->days ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Subtotal:</span>
                            <span>$<?= number_format($reservation['total_amount'], 2) ?></span>
                        </div>
                        
                        <?php if ($reservation['coupon_code'] && $reservation['discount_amount'] > 0): ?>
                            <div class="summary-item discount-item">
                                <span>Coupon Discount (<?= htmlspecialchars($reservation['coupon_code']) ?>):</span>
                                <span class="discount-amount">-$<?= number_format($reservation['discount_amount'], 2) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="summary-item total-amount">
                            <span>Final Amount:</span>
                            <span>$<?= number_format($reservation['final_amount'], 2) ?></span>
                        </div>
                    </div>

                    <div class="action-buttons">
                        <a href="home.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <a href="hotels.php" class="btn btn-secondary">
                            <i class="fas fa-bed"></i> View More Hotels
                        </a>
                        <button onclick="window.print()" class="btn btn-tertiary">
                            <i class="fas fa-print"></i> Print Confirmation
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>