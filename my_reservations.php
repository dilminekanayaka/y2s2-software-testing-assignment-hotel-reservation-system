<?php
require_once 'auth.php';
require_once 'config.php';

// Redirect if not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$reservations = [];
$error = '';

try {
    // Get user's reservations with hotel and room details
    $stmt = $pdo->prepare("
        SELECT 
            r.*,
            h.name as hotel_name,
            h.city,
            h.address as hotel_address,
            rt.type_name,
            rt.description as room_description,
            rt.base_price,
            rt.amenities,
            rt.images as room_images,
            c.code as coupon_code,
            c.name as coupon_name
        FROM reservations r
        JOIN hotels h ON r.hotel_id = h.id
        JOIN room_types rt ON r.room_type_id = rt.id
        LEFT JOIN coupons c ON r.coupon_id = c.id
        WHERE r.user_id = ?
        ORDER BY r.check_in_date DESC, r.created_at DESC
    ");
    
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = "Error loading reservations: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Reservations - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .reservations-container {
            min-height: 100vh;
            background: #f8f9fa;
            padding: 2rem 5%;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-top: 2rem;
        }
        
        .page-header h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .page-header p {
            font-size: 1.2rem;
            color: #666;
        }
        
        .reservations-grid {
            display: grid;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .reservation-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e1e5e9;
            transition: all 0.3s ease;
        }
        
        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .reservation-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f1f3f4;
        }
        
        .reservation-info h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .reservation-info .hotel-name {
            color: #667eea;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .reservation-info .room-type {
            color: #666;
            font-size: 1rem;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-checked-in {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-checked-out {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .reservation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .detail-group {
            display: flex;
            flex-direction: column;
        }
        
        .detail-group h4 {
            font-size: 0.9rem;
            font-weight: 600;
            color: #666;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-group p {
            font-size: 1rem;
            color: #333;
            font-weight: 500;
        }
        
        .reservation-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .no-reservations {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .no-reservations i {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .no-reservations h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .no-reservations p {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
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
        
        @media (max-width: 768px) {
            .reservations-container {
                padding: 1rem 2%;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .reservation-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .reservation-details {
                grid-template-columns: 1fr;
            }
            
            .reservation-actions {
                justify-content: center;
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

    <div class="reservations-container">
        <div class="page-header">
            <h1><i class="fas fa-calendar-check"></i> My Reservations</h1>
            <p>View and manage your hotel reservations</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($reservations)): ?>
            <div class="no-reservations">
                <i class="fas fa-calendar-times"></i>
                <h3>No Reservations Yet</h3>
                <p>You haven't made any reservations yet. Start exploring our beautiful hotels and book your perfect stay!</p>
                <a href="hotels.php" class="btn btn-primary">
                    <i class="fas fa-search"></i> Browse Hotels
                </a>
            </div>
        <?php else: ?>
            <div class="reservations-grid">
                <?php foreach ($reservations as $reservation): ?>
                    <div class="reservation-card">
                        <div class="reservation-header">
                            <div class="reservation-info">
                                <h3><?= htmlspecialchars($reservation['hotel_name']) ?></h3>
                                <p class="hotel-name"><?= htmlspecialchars($reservation['city']) ?></p>
                                <p class="room-type"><?= htmlspecialchars($reservation['type_name']) ?></p>
                            </div>
                            <div class="status-badge status-<?= $reservation['status'] ?>">
                                <?= ucfirst($reservation['status']) ?>
                            </div>
                        </div>

                        <div class="reservation-details">
                            <div class="detail-group">
                                <h4><i class="fas fa-calendar-alt"></i> Check-in Date</h4>
                                <p><?= date('M d, Y', strtotime($reservation['check_in_date'])) ?></p>
                            </div>
                            <div class="detail-group">
                                <h4><i class="fas fa-calendar-alt"></i> Check-out Date</h4>
                                <p><?= date('M d, Y', strtotime($reservation['check_out_date'])) ?></p>
                            </div>
                            <div class="detail-group">
                                <h4><i class="fas fa-users"></i> Guests</h4>
                                <p><?= $reservation['num_guests'] ?> guest<?= $reservation['num_guests'] > 1 ? 's' : '' ?></p>
                            </div>
                            <div class="detail-group">
                                <h4><i class="fas fa-hashtag"></i> Booking Reference</h4>
                                <p><?= htmlspecialchars($reservation['booking_reference']) ?></p>
                            </div>
                            <div class="detail-group">
                                <h4><i class="fas fa-dollar-sign"></i> Total Amount</h4>
                                <p>$<?= number_format($reservation['final_amount'], 2) ?></p>
                            </div>
                            <div class="detail-group">
                                <h4><i class="fas fa-credit-card"></i> Payment Status</h4>
                                <p class="status-<?= $reservation['payment_status'] ?>"><?= ucfirst($reservation['payment_status']) ?></p>
                            </div>
                        </div>

                        <?php if ($reservation['coupon_code']): ?>
                            <div class="detail-group" style="margin-bottom: 1rem;">
                                <h4><i class="fas fa-tag"></i> Coupon Used</h4>
                                <p><?= htmlspecialchars($reservation['coupon_code']) ?> - $<?= number_format($reservation['discount_amount'], 2) ?> discount</p>
                            </div>
                        <?php endif; ?>

                        <?php if ($reservation['special_requests']): ?>
                            <div class="detail-group" style="margin-bottom: 1rem;">
                                <h4><i class="fas fa-comment"></i> Special Requests</h4>
                                <p><?= htmlspecialchars($reservation['special_requests']) ?></p>
                            </div>
                        <?php endif; ?>

                        <div class="reservation-actions">
                            <a href="booking_confirmation.php?id=<?= $reservation['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-eye"></i> View Details
                            </a>
                            
                            <?php if ($reservation['status'] === 'confirmed' && strtotime($reservation['check_in_date']) > time()): ?>
                                <a href="cancel_reservation.php?id=<?= $reservation['id'] ?>" class="btn btn-danger" 
                                   onclick="return confirm('Are you sure you want to cancel this reservation?')">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php endif; ?>
                            
                            <a href="hotels.php" class="btn btn-secondary">
                                <i class="fas fa-plus"></i> Book Another
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="script.js"></script>
</body>
</html>
