<?php
require_once 'auth.php';
require_once 'config.php';
require_once 'email_functions.php';

// Redirect if not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}

$reservation_id = $_GET['id'] ?? '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Verify the reservation belongs to the current user
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
        $stmt->execute([$reservation_id, $_SESSION['user_id']]);
        $reservation = $stmt->fetch();
        
        if (!$reservation) {
            throw new Exception("Reservation not found or access denied.");
        }
        
        // Check if reservation can be cancelled (not already cancelled and check-in is in the future)
        if ($reservation['status'] === 'cancelled') {
            throw new Exception("This reservation is already cancelled.");
        }
        
        if (strtotime($reservation['check_in_date']) <= time()) {
            throw new Exception("Cannot cancel reservation on or after check-in date.");
        }
        
        // Update reservation status to cancelled
        $stmt = $pdo->prepare("UPDATE reservations SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
        $stmt->execute([$reservation_id]);
        
        // Send cancellation email
        try {
            // Get hotel and room details for email
            $stmt = $pdo->prepare("
                SELECT r.*, h.name as hotel_name, rt.type_name as room_type_name 
                FROM reservations r 
                JOIN hotels h ON r.hotel_id = h.id 
                JOIN room_types rt ON r.room_type_id = rt.id 
                WHERE r.id = ?
            ");
            $stmt->execute([$reservation_id]);
            $booking_data = $stmt->fetch();
            
            $booking_details = [
                'booking_id' => $booking_data['booking_reference'],
                'hotel_name' => $booking_data['hotel_name'],
                'room_type' => $booking_data['room_type_name'],
                'check_in' => $booking_data['check_in_date'],
                'check_out' => $booking_data['check_out_date'],
                'refund_amount' => $booking_data['final_amount'] // Full refund for cancellations before check-in
            ];
            
            $email_sent = sendBookingCancellationEmail($_SESSION['user_email'], $_SESSION['user_name'], $booking_details);
            
            if ($email_sent) {
                error_log("Cancellation email sent successfully to: " . $_SESSION['user_email']);
            } else {
                error_log("Failed to send cancellation email to: " . $_SESSION['user_email']);
            }
        } catch (Exception $email_error) {
            error_log("Email error: " . $email_error->getMessage());
            // Don't fail the cancellation if email fails
        }
        
        $success = "Reservation cancelled successfully. You will receive a refund confirmation via email.";
        
        // Redirect to my reservations page after 3 seconds
        header("refresh:3;url=my_reservations.php");
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
} else {
    // GET request - show confirmation page
    try {
        $stmt = $pdo->prepare("
            SELECT r.*, h.name as hotel_name, h.city, rt.type_name
            FROM reservations r
            JOIN hotels h ON r.hotel_id = h.id
            JOIN room_types rt ON r.room_type_id = rt.id
            WHERE r.id = ? AND r.user_id = ?
        ");
        $stmt->execute([$reservation_id, $_SESSION['user_id']]);
        $reservation = $stmt->fetch();
        
        if (!$reservation) {
            $error = "Reservation not found or access denied.";
        }
        
    } catch (Exception $e) {
        $error = "Error loading reservation: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancel Reservation - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .cancel-container {
            min-height: 100vh;
            background: #f8f9fa;
            padding: 2rem 5%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .cancel-card {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        
        .cancel-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .cancel-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .cancel-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .reservation-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .reservation-summary h3 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 1.3rem;
        }
        
        .summary-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-item label {
            font-size: 0.9rem;
            color: #666;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        
        .detail-item span {
            font-size: 1rem;
            color: #333;
            font-weight: 500;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 500;
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
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 1rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .user-welcome {
            color: #667eea;
            font-weight: 600;
            font-size: 0.9rem;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            background: rgba(102, 126, 234, 0.1);
            border-radius: 20px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
        
        @media (max-width: 768px) {
            .cancel-container {
                padding: 1rem 2%;
            }
            
            .cancel-card {
                padding: 2rem;
            }
            
            .cancel-header h1 {
                font-size: 2rem;
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
                <a href="my_reservations.php">my reservations</a>
                <span class="user-welcome">Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="logout.php">logout</a>
            <?php else: ?>
                <a href="login.php">login</a>
                <a href="register.php">register</a>
            <?php endif; ?>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <div class="cancel-container">
        <div class="cancel-card">
            <div class="cancel-header">
                <h1><i class="fas fa-times-circle"></i> Cancel Reservation</h1>
                <p>Please review your reservation details before cancelling</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    <br><small>Redirecting to your reservations...</small>
                </div>
            <?php endif; ?>

            <?php if ($reservation && !$success): ?>
                <div class="reservation-summary">
                    <h3><i class="fas fa-calendar-check"></i> Reservation Details</h3>
                    <div class="summary-details">
                        <div class="detail-item">
                            <label>Hotel</label>
                            <span><?= htmlspecialchars($reservation['hotel_name']) ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Location</label>
                            <span><?= htmlspecialchars($reservation['city']) ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Room Type</label>
                            <span><?= htmlspecialchars($reservation['type_name']) ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Check-in</label>
                            <span><?= date('M d, Y', strtotime($reservation['check_in_date'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Check-out</label>
                            <span><?= date('M d, Y', strtotime($reservation['check_out_date'])) ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Guests</label>
                            <span><?= $reservation['num_guests'] ?> guest<?= $reservation['num_guests'] > 1 ? 's' : '' ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Total Amount</label>
                            <span>$<?= number_format($reservation['final_amount'], 2) ?></span>
                        </div>
                        <div class="detail-item">
                            <label>Booking Reference</label>
                            <span><?= htmlspecialchars($reservation['booking_reference']) ?></span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Important:</strong> Cancelling this reservation will process a full refund to your original payment method. 
                    The refund may take 3-5 business days to appear in your account.
                </div>

                <form method="POST" action="">
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this reservation? This action cannot be undone.')">
                            <i class="fas fa-times"></i> Yes, Cancel Reservation
                        </button>
                        <a href="my_reservations.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Reservations
                        </a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
