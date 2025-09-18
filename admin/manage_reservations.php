<?php
require_once '../config.php';

// Check if admin is logged in
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_reservation'])) {
        $id = $_POST['reservation_id'];
        $status = $_POST['status'];
        $check_in = $_POST['check_in_date'];
        $check_out = $_POST['check_out_date'];
        $num_guests = $_POST['num_guests'];
        $total_amount = $_POST['total_amount'];
        $special_requests = sanitizeInput($_POST['special_requests']);
        
        try {
            $stmt = $pdo->prepare("UPDATE reservations SET status = ?, check_in_date = ?, check_out_date = ?, num_guests = ?, total_amount = ?, special_requests = ? WHERE id = ?");
            $stmt->execute([$status, $check_in, $check_out, $num_guests, $total_amount, $special_requests, $id]);
            $success = "Reservation updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating reservation: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['delete_reservation'])) {
        $id = $_POST['reservation_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Reservation deleted successfully!";
        } catch (PDOException $e) {
            $error = "Error deleting reservation: " . $e->getMessage();
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';

// Build query with filters
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "r.status = ?";
    $params[] = $status_filter;
}

if ($date_filter) {
    $where_conditions[] = "r.check_in_date >= ?";
    $params[] = $date_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get all reservations with filters
try {
    $sql = "
        SELECT r.*, rt.type_name, rt.base_price, h.name as hotel_name, h.city, h.country,
               u.first_name, u.last_name, u.email, u.phone
        FROM reservations r
        JOIN room_types rt ON r.room_type_id = rt.id
        JOIN hotels h ON rt.hotel_id = h.id
        JOIN users u ON r.user_id = u.id
        $where_clause
        ORDER BY r.created_at DESC
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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
    <title>Manage Reservations - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-portal {
            padding: 8rem 5% 5rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .portal-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .portal-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .portal-header h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .portal-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        
        .nav-btn {
            padding: 1rem 2rem;
            background: white;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .nav-btn:hover, .nav-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
            text-decoration: none;
        }
        
        .filters-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            align-items: end;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .form-group input,
        .form-group select {
            padding: 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
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
        
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: #f39c12;
            color: white;
        }
        
        .btn-warning:hover {
            background: #e67e22;
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
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background: white;
            margin: 2% auto;
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .close {
            font-size: 2rem;
            cursor: pointer;
            color: #999;
        }
        
        .close:hover {
            color: #333;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .form-group textarea {
            padding: 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            min-height: 100px;
            resize: vertical;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        @media (max-width: 768px) {
            .admin-portal {
                padding: 6rem 2% 3rem;
            }
            
            .portal-header h1 {
                font-size: 2.5rem;
            }
            
            .portal-nav {
                flex-direction: column;
            }
            
            .filters-grid {
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
            <a href="dashboard.php">dashboard</a>
            <a href="manage_admins.php">admins</a>
            <a href="manage_rooms.php">rooms</a>
            <a href="manage_reservations.php" class="active">reservations</a>
            <a href="logout.php">logout</a>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <section class="admin-portal">
        <div class="portal-container">
            <div class="portal-header">
                <h1>Reservation Management Portal</h1>
            </div>

            <div class="portal-nav">
                <a href="dashboard.php" class="nav-btn">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="manage_admins.php" class="nav-btn">
                    <i class="fas fa-users-cog"></i> Admins
                </a>
                <a href="manage_rooms.php" class="nav-btn">
                    <i class="fas fa-bed"></i> Rooms
                </a>
                <a href="manage_reservations.php" class="nav-btn active">
                    <i class="fas fa-calendar-check"></i> Reservations
                </a>
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

            <!-- Filters Section -->
            <div class="filters-section">
                <h2 style="margin-bottom: 1.5rem; color: #333;">
                    <i class="fas fa-filter"></i> Filter Reservations
                </h2>
                
                <form method="GET" class="filters-grid">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="confirmed" <?= $status_filter === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                            <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="date">Check-in From</label>
                        <input type="date" id="date" name="date" value="<?= htmlspecialchars($date_filter) ?>">
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Apply Filters
                        </button>
                        <a href="manage_reservations.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- Reservations List -->
            <div class="reservations-grid">
                <?php if (empty($reservations)): ?>
                    <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);">
                        <i class="fas fa-calendar-times" style="font-size: 4rem; color: #667eea; margin-bottom: 1rem;"></i>
                        <h3 style="color: #333; margin-bottom: 1rem;">No Reservations Found</h3>
                        <p style="color: #666;">No reservations match your current filters.</p>
                    </div>
                <?php else: ?>
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
                                        <div class="detail-label">Guest</div>
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
                                
                                <?php if ($reservation['special_requests']): ?>
                                    <div style="margin-bottom: 1.5rem;">
                                        <div style="font-weight: 600; color: #333; margin-bottom: 0.5rem;">Special Requests:</div>
                                        <div style="color: #666; background: #f8f9fa; padding: 1rem; border-radius: 10px;">
                                            <?= htmlspecialchars($reservation['special_requests']) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="reservation-actions">
                                    <button class="btn btn-warning" onclick="editReservation(<?= htmlspecialchars(json_encode($reservation)) ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this reservation?')">
                                        <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                        <button type="submit" name="delete_reservation" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Edit Reservation Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Reservation</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <form method="POST" id="editForm">
                <input type="hidden" name="reservation_id" id="edit_reservation_id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status">
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_check_in_date">Check-in Date</label>
                        <input type="date" id="edit_check_in_date" name="check_in_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_check_out_date">Check-out Date</label>
                        <input type="date" id="edit_check_out_date" name="check_out_date" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_num_guests">Number of Guests</label>
                        <input type="number" id="edit_num_guests" name="num_guests" min="1" max="10" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_total_amount">Total Amount</label>
                        <input type="number" id="edit_total_amount" name="total_amount" step="0.01" min="0" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_special_requests">Special Requests</label>
                    <textarea id="edit_special_requests" name="special_requests"></textarea>
                </div>
                
                <button type="submit" name="update_reservation" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Reservation
                </button>
            </form>
        </div>
    </div>

    <script src="../script.js"></script>
    <script>
        function editReservation(reservation) {
            document.getElementById('edit_reservation_id').value = reservation.id;
            document.getElementById('edit_status').value = reservation.status;
            document.getElementById('edit_check_in_date').value = reservation.check_in_date;
            document.getElementById('edit_check_out_date').value = reservation.check_out_date;
            document.getElementById('edit_num_guests').value = reservation.num_guests;
            document.getElementById('edit_total_amount').value = reservation.total_amount;
            document.getElementById('edit_special_requests').value = reservation.special_requests || '';
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
