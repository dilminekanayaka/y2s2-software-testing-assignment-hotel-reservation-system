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
    if (isset($_POST['add_room'])) {
        $hotel_id = $_POST['hotel_id'];
        $type_name = sanitizeInput($_POST['type_name']);
        $description = sanitizeInput($_POST['description']);
        $max_occupancy = $_POST['max_occupancy'];
        $bed_type = sanitizeInput($_POST['bed_type']);
        $room_size = sanitizeInput($_POST['room_size']);
        $amenities = json_encode(explode(',', $_POST['amenities']));
        $images = json_encode(explode(',', $_POST['images']));
        $base_price = $_POST['base_price'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO room_types (hotel_id, type_name, description, max_occupancy, bed_type, room_size, amenities, images, base_price, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())");
            $stmt->execute([$hotel_id, $type_name, $description, $max_occupancy, $bed_type, $room_size, $amenities, $images, $base_price]);
            $success = "Room type added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding room: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['update_room'])) {
        $id = $_POST['room_id'];
        $type_name = sanitizeInput($_POST['type_name']);
        $description = sanitizeInput($_POST['description']);
        $max_occupancy = $_POST['max_occupancy'];
        $bed_type = sanitizeInput($_POST['bed_type']);
        $room_size = sanitizeInput($_POST['room_size']);
        $amenities = json_encode(explode(',', $_POST['amenities']));
        $images = json_encode(explode(',', $_POST['images']));
        $base_price = $_POST['base_price'];
        $status = $_POST['status'];
        
        try {
            $stmt = $pdo->prepare("UPDATE room_types SET type_name = ?, description = ?, max_occupancy = ?, bed_type = ?, room_size = ?, amenities = ?, images = ?, base_price = ?, status = ? WHERE id = ?");
            $stmt->execute([$type_name, $description, $max_occupancy, $bed_type, $room_size, $amenities, $images, $base_price, $status, $id]);
            $success = "Room type updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating room: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['delete_room'])) {
        $id = $_POST['room_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM room_types WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Room type deleted successfully!";
        } catch (PDOException $e) {
            $error = "Error deleting room: " . $e->getMessage();
        }
    }
}

// Get hotel ID (assuming single hotel)
$hotel_id = 4; // Ella Flower Garden hotel ID

// Get all room types
try {
    $stmt = $pdo->prepare("SELECT * FROM room_types WHERE hotel_id = ? ORDER BY created_at DESC");
    $stmt->execute([$hotel_id]);
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading rooms: " . $e->getMessage();
    $rooms = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms - Flower Garden</title>
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
        
        .crud-section {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .section-title {
            font-size: 1.8rem;
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
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
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
        .form-group select,
        .form-group textarea {
            padding: 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
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
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .room-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .room-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .room-content {
            padding: 1.5rem;
        }
        
        .room-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .room-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 1rem;
        }
        
        .room-details {
            color: #666;
            margin-bottom: 1rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .room-details span {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.9rem;
        }
        
        .room-details i {
            color: #667eea;
        }
        
        .room-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
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
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            
            .room-actions {
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
            <a href="manage_rooms.php" class="active">rooms</a>
            <a href="manage_reservations.php">reservations</a>
            <a href="logout.php">logout</a>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <section class="admin-portal">
        <div class="portal-container">
            <div class="portal-header">
                <h1>Room Management Portal</h1>
            </div>

            <div class="portal-nav">
                <a href="dashboard.php" class="nav-btn">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="manage_admins.php" class="nav-btn">
                    <i class="fas fa-users-cog"></i> Admins
                </a>
                <a href="manage_rooms.php" class="nav-btn active">
                    <i class="fas fa-bed"></i> Rooms
                </a>
                <a href="manage_reservations.php" class="nav-btn">
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

            <!-- Add Room Form -->
            <div class="crud-section">
                <h2 class="section-title">
                    <i class="fas fa-plus"></i>
                    Add New Room Type
                </h2>
                
                <form method="POST">
                    <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="type_name">Room Type Name</label>
                            <input type="text" id="type_name" name="type_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="max_occupancy">Max Occupancy</label>
                            <input type="number" id="max_occupancy" name="max_occupancy" min="1" max="10" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="bed_type">Bed Type</label>
                            <input type="text" id="bed_type" name="bed_type" placeholder="e.g., King Bed, Queen Bed" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="room_size">Room Size</label>
                            <input type="text" id="room_size" name="room_size" placeholder="e.g., 25 sq m" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="base_price">Base Price (per night)</label>
                            <input type="number" id="base_price" name="base_price" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="amenities">Amenities (comma-separated)</label>
                            <input type="text" id="amenities" name="amenities" placeholder="WiFi, TV, Air Conditioning, Mini Bar">
                        </div>
                        
                        <div class="form-group">
                            <label for="images">Image URLs (comma-separated)</label>
                            <input type="text" id="images" name="images" placeholder="https://example.com/image1.jpg, https://example.com/image2.jpg">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    
                    <button type="submit" name="add_room" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Room Type
                    </button>
                </form>
            </div>

            <!-- Rooms List -->
            <div class="crud-section">
                <h2 class="section-title">
                    <i class="fas fa-bed"></i>
                    Current Room Types
                </h2>
                
                <div class="rooms-grid">
                    <?php foreach ($rooms as $room): ?>
                        <?php 
                        $amenities = json_decode($room['amenities'], true) ?? [];
                        $images = json_decode($room['images'], true) ?? [];
                        $main_image = !empty($images) ? $images[0] : 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800';
                        ?>
                        
                        <div class="room-card">
                            <div class="room-image">
                                <img src="<?= htmlspecialchars($main_image) ?>" alt="<?= htmlspecialchars($room['type_name']) ?>">
                            </div>
                            
                            <div class="room-content">
                                <h3 class="room-name"><?= htmlspecialchars($room['type_name']) ?></h3>
                                <div class="room-price">$<?= number_format($room['base_price'], 2) ?>/night</div>
                                
                                <div class="room-details">
                                    <span><i class="fas fa-users"></i> <?= $room['max_occupancy'] ?> guests</span>
                                    <span><i class="fas fa-bed"></i> <?= htmlspecialchars($room['bed_type']) ?></span>
                                    <span><i class="fas fa-expand-arrows-alt"></i> <?= htmlspecialchars($room['room_size']) ?></span>
                                </div>
                                
                                <p style="color: #666; margin-bottom: 1rem;"><?= htmlspecialchars($room['description']) ?></p>
                                
                                <div class="room-actions">
                                    <button class="btn btn-warning" onclick="editRoom(<?= htmlspecialchars(json_encode($room)) ?>)">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this room type?')">
                                        <input type="hidden" name="room_id" value="<?= $room['id'] ?>">
                                        <button type="submit" name="delete_room" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Edit Room Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Room Type</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <form method="POST" id="editForm">
                <input type="hidden" name="room_id" id="edit_room_id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit_type_name">Room Type Name</label>
                        <input type="text" id="edit_type_name" name="type_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_max_occupancy">Max Occupancy</label>
                        <input type="number" id="edit_max_occupancy" name="max_occupancy" min="1" max="10" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_bed_type">Bed Type</label>
                        <input type="text" id="edit_bed_type" name="bed_type" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_room_size">Room Size</label>
                        <input type="text" id="edit_room_size" name="room_size" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_base_price">Base Price (per night)</label>
                        <input type="number" id="edit_base_price" name="base_price" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_status">Status</label>
                        <select id="edit_status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_amenities">Amenities (comma-separated)</label>
                    <input type="text" id="edit_amenities" name="amenities">
                </div>
                
                <div class="form-group">
                    <label for="edit_images">Image URLs (comma-separated)</label>
                    <input type="text" id="edit_images" name="images">
                </div>
                
                <div class="form-group">
                    <label for="edit_description">Description</label>
                    <textarea id="edit_description" name="description" required></textarea>
                </div>
                
                <button type="submit" name="update_room" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Room Type
                </button>
            </form>
        </div>
    </div>

    <script src="../script.js"></script>
    <script>
        function editRoom(room) {
            document.getElementById('edit_room_id').value = room.id;
            document.getElementById('edit_type_name').value = room.type_name;
            document.getElementById('edit_max_occupancy').value = room.max_occupancy;
            document.getElementById('edit_bed_type').value = room.bed_type;
            document.getElementById('edit_room_size').value = room.room_size;
            document.getElementById('edit_base_price').value = room.base_price;
            document.getElementById('edit_status').value = room.status;
            document.getElementById('edit_description').value = room.description;
            
            // Handle amenities
            const amenities = JSON.parse(room.amenities || '[]');
            document.getElementById('edit_amenities').value = amenities.join(', ');
            
            // Handle images
            const images = JSON.parse(room.images || '[]');
            document.getElementById('edit_images').value = images.join(', ');
            
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
