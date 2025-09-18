<?php
require_once '../config.php';
require_once '../email_functions.php';

// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$reservation_id = $_GET['id'] ?? '';
$error = '';
$success = '';

if (empty($reservation_id)) {
    header("Location: dashboard.php");
    exit;
}

// Handle admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $new_status = $_POST['status'];
        $admin_notes = sanitizeInput($_POST['admin_notes'] ?? '');
        
        try {
            $stmt = $pdo->prepare("UPDATE reservations SET status = ?, admin_notes = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$new_status, $admin_notes, $reservation_id]);
            
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
                $stmt->execute([$reservation_id]);
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
            }
            
            $success = "Reservation status updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating reservation: " . $e->getMessage();
        }
    }
}

try {
    // Get detailed reservation information
    $stmt = $pdo->prepare("
        SELECT 
            r.*,
            u.first_name, u.last_name, u.email, u.phone, u.address, u.date_of_birth,
            h.name as hotel_name, h.address as hotel_address, h.city, h.country, h.rating, h.description as hotel_description, h.images as hotel_images,
            rt.type_name, rt.description as room_description, rt.max_occupancy, rt.bed_type, rt.room_size, rt.amenities, rt.images,
            p.card_number, p.card_name, p.card_expiry, p.billing_address, p.amount as payment_amount, p.payment_date,
            c.code as coupon_code, c.discount_type, c.discount_value
        FROM reservations r
        JOIN users u ON r.user_id = u.id
        JOIN hotels h ON r.hotel_id = h.id
        JOIN room_types rt ON r.room_type_id = rt.id
        LEFT JOIN payments p ON r.id = p.reservation_id
        LEFT JOIN coupons c ON r.coupon_id = c.id
        WHERE r.id = ?
    ");
    
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch();
    
    if (!$reservation) {
        header("Location: dashboard.php");
        exit;
    }
    
} catch (PDOException $e) {
    $error = "Error loading reservation: " . $e->getMessage();
    $reservation = null;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details - Admin - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-details {
            padding: 8rem 5% 5rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .details-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .details-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .details-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .back-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .details-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .card-title i {
            color: #667eea;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
        }
        
        .detail-value {
            color: #333;
            text-align: right;
            flex: 1;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-confirmed { background: #d4edda; color: #155724; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .status-completed { background: #d1ecf1; color: #0c5460; }
        
        .admin-actions {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .status-form {
            display: flex;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        
        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }
        
        .amenity-tag {
            background: #e3f2fd;
            color: #1976d2;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .room-images {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        
        .room-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid #e1e5e9;
        }
        
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
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
        
        @media (max-width: 768px) {
            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .details-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .status-form {
                flex-direction: column;
            }
            
            .form-group {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <section class="header">
        <a href="dashboard.php" class="logo">Flower Garden - Admin</a>
        <nav class="navbar">
            <a href="dashboard.php">dashboard</a>
            <a href="manage_admins.php">admins</a>
            <a href="manage_rooms.php">rooms</a>
            <a href="manage_reservations.php">reservations</a>
            <a href="logout.php">logout</a>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <section class="admin-details">
        <div class="details-container">
            <div class="details-header">
                <h1 class="details-title">Reservation Details</h1>
                <a href="dashboard.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if ($reservation): ?>
                <!-- Admin Actions -->
                <div class="admin-actions">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Admin Actions
                    </h3>
                    <form method="POST" class="status-form">
                        <div class="form-group">
                            <label for="status">Update Status</label>
                            <select name="status" id="status" required>
                                <option value="pending" <?= $reservation['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= $reservation['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="cancelled" <?= $reservation['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                <option value="completed" <?= $reservation['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="admin_notes">Admin Notes</label>
                            <textarea name="admin_notes" id="admin_notes" placeholder="Add internal notes about this reservation..."><?= htmlspecialchars($reservation['admin_notes'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" name="update_status" class="btn-update">
                            <i class="fas fa-save"></i> Update Status
                        </button>
                    </form>
                </div>

                <div class="details-grid">
                    <!-- Reservation Information -->
                    <div class="details-card">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-check"></i> Reservation Information
                        </h3>
                        <div class="detail-row">
                            <span class="detail-label">Booking ID:</span>
                            <span class="detail-value">#<?= str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Reference:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['booking_reference']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                <span class="status-badge status-<?= $reservation['status'] ?>">
                                    <?= ucfirst($reservation['status']) ?>
                                </span>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Check-in:</span>
                            <span class="detail-value"><?= date('M j, Y', strtotime($reservation['check_in_date'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Check-out:</span>
                            <span class="detail-value"><?= date('M j, Y', strtotime($reservation['check_out_date'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Nights:</span>
                            <span class="detail-value"><?= (new DateTime($reservation['check_in_date']))->diff(new DateTime($reservation['check_out_date']))->days ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Guests:</span>
                            <span class="detail-value"><?= $reservation['num_guests'] ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Created:</span>
                            <span class="detail-value"><?= date('M j, Y g:i A', strtotime($reservation['created_at'])) ?></span>
                        </div>
                        <?php if ($reservation['updated_at']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Last Updated:</span>
                            <span class="detail-value"><?= date('M j, Y g:i A', strtotime($reservation['updated_at'])) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Guest Information -->
                    <div class="details-card">
                        <h3 class="card-title">
                            <i class="fas fa-user"></i> Guest Information
                        </h3>
                        <div class="detail-row">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['first_name'] . ' ' . $reservation['last_name']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['email']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['phone'] ?? 'Not provided') ?></span>
                        </div>
                        <?php if ($reservation['address']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Address:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['address']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($reservation['date_of_birth']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Date of Birth:</span>
                            <span class="detail-value"><?= date('M j, Y', strtotime($reservation['date_of_birth'])) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($reservation['special_requests']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Special Requests:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['special_requests']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Hotel Information -->
                    <div class="details-card">
                        <h3 class="card-title">
                            <i class="fas fa-hotel"></i> Hotel Information
                        </h3>
                        <div class="detail-row">
                            <span class="detail-label">Hotel:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['hotel_name']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Location:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['city'] . ', ' . $reservation['country']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Address:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['hotel_address']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Rating:</span>
                            <span class="detail-value">
                                <?= $reservation['rating'] ?>/5 
                                <i class="fas fa-star" style="color: #ffc107;"></i>
                            </span>
                        </div>
                        <?php if ($reservation['hotel_description']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Description:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['hotel_description']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($reservation['hotel_images']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Hotel Images:</span>
                            <span class="detail-value">
                                <div class="room-images">
                                    <?php 
                                    $hotel_images = json_decode($reservation['hotel_images'], true);
                                    if ($hotel_images && is_array($hotel_images)):
                                        foreach ($hotel_images as $image):
                                            if (!empty($image)):
                                    ?>
                                        <img src="<?= htmlspecialchars($image) ?>" alt="Hotel Image" class="room-image" onerror="this.style.display='none'">
                                    <?php 
                                            endif;
                                        endforeach;
                                    else:
                                    ?>
                                        <p style="color: #666; font-style: italic;">No hotel images available</p>
                                    <?php endif; ?>
                                </div>
                            </span>
                        </div>
                        <?php else: ?>
                        <div class="detail-row">
                            <span class="detail-label">Hotel Images:</span>
                            <span class="detail-value">
                                <p style="color: #666; font-style: italic;">No hotel images available</p>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Room Information -->
                    <div class="details-card">
                        <h3 class="card-title">
                            <i class="fas fa-bed"></i> Room Information
                        </h3>
                        <div class="detail-row">
                            <span class="detail-label">Room Type:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['type_name']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Max Occupancy:</span>
                            <span class="detail-value"><?= $reservation['max_occupancy'] ?> guests</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Bed Type:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['bed_type']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Room Size:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['room_size']) ?></span>
                        </div>
                        <?php if ($reservation['amenities']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Amenities:</span>
                            <span class="detail-value">
                                <div class="amenities-list">
                                    <?php 
                                    $amenities = json_decode($reservation['amenities'], true);
                                    if ($amenities):
                                        foreach ($amenities as $amenity):
                                    ?>
                                        <span class="amenity-tag"><?= htmlspecialchars($amenity) ?></span>
                                    <?php 
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if ($reservation['images']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Room Images:</span>
                            <span class="detail-value">
                                <div class="room-images">
                                    <?php 
                                    $images = json_decode($reservation['images'], true);
                                    if ($images && is_array($images)):
                                        foreach ($images as $image):
                                            if (!empty($image)):
                                    ?>
                                        <img src="<?= htmlspecialchars($image) ?>" alt="Room Image" class="room-image" onerror="this.style.display='none'">
                                    <?php 
                                            endif;
                                        endforeach;
                                    else:
                                    ?>
                                        <p style="color: #666; font-style: italic;">No room images available</p>
                                    <?php endif; ?>
                                </div>
                            </span>
                        </div>
                        <?php else: ?>
                        <div class="detail-row">
                            <span class="detail-label">Room Images:</span>
                            <span class="detail-value">
                                <p style="color: #666; font-style: italic;">No room images available</p>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Payment Information -->
                    <div class="details-card">
                        <h3 class="card-title">
                            <i class="fas fa-credit-card"></i> Payment Information
                        </h3>
                        <div class="detail-row">
                            <span class="detail-label">Payment Status:</span>
                            <span class="detail-value">
                                <span class="status-badge status-<?= $reservation['payment_status'] ?>">
                                    <?= ucfirst($reservation['payment_status']) ?>
                                </span>
                            </span>
                        </div>
                        <?php if ($reservation['card_name']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Cardholder:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['card_name']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($reservation['card_number']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Card Number:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['card_number']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($reservation['card_expiry']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Expiry:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['card_expiry']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if ($reservation['billing_address']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Billing Address:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['billing_address']) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="detail-row">
                            <span class="detail-label">Amount Paid:</span>
                            <span class="detail-value">$<?= number_format($reservation['payment_amount'], 2) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Payment Date:</span>
                            <span class="detail-value"><?= date('M j, Y g:i A', strtotime($reservation['payment_date'])) ?></span>
                        </div>
                    </div>

                    <!-- Pricing Information -->
                    <div class="details-card">
                        <h3 class="card-title">
                            <i class="fas fa-dollar-sign"></i> Pricing Information
                        </h3>
                        <div class="detail-row">
                            <span class="detail-label">Base Amount:</span>
                            <span class="detail-value">$<?= number_format($reservation['total_amount'], 2) ?></span>
                        </div>
                        <?php if ($reservation['coupon_code']): ?>
                        <div class="detail-row">
                            <span class="detail-label">Coupon Used:</span>
                            <span class="detail-value"><?= htmlspecialchars($reservation['coupon_code']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Discount:</span>
                            <span class="detail-value">-$<?= number_format($reservation['discount_amount'], 2) ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="detail-row">
                            <span class="detail-label">Final Amount:</span>
                            <span class="detail-value" style="font-weight: 700; font-size: 1.2rem; color: #667eea;">
                                $<?= number_format($reservation['final_amount'], 2) ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> Reservation not found.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="../script.js"></script>
</body>
</html>
