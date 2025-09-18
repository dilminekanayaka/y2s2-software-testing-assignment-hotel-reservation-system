<?php
require_once '../config.php';
require_once '../email_functions.php';

// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin/login.php");
    exit;
}

$error = '';
$success = '';

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_booking_status'])) {
        $booking_id = $_POST['booking_id'];
        $new_status = $_POST['status'];
        
        // Debug: Log the form submission
        error_log("Admin status update attempt: booking_id=$booking_id, status=$new_status");
        
        try {
            $stmt = $pdo->prepare("UPDATE reservations SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $booking_id]);
            
            // Send status update email to user
            try {
                // Get user and booking details for email
                $stmt = $pdo->prepare("
                    SELECT r.*, u.email as user_email, u.first_name, u.last_name, h.name as hotel_name, rt.type_name as room_type_name 
                    FROM reservations r 
                    JOIN users u ON r.user_id = u.id 
                    JOIN hotels h ON r.hotel_id = h.id 
                    JOIN room_types rt ON r.room_type_id = rt.id 
                    WHERE r.id = ?
                ");
                $stmt->execute([$booking_id]);
                $booking_data = $stmt->fetch();
                
                if ($booking_data) {
                    $user_name = $booking_data['first_name'] . ' ' . $booking_data['last_name'];
                    
                    $booking_details = [
                        'booking_id' => $booking_data['booking_reference'],
                        'hotel_name' => $booking_data['hotel_name'],
                        'room_type' => $booking_data['room_type_name'],
                        'check_in' => $booking_data['check_in_date'],
                        'check_out' => $booking_data['check_out_date']
                    ];
                    
                    $email_sent = sendBookingStatusUpdateEmail($booking_data['user_email'], $user_name, $booking_details, $new_status);
                    
                    if ($email_sent) {
                        error_log("Status update email sent successfully to: " . $booking_data['user_email']);
                    } else {
                        error_log("Failed to send status update email to: " . $booking_data['user_email']);
                    }
                }
            } catch (Exception $email_error) {
                error_log("Email error: " . $email_error->getMessage());
                // Don't fail the status update if email fails
            }
            
            $success = "Booking status updated successfully!";
            error_log("Admin status update successful: booking_id=$booking_id, status=$new_status");
        } catch (PDOException $e) {
            $error = "Error updating booking: " . $e->getMessage();
            error_log("Admin status update error: " . $e->getMessage());
        }
    }
}

