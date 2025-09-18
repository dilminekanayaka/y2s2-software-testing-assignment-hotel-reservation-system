<?php
require_once 'auth.php';
require_once 'config.php';

// Get hotel ID from URL
$hotel_id = $_GET['id'] ?? '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$guests = $_GET['guests'] ?? 1;

$hotel = null;
$room_types = [];
$error = '';

try {
    if ($hotel_id) {
        // Get hotel details
        $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ? AND status = 'active'");
        $stmt->execute([$hotel_id]);
        $hotel = $stmt->fetch();
        
        if ($hotel) {
            // Get room types for this hotel
            $stmt = $pdo->prepare("SELECT * FROM room_types WHERE hotel_id = ? AND status = 'active' ORDER BY base_price ASC");
            $stmt->execute([$hotel_id]);
            $room_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // If no room types exist, create some default ones
            if (empty($room_types)) {
                // Get appropriate images based on hotel city
                $room_images = [];
                
                switch ($hotel['city']) {
                    case 'Colombo':
                        $room_images = ['uploads/Hotels/Colombo/Room-1.png', 'uploads/Hotels/Colombo/Room-2.png', 'uploads/Hotels/Colombo/Room-3.png'];
                        break;
                    case 'Ella':
                        $room_images = ['uploads/Hotels/Ella/Room-1.png', 'uploads/Hotels/Ella/Room-2.png'];
                        break;
                    case 'Matara':
                        $room_images = ['uploads/Hotels/Matara/Room-1.png', 'uploads/Hotels/Matara/Room-2.png', 'uploads/Hotels/Matara/Room-3.png'];
                        break;
                    case 'Nuwara Eliya':
                        $room_images = ['uploads/Hotels/Nuwara Eliya/Room-1.png', 'uploads/Hotels/Nuwara Eliya/Room-2.png'];
                        break;
                    default:
                        $room_images = ['uploads/Hotels/Colombo/Room-1.png'];
                }
                
                $default_rooms = [
                    [
                        'type_name' => 'Standard Room',
                        'description' => 'Comfortable room with modern amenities.',
                        'max_occupancy' => 2,
                        'bed_type' => 'Queen Bed',
                        'room_size' => '25 sq m',
                        'amenities' => '["Air Conditioning", "WiFi", "TV", "Private Bathroom"]',
                        'images' => json_encode([$room_images[0]]),
                        'base_price' => 120.00
                    ],
                    [
                        'type_name' => 'Deluxe Room',
                        'description' => 'Spacious room with premium amenities.',
                        'max_occupancy' => 4,
                        'bed_type' => 'King Bed',
                        'room_size' => '35 sq m',
                        'amenities' => '["Air Conditioning", "WiFi", "TV", "Private Bathroom", "Mini Bar", "Balcony"]',
                        'images' => json_encode([$room_images[1] ?? $room_images[0]]),
                        'base_price' => 180.00
                    ]
                ];
                
                foreach ($default_rooms as $room) {
                    $stmt = $pdo->prepare("
                        INSERT INTO room_types (hotel_id, type_name, description, max_occupancy, bed_type, room_size, amenities, images, base_price) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $hotel_id,
                        $room['type_name'],
                        $room['description'],
                        $room['max_occupancy'],
                        $room['bed_type'],
                        $room['room_size'],
                        $room['amenities'],
                        $room['images'],
                        $room['base_price']
                    ]);
                }
                
                // Fetch the newly created room types
                $stmt = $pdo->prepare("SELECT * FROM room_types WHERE hotel_id = ? AND status = 'active' ORDER BY base_price ASC");
                $stmt->execute([$hotel_id]);
                $room_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }
} catch (PDOException $e) {
    $error = "Error loading hotel details: " . $e->getMessage();
}

if (!$hotel) {
    $error = "Hotel not found or not available.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($hotel['name'] ?? 'Hotel Details') ?> - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .hotel-details-container {
            min-height: 100vh;
            background: #f8f9fa;
        }
        
        .hotel-hero {
            position: relative;
            height: 60vh;
            overflow: hidden;
        }
        
        .hotel-image-slider {
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        .hotel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }
        
        .hotel-slide.active {
            opacity: 1;
        }
        
        .hotel-slide-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4));
            z-index: 1;
        }
        
        .hotel-hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            text-align: center;
            color: white;
            width: 100%;
            max-width: 1000px;
            padding: 0 2rem;
        }
        
        .slider-nav {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 3;
            display: flex;
            gap: 10px;
        }
        
        .slider-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .slider-dot.active {
            background: white;
            transform: scale(1.2);
        }
        
        .slider-arrows {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 3;
            background: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 15px 20px;
            cursor: pointer;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .slider-arrows:hover {
            background: rgba(0, 0, 0, 0.8);
        }
        
        .slider-prev {
            left: 20px;
        }
        
        .slider-next {
            right: 20px;
        }
        
        .hotel-hero-content h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        .hotel-hero-content p {
            font-size: 1.3rem;
            margin-bottom: 2rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .hotel-info {
            padding: 4rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .hotel-overview {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }
        
        .hotel-description h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
        }
        
        .hotel-description p {
            font-size: 1.1rem;
            color: #666;
            line-height: 1.8;
            margin-bottom: 2rem;
        }
        
        .hotel-amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .amenity-tag {
            background: #667eea;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .hotel-sidebar {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .hotel-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .stars {
            color: #ffc107;
            font-size: 1.2rem;
        }
        
        .rating-text {
            font-weight: 600;
            color: #333;
        }
        
        .hotel-location {
            margin-bottom: 2rem;
        }
        
        .location-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: #666;
        }
        
        .location-item i {
            color: #667eea;
            width: 20px;
        }
        
        .rooms-section {
            margin-top: 4rem;
        }
        
        .rooms-section h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2rem;
        }
        
        .room-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .room-image {
            height: 250px;
            background-size: cover;
            background-position: center;
        }
        
        .room-content {
            padding: 2rem;
        }
        
        .room-name {
            font-size: 1.6rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .room-description {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .room-features {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.9rem;
        }
        
        .feature-item i {
            color: #667eea;
        }
        
        .room-amenities {
            margin-bottom: 1.5rem;
        }
        
        .room-amenities h4 {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .amenity-item {
            background: #f8f9fa;
            color: #666;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
        
        .room-price {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .price-amount {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
        }
        
        .price-period {
            color: #666;
            font-size: 0.9rem;
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
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 5%;
            border: 1px solid #f5c6cb;
            text-align: center;
        }
        
        /* User avatar dropdown styling */
        .user-avatar-dropdown {
            position: relative;
            margin-left: 1rem;
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
            font-size: 1.1rem;
        }
        
        .user-email {
            color: #666;
            font-size: 0.95rem;
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
            font-size: 1rem;
            font-weight: 500;
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
        
        .user-avatar-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .user-avatar-dropdown:hover .user-avatar .dropdown-arrow {
            transform: rotate(180deg);
        }
        
        @media (max-width: 768px) {
            .hotel-hero-content h1 {
                font-size: 2.5rem;
            }
            
            .hotel-overview {
                grid-template-columns: 1fr;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
            }
            
            .room-features {
                grid-template-columns: 1fr;
            }
            
            .slider-arrows {
                padding: 10px 15px;
                font-size: 1.2rem;
            }
            
            .slider-prev {
                left: 10px;
            }
            
            .slider-next {
                right: 10px;
            }
            
            .slider-nav {
                bottom: 15px;
            }
            
            .slider-dot {
                width: 10px;
                height: 10px;
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

    <?php if ($error): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            <br><br>
            <a href="hotels.php" class="btn btn-primary">Back to Hotels</a>
        </div>
    <?php else: ?>
        <div class="hotel-details-container">
            <!-- Hotel Hero Section with Image Slider -->
            <div class="hotel-hero">
                <div class="hotel-image-slider">
                    <?php 
                    $hotel_images = json_decode($hotel['images'] ?? '[]', true);
                    if (empty($hotel_images)) {
                        // Fallback images based on city
                        switch ($hotel['city']) {
                            case 'Colombo':
                                $hotel_images = ['uploads/Hotels/Colombo/Hotel-1.png', 'uploads/Hotels/Colombo/Hotel-2.png', 'uploads/Hotels/Colombo/Hotel-3.png'];
                                break;
                            case 'Ella':
                                $hotel_images = ['uploads/Hotels/Ella/Hotel-1.png', 'uploads/Hotels/Ella/Hotel-2.png'];
                                break;
                            case 'Matara':
                                $hotel_images = ['uploads/Hotels/Matara/Matara-1.png', 'uploads/Hotels/Matara/Matara-2.png'];
                                break;
                            case 'Nuwara Eliya':
                                $hotel_images = ['uploads/Hotels/Nuwara Eliya/Hotel-1.png', 'uploads/Hotels/Nuwara Eliya/Hotel-2.png', 'uploads/Hotels/Nuwara Eliya/Hotel-3.png', 'uploads/Hotels/Nuwara Eliya/Hotel-4.png'];
                                break;
                            default:
                                $hotel_images = ['uploads/Hotels/Colombo/Hotel-1.png'];
                        }
                    }
                    
                    foreach ($hotel_images as $index => $image): 
                    ?>
                        <div class="hotel-slide <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('<?= htmlspecialchars($image) ?>')"></div>
                    <?php endforeach; ?>
                    
                    <div class="hotel-slide-overlay"></div>
                    
                    <div class="hotel-hero-content">
                        <h1><?= htmlspecialchars($hotel['name']) ?></h1>
                        <p><?= htmlspecialchars($hotel['description']) ?></p>
                    </div>
                    
                    <!-- Slider Navigation -->
                    <div class="slider-nav">
                        <?php foreach ($hotel_images as $index => $image): ?>
                            <div class="slider-dot <?= $index === 0 ? 'active' : '' ?>" onclick="showSlide(<?= $index ?>)"></div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Slider Arrows -->
                    <?php if (count($hotel_images) > 1): ?>
                        <button class="slider-arrows slider-prev" onclick="previousSlide()">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button class="slider-arrows slider-next" onclick="nextSlide()">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Hotel Information -->
            <div class="hotel-info">
                <div class="hotel-overview">
                    <div class="hotel-description">
                        <h2>About This Hotel</h2>
                        <p><?= htmlspecialchars($hotel['description']) ?></p>
                        
                        <div class="hotel-amenities">
                            <?php 
                            $amenities = json_decode($hotel['amenities'] ?? '[]', true);
                            foreach ($amenities as $amenity): 
                            ?>
                                <span class="amenity-tag"><?= htmlspecialchars($amenity) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="hotel-sidebar">
                        <div class="hotel-rating">
                            <div class="stars">
                                <?php for ($i = 0; $i < floor($hotel['rating']); $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                                <?php if ($hotel['rating'] - floor($hotel['rating']) >= 0.5): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php endif; ?>
                            </div>
                            <span class="rating-text"><?= number_format($hotel['rating'], 1) ?>/5</span>
                        </div>
                        
                        <div class="hotel-location">
                            <div class="location-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($hotel['address']) ?></span>
                            </div>
                            <div class="location-item">
                                <i class="fas fa-city"></i>
                                <span><?= htmlspecialchars($hotel['city']) ?>, <?= htmlspecialchars($hotel['country']) ?></span>
                            </div>
                            <?php if ($hotel['phone']): ?>
                            <div class="location-item">
                                <i class="fas fa-phone"></i>
                                <span><?= htmlspecialchars($hotel['phone']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($hotel['email']): ?>
                            <div class="location-item">
                                <i class="fas fa-envelope"></i>
                                <span><?= htmlspecialchars($hotel['email']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Available Rooms -->
                <div class="rooms-section">
                    <h2>Available Rooms</h2>
                    <div class="rooms-grid">
                        <?php foreach ($room_types as $room): ?>
                            <div class="room-card">
                                <div class="room-image" style="background-image: url('<?= json_decode($room['images'] ?? '[]')[0] ?? 'uploads/Hotels/Colombo/Room-1.png' ?>')"></div>
                                <div class="room-content">
                                    <h3 class="room-name"><?= htmlspecialchars($room['type_name']) ?></h3>
                                    <p class="room-description"><?= htmlspecialchars($room['description']) ?></p>
                                    
                                    <div class="room-features">
                                        <div class="feature-item">
                                            <i class="fas fa-users"></i>
                                            <span><?= $room['max_occupancy'] ?> Guest<?= $room['max_occupancy'] > 1 ? 's' : '' ?></span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-bed"></i>
                                            <span><?= htmlspecialchars($room['bed_type']) ?></span>
                                        </div>
                                        <div class="feature-item">
                                            <i class="fas fa-expand-arrows-alt"></i>
                                            <span><?= htmlspecialchars($room['room_size']) ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="room-amenities">
                                        <h4>Amenities</h4>
                                        <div class="amenities-list">
                                            <?php 
                                            $room_amenities = json_decode($room['amenities'] ?? '[]', true);
                                            foreach ($room_amenities as $amenity): 
                                            ?>
                                                <span class="amenity-item"><?= htmlspecialchars($amenity) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="room-price">
                                        <div>
                                            <div class="price-amount">$<?= number_format($room['base_price'], 2) ?></div>
                                            <div class="price-period">per night</div>
                                        </div>
                                        <?php if (isUserLoggedIn()): ?>
                                            <?php
                                            $booking_url = "booking.php?hotel_id=" . $hotel['id'] . "&room_type_id=" . $room['id'];
                                            if ($check_in && $check_out && $guests) {
                                                $booking_url .= "&check_in=" . urlencode($check_in) . "&check_out=" . urlencode($check_out) . "&guests=" . $guests;
                                            }
                                            ?>
                                            <a href="<?= $booking_url ?>" class="btn btn-primary">
                                                <i class="fas fa-calendar-check"></i> Book Now
                                            </a>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-primary">
                                                <i class="fas fa-sign-in-alt"></i> Login to Book
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="script.js"></script>
    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.hotel-slide');
        const dots = document.querySelectorAll('.slider-dot');
        const totalSlides = slides.length;
        
        function showSlide(index) {
            // Remove active class from all slides and dots
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            
            // Add active class to current slide and dot
            slides[index].classList.add('active');
            dots[index].classList.add('active');
            
            currentSlide = index;
        }
        
        function nextSlide() {
            const nextIndex = (currentSlide + 1) % totalSlides;
            showSlide(nextIndex);
        }
        
        function previousSlide() {
            const prevIndex = (currentSlide - 1 + totalSlides) % totalSlides;
            showSlide(prevIndex);
        }
        
        // Auto-play slider
        if (totalSlides > 1) {
            setInterval(nextSlide, 5000); // Change slide every 5 seconds
        }
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                previousSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            }
        });
    </script>
</body>
</html>
