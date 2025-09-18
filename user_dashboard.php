<?php
require_once 'auth.php';
require_once 'config.php';

// Redirect if not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user = getUserData();
$error = '';
$success = '';

// Handle cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    try {
        // Check if booking belongs to user
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $user['id']]);
        $booking = $stmt->fetch();
        
        if ($booking) {
            // Update booking status to cancelled
            $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled' WHERE id = ?");
            $stmt->execute([$booking_id]);
            $success = "Booking cancelled successfully!";
        } else {
            $error = "Booking not found or you don't have permission to cancel it.";
        }
    } catch (PDOException $e) {
        $error = "Error cancelling booking: " . $e->getMessage();
    }
}

try {
    // Get user's reservations
    $stmt = $pdo->prepare("
        SELECT r.*, rt.type_name, rt.base_price, h.name as hotel_name, h.city, h.country
        FROM reservations r
        JOIN room_types rt ON r.room_type_id = rt.id
        JOIN hotels h ON rt.hotel_id = h.id
        WHERE r.user_id = ?
        ORDER BY r.check_in_date DESC
    ");
    $stmt->execute([$user['id']]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    $error = "Error loading reservations: " . $e->getMessage();
    $reservations = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - Ella Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .dashboard-section {
            padding: 8rem 5% 5rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .dashboard-header h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .welcome-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            text-align: center;
        }
        
        .welcome-card h2 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .welcome-card p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .user-info {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
            margin-top: 1rem;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
        }
        
        .info-item i {
            color: #667eea;
        }
        
        .reservations-section {
            margin-bottom: 3rem;
        }
        
        .section-title {
            font-size: 2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .section-title i {
            color: #667eea;
        }
        
        .reservations-grid {
            display: grid;
            gap: 2rem;
        }
        
        .reservation-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .reservation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .reservation-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .reservation-id {
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .reservation-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .status-confirmed {
            background: rgba(46, 204, 113, 0.2);
            color: #27ae60;
        }
        
        .status-cancelled {
            background: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
        }
        
        .status-pending {
            background: rgba(241, 196, 15, 0.2);
            color: #f39c12;
        }
        
        .reservation-content {
            padding: 2rem;
        }
        
        .reservation-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .detail-label {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
        }
        
        .detail-value {
            color: #666;
            font-size: 1rem;
        }
        
        .reservation-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .btn-cancel {
            background: #e74c3c;
            color: white;
        }
        
        .btn-cancel:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .btn-view {
            background: #3498db;
            color: white;
        }
        
        .btn-view:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-book {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-book:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
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
            color: #333;
            margin-bottom: 1rem;
        }
        
        .no-reservations p {
            color: #666;
            margin-bottom: 2rem;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .dashboard-section {
                padding: 6rem 2% 3rem;
            }
            
            .dashboard-header h1 {
                font-size: 2.5rem;
            }
            
            .user-info {
                flex-direction: column;
                gap: 1rem;
            }
            
            .reservation-header {
                flex-direction: column;
                text-align: center;
            }
            
            .reservation-details {
                grid-template-columns: 1fr;
            }
            
            .reservation-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <section class="header">
        <a href="home.php" class="logo">Ella Flower Garden</a>
        <nav class="navbar">
            <a href="home.php">home</a>
            <a href="hotels.php">hotels</a>
            <a href="user_dashboard.php" class="active">dashboard</a>
            <a href="logout.php">logout</a>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <section class="dashboard-section">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>My Dashboard</h1>
            </div>

            <div class="welcome-card">
                <h2>Welcome back, <?= htmlspecialchars($user['first_name']) ?>!</h2>
                <p>Manage your reservations and explore our luxury accommodations</p>
                
                <div class="user-info">
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <span><?= htmlspecialchars($user['email']) ?></span>
                    </div>
                    <?php if ($user['phone']): ?>
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span><?= htmlspecialchars($user['phone']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Member since <?= date('M Y', strtotime($user['created_at'])) ?></span>
                    </div>
                </div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <div class="reservations-section">
                <h2 class="section-title">
                    <i class="fas fa-calendar-check"></i>
                    My Reservations
                </h2>

                <?php if (empty($reservations)): ?>
                    <div class="no-reservations">
                        <i class="fas fa-bed"></i>
                        <h3>No Reservations Yet</h3>
                        <p>You haven't made any reservations yet. Start exploring our luxury rooms!</p>
                        <a href="hotels.php" class="btn btn-book">
                            <i class="fas fa-search"></i> View Available Hotels
                        </a>
                    </div>
                <?php else: ?>
                    <div class="reservations-grid">
                        <?php foreach ($reservations as $reservation): ?>
                            <div class="reservation-card">
                                <div class="reservation-header">
                                    <div class="reservation-id">
                                        Booking #<?= str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </div>
                                    <div class="reservation-status status-<?= $reservation['status'] ?>">
                                        <?= ucfirst($reservation['status']) ?>
                                    </div>
                                </div>
                                
                                <div class="reservation-content">
                                    <div class="reservation-details">
                                        <div class="detail-item">
                                            <div class="detail-label">Hotel</div>
                                            <div class="detail-value"><?= htmlspecialchars($reservation['hotel_name']) ?></div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-label">Room Type</div>
                                            <div class="detail-value"><?= htmlspecialchars($reservation['type_name']) ?></div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-label">Check-in</div>
                                            <div class="detail-value"><?= date('M j, Y', strtotime($reservation['check_in_date'])) ?></div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-label">Check-out</div>
                                            <div class="detail-value"><?= date('M j, Y', strtotime($reservation['check_out_date'])) ?></div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-label">Guests</div>
                                            <div class="detail-value"><?= $reservation['num_guests'] ?> <?= $reservation['num_guests'] === 1 ? 'Guest' : 'Guests' ?></div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-label">Total Amount</div>
                                            <div class="detail-value">$<?= number_format($reservation['total_amount'], 2) ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="reservation-actions">
                                        <?php if ($reservation['status'] === 'confirmed' && strtotime($reservation['check_in_date']) > time()): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="booking_id" value="<?= $reservation['id'] ?>">
                                                <button type="submit" name="cancel_booking" class="btn btn-cancel" 
                                                        onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                    <i class="fas fa-times"></i> Cancel Booking
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <a href="booking_confirmation.php?id=<?= $reservation['id'] ?>" class="btn btn-view">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>