try {
    // Get all reservations
    $stmt = $pdo->prepare("
        SELECT r.*, rt.type_name, rt.base_price, h.name as hotel_name, h.city, h.country,
               u.first_name, u.last_name, u.email, u.phone
        FROM reservations r
        JOIN room_types rt ON r.room_type_id = rt.id
        JOIN hotels h ON rt.hotel_id = h.id
        JOIN users u ON r.user_id = u.id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get statistics
    $stats = [];
    
    // Total reservations
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM reservations");
    $stats['total_reservations'] = $stmt->fetch()['total'];
    
    // Confirmed reservations
    $stmt = $pdo->query("SELECT COUNT(*) as confirmed FROM reservations WHERE status = 'confirmed'");
    $stats['confirmed_reservations'] = $stmt->fetch()['confirmed'];
    
    // Total revenue
    $stmt = $pdo->query("SELECT SUM(total_amount) as revenue FROM reservations WHERE status = 'confirmed'");
    $stats['total_revenue'] = $stmt->fetch()['revenue'] ?? 0;
    
    // Recent bookings (last 7 days)
    $stmt = $pdo->query("SELECT COUNT(*) as recent FROM reservations WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stats['recent_bookings'] = $stmt->fetch()['recent'];
    
    // Pending bookings
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM reservations WHERE status = 'pending'");
    $stats['pending_bookings'] = $stmt->fetch()['pending'];
    
    // Cancelled bookings
    $stmt = $pdo->query("SELECT COUNT(*) as cancelled FROM reservations WHERE status = 'cancelled'");
    $stats['cancelled_bookings'] = $stmt->fetch()['cancelled'];
    
    // Average booking value
    $stmt = $pdo->query("SELECT AVG(total_amount) as avg_value FROM reservations WHERE status = 'confirmed'");
    $stats['avg_booking_value'] = $stmt->fetch()['avg_value'] ?? 0;
    
} catch (PDOException $e) {
    $error = "Error loading data: " . $e->getMessage();
    $reservations = [];
    $stats = ['total_reservations' => 0, 'confirmed_reservations' => 0, 'total_revenue' => 0, 'recent_bookings' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-dashboard {
            padding: 8rem 5% 5rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .dashboard-container {
            max-width: 1400px;
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .stat-icon.bookings { color: #3498db; }
        .stat-icon.confirmed { color: #27ae60; }
        .stat-icon.pending { color: #f39c12; }
        .stat-icon.revenue { color: #e74c3c; }
        .stat-icon.avg-value { color: #9b59b6; }
        .stat-icon.recent { color: #1abc9c; }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #666;
            font-size: 1.1rem;
        }
        
        .quick-actions-section {
            margin-bottom: 3rem;
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .quick-action-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .quick-action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            color: inherit;
        }
        
        .quick-action-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }
        
        .quick-action-card:nth-child(1) .quick-action-icon {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }
        
        .quick-action-card:nth-child(2) .quick-action-icon {
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
        }
        
        .quick-action-card:nth-child(3) .quick-action-icon {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }
        
        .quick-action-card:nth-child(4) .quick-action-icon {
            background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);
        }
        
        .quick-action-content h3 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.2rem;
        }
        
        .quick-action-content p {
            color: #666;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .quick-action-content .count {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
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
        
        .btn-update {
            background: #3498db;
            color: white;
        }
        
        .btn-update:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-view {
            background: #9b59b6;
            color: white;
        }
        
        .btn-view:hover {
            background: #8e44ad;
            transform: translateY(-2px);
        }
        
        .status-select {
            padding: 0.5rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 0.9rem;
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
            .admin-dashboard {
                padding: 6rem 2% 3rem;
            }
            
            .dashboard-header h1 {
                font-size: 2.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
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
        <a href="dashboard.php" class="logo">Flower Garden - Admin</a>
        <nav class="navbar">
            <a href="dashboard.php" class="active">dashboard</a>
            <a href="manage_admins.php">admins</a>
            <a href="manage_rooms.php">rooms</a>
            <a href="manage_reservations.php">reservations</a>
            <a href="logout.php">logout</a>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <section class="admin-dashboard">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>Hotel Management Dashboard</h1>
                <p>Welcome to Ella Flower Garden Administration Panel</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon bookings">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-number"><?= $stats['total_reservations'] ?></div>
                    <div class="stat-label">Total Reservations</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon confirmed">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number"><?= $stats['confirmed_reservations'] ?></div>
                    <div class="stat-label">Confirmed Bookings</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?= $stats['pending_bookings'] ?></div>
                    <div class="stat-label">Pending Bookings</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-number">$<?= number_format($stats['total_revenue'], 2) ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon avg-value">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-number">$<?= number_format($stats['avg_booking_value'], 2) ?></div>
                    <div class="stat-label">Avg Booking Value</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon recent">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="stat-number"><?= $stats['recent_bookings'] ?></div>
                    <div class="stat-label">Recent Bookings (7 days)</div>
                </div>
            </div>

            <!-- Quick Actions Section -->
            <div class="quick-actions-section">
                <h2 class="section-title">
                    <i class="fas fa-bolt"></i>
                    Quick Actions
                </h2>
                
                <div class="quick-actions-grid">
                    <a href="manage_reservations.php?status=pending" class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="quick-action-content">
                            <h3>Pending Bookings</h3>
                            <p>Review and confirm pending reservations</p>
                            <span class="count"><?= $stats['pending_bookings'] ?> pending</span>
                        </div>
                    </a>
                    
                    <a href="manage_reservations.php" class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="quick-action-content">
                            <h3>All Reservations</h3>
                            <p>Manage all guest reservations</p>
                            <span class="count"><?= $stats['total_reservations'] ?> total</span>
                        </div>
                    </a>
                    
                    <a href="manage_rooms.php" class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-bed"></i>
                        </div>
                        <div class="quick-action-content">
                            <h3>Room Management</h3>
                            <p>Manage room types and availability</p>
                            <span class="count">5 room types</span>
                        </div>
                    </a>
                    
                    <a href="manage_admins.php" class="quick-action-card">
                        <div class="quick-action-icon">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="quick-action-content">
                            <h3>Admin Management</h3>
                            <p>Manage admin users and permissions</p>
                            <span class="count">Admin Portal</span>
                        </div>
                    </a>
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
                    Guest Reservations Management
                </h2>

                <?php if (empty($reservations)): ?>
                    <div class="no-reservations">
                        <i class="fas fa-bed"></i>
                        <h3>No Guest Reservations</h3>
                        <p>No bookings have been made yet. Monitor this dashboard for incoming reservations from guests.</p>
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
                                            <div class="detail-label">Customer</div>
                                            <div class="detail-value"><?= htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']) ?></div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-label">Email</div>
                                            <div class="detail-value"><?= htmlspecialchars($reservation['email']) ?></div>
                                        </div>
                                        
                                        <div class="detail-item">
                                            <div class="detail-label">Phone</div>
                                            <div class="detail-value"><?= htmlspecialchars($reservation['phone'] ?: 'Not provided') ?></div>
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
                                        
                                        <div class="detail-item">
                                            <div class="detail-label">Booking Date</div>
                                            <div class="detail-value"><?= date('M j, Y g:i A', strtotime($reservation['created_at'])) ?></div>
                                        </div>
                                    </div>
                                    
                                    <div class="reservation-actions">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="booking_id" value="<?= $reservation['id'] ?>">
                                            <select name="status" class="status-select">
                                                <option value="pending" <?= $reservation['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="confirmed" <?= $reservation['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                <option value="cancelled" <?= $reservation['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_booking_status" class="btn btn-update">
                                                <i class="fas fa-save"></i> Update Status
                                            </button>
                                        </form>
                                        
                                        <a href="reservation_details.php?id=<?= $reservation['id'] ?>" class="btn btn-view">
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

    <script src="../script.js"></script>
</body>
</html>
