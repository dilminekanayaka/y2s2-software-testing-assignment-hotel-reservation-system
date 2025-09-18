<?php
require_once 'auth.php';
require_once 'config.php';

// Get search parameters
$city = $_GET['city'] ?? '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$guests = $_GET['guests'] ?? 1;

// Debug: Log received parameters (remove in production)
// error_log("Hotels.php received: city=$city, check_in=$check_in, check_out=$check_out, guests=$guests");

try {
    // Get hotels based on city selection
    if ($city) {
        $stmt = $pdo->prepare("SELECT * FROM hotels WHERE city = ? AND status = 'active'");
        $stmt->execute([$city]);
        $hotel = $stmt->fetch();
        // error_log("Hotel found for city '$city': " . ($hotel ? $hotel['name'] . " (ID: " . $hotel['id'] . ")" : 'none'));
    } else {
        // If no city selected, show all hotels
        $stmt = $pdo->query("SELECT * FROM hotels WHERE status = 'active' ORDER BY city");
        $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hotel = null; // We'll show multiple hotels
    }
    
    // If no city selected, show all hotels
    if (!$city) {
        $hotels = $hotels ?? [];
    } else if (!$hotel) {
        // If city selected but no hotel found, show message
        $hotel = null;
    }
    
    // Get room types for the selected hotel
    if ($hotel) {
        // error_log("Getting room types for hotel ID: " . $hotel['id']);
        $stmt = $pdo->prepare("SELECT * FROM room_types WHERE hotel_id = ? AND status = 'active' ORDER BY base_price ASC");
        $stmt->execute([$hotel['id']]);
        $room_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // error_log("Found " . count($room_types) . " room types for hotel " . $hotel['id']);
    } else {
        $room_types = [];
        // error_log("No hotel selected, room_types = empty");
    }
    
    // If no room types exist, create some default ones
    if (empty($room_types)) {
        // error_log("No room types found, creating default rooms for hotel " . $hotel['id']);
        // Get appropriate images based on hotel city
        $hotel_images = [];
        $room_images = [];
        
        switch ($hotel['city']) {
            case 'Colombo':
                $hotel_images = ['uploads/Hotels/Colombo/Hotel-1.png', 'uploads/Hotels/Colombo/Hotel-2.png', 'uploads/Hotels/Colombo/Hotel-3.png'];
                $room_images = ['uploads/Hotels/Colombo/Room-1.png', 'uploads/Hotels/Colombo/Room-2.png', 'uploads/Hotels/Colombo/Room-3.png'];
                break;
            case 'Ella':
                $hotel_images = ['uploads/Hotels/Ella/Hotel-1.png', 'uploads/Hotels/Ella/Hotel-2.png'];
                $room_images = ['uploads/Hotels/Ella/Room-1.png', 'uploads/Hotels/Ella/Room-2.png'];
                break;
            case 'Matara':
                $hotel_images = ['uploads/Hotels/Matara/Matara-1.png', 'uploads/Hotels/Matara/Matara-2.png'];
                $room_images = ['uploads/Hotels/Matara/Room-1.png', 'uploads/Hotels/Matara/Room-2.png', 'uploads/Hotels/Matara/Room-3.png'];
                break;
            case 'Nuwara Eliya':
                $hotel_images = ['uploads/Hotels/Nuwara Eliya/Hotel-1.png', 'uploads/Hotels/Nuwara Eliya/Hotel-2.png', 'uploads/Hotels/Nuwara Eliya/Hotel-3.png', 'uploads/Hotels/Nuwara Eliya/Hotel-4.png'];
                $room_images = ['uploads/Hotels/Nuwara Eliya/Room-1.png', 'uploads/Hotels/Nuwara Eliya/Room-2.png'];
                break;
            default:
                $hotel_images = ['uploads/Hotels/Colombo/Hotel-1.png'];
                $room_images = ['uploads/Hotels/Colombo/Room-1.png'];
        }
        
        $default_rooms = [
            [
                'type_name' => 'Garden View Room',
                'description' => 'Comfortable room with beautiful garden views and modern amenities.',
                'max_occupancy' => 2,
                'bed_type' => 'Queen Bed',
                'room_size' => '25 sq m',
                'amenities' => '["Air Conditioning", "WiFi", "TV", "Garden View", "Private Bathroom", "Mini Bar"]',
                'images' => json_encode([$room_images[0]]),
                'base_price' => 120.00
            ],
            [
                'type_name' => 'Deluxe Queen Room',
                'description' => 'Elegant room featuring a queen bed with enhanced comfort and premium amenities.',
                'max_occupancy' => 2,
                'bed_type' => 'Queen Bed',
                'room_size' => '30 sq m',
                'amenities' => '["Air Conditioning", "WiFi", "TV", "Garden View", "Private Bathroom", "Mini Bar", "Coffee Machine", "Safe", "Work Desk"]',
                'images' => json_encode([$room_images[1] ?? $room_images[0]]),
                'base_price' => 150.00
            ],
            [
                'type_name' => 'Premier Room',
                'description' => 'Higher floor room with stunning mountain views and upgraded amenities for the ultimate comfort.',
                'max_occupancy' => 3,
                'bed_type' => 'King Bed',
                'room_size' => '35 sq m',
                'amenities' => '["Air Conditioning", "WiFi", "TV", "Mountain View", "Private Bathroom", "Mini Bar", "Coffee Machine", "Safe", "Work Desk", "Balcony Access"]',
                'images' => json_encode([$room_images[2] ?? $room_images[0]]),
                'base_price' => 180.00
            ],
            [
                'type_name' => 'Mountain View Suite',
                'description' => 'Spacious suite with panoramic mountain views and premium amenities.',
                'max_occupancy' => 4,
                'bed_type' => 'King Bed',
                'room_size' => '45 sq m',
                'amenities' => '["Air Conditioning", "WiFi", "TV", "Mountain View", "Private Balcony", "Living Area", "Mini Bar", "Safe"]',
                'images' => json_encode([$room_images[1] ?? $room_images[0]]),
                'base_price' => 200.00
            ],
            [
                'type_name' => 'Presidential Suite',
                'description' => 'Luxury suite with breathtaking views and exclusive amenities.',
                'max_occupancy' => 6,
                'bed_type' => 'King Bed',
                'room_size' => '80 sq m',
                'amenities' => '["Air Conditioning", "WiFi", "TV", "Panoramic Views", "Private Balcony", "Living Area", "Jacuzzi", "Butler Service", "Mini Bar", "Safe"]',
                'images' => json_encode([$room_images[0]]),
                'base_price' => 350.00
            ]
        ];
        
        foreach ($default_rooms as $room) {
            $stmt = $pdo->prepare("
                INSERT INTO room_types (hotel_id, type_name, description, max_occupancy, bed_type, room_size, amenities, images, base_price, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $hotel['id'],
                $room['type_name'],
                $room['description'],
                $room['max_occupancy'],
                $room['bed_type'],
                $room['room_size'],
                $room['amenities'],
                $room['images'],
                $room['base_price'],
                'active'
            ]);
        }
        
        // Get the newly created room types
        $stmt = $pdo->prepare("SELECT * FROM room_types WHERE hotel_id = ? AND status = 'active' ORDER BY base_price ASC");
        $stmt->execute([$hotel['id']]);
        $room_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // error_log("After creating default rooms, found " . count($room_types) . " room types for hotel " . $hotel['id']);
    }
    
} catch (PDOException $e) {
    $error = "Error loading hotels: " . $e->getMessage();
    $hotel = null;
    $room_types = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotels - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .hotels-section {
            padding: 8rem 5% 5rem;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .hotels-section:has(.hotel-hero-section) {
            padding: 0;
            background: transparent;
        }
        
        .hotels-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .hotels-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .hotels-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .hotels-header h1 {
            font-size: 3.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .hotels-header p {
            font-size: 1.2rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .hotel-description-header {
            font-size: 1.3rem !important;
            color: #555 !important;
            max-width: 800px !important;
            margin: 0 auto 1.5rem !important;
            line-height: 1.6 !important;
        }
        
        .hotel-location-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #667eea;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .hotel-location-header i {
            font-size: 1.2rem;
        }
        
        .hotel-rating-header {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #ffd700;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .hotel-rating-header i {
            font-size: 1.2rem;
        }
        
        /* Hotel Hero Section */
        .hotel-hero-section {
            position: relative;
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        
        .hotel-hero-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hotel-hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%);
            z-index: 1;
        }
        
        .hotel-hero-content {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 1000px;
            padding: 0 2rem;
            text-align: center;
            color: white;
        }
        
        .hotel-hero-info {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hotel-hero-name {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            color: #333;
        }
        
        .hotel-hero-location {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #667eea;
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .hotel-hero-location i {
            font-size: 1.3rem;
        }
        
        .hotel-hero-rating {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            color: #ffd700;
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        
        .hotel-hero-rating i {
            font-size: 1.3rem;
        }
        
        .hotel-hero-description {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .hotel-hero-amenities {
            margin-top: 1.5rem;
        }
        
        .hotel-hero-amenities .amenities-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.8rem;
            text-align: center;
        }
        
        .hotel-hero-amenities .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
            justify-content: center;
        }
        
        .hotel-hero-amenities .amenity-tag {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .hotel-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 3rem;
            transition: all 0.3s ease;
        }
        
        .hotel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }
        
        .hotel-image {
            height: 300px;
            overflow: hidden;
            position: relative;
        }
        
        .hotel-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .hotel-card:hover .hotel-image img {
            transform: scale(1.05);
        }
        
        .hotel-content {
            padding: 2rem;
        }
        
        .hotel-name {
            font-size: 2rem;
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
        
        .hotel-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .hotel-rating i {
            color: #ffd700;
        }
        
        .hotel-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }
        
        .hotel-amenities {
            margin-bottom: 2rem;
        }
        
        .hotel-actions {
            margin-top: 1.5rem;
        }
        
        .amenities-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .amenities-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .amenity-tag {
            background: #f0f2ff;
            color: #667eea;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .search-results-section {
            padding: 4rem 5% 5rem;
            background: #f8f9fa;
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
        
        .rooms-section {
            margin-top: 2rem;
        }
        
        .rooms-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .rooms-title i {
            color: #667eea;
        }
        
        .rooms-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 2.5rem;
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
            height: 250px;
            overflow: hidden;
            position: relative;
        }
        
        .room-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .room-card:hover .room-image img {
            transform: scale(1.1);
        }
        
        .room-content {
            padding: 2rem;
        }
        
        .room-name {
            font-size: 1.6rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.8rem;
        }
        
        .room-details {
            color: #666;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
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
        
        .room-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
        }
        
        .room-price {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .price-amount {
            font-size: 2rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 0.3rem;
        }
        
        .price-period {
            color: #666;
            font-size: 0.9rem;
        }
        
        .room-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn {
            padding: 1rem 1.5rem;
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
            flex: 1;
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
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .search-params {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .search-info {
            color: #666;
        }
        
        .search-info strong {
            color: #333;
        }
        
        .modify-search {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .modify-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }
        
        @media (max-width: 768px) {
            .hotels-section {
                padding: 6rem 2% 3rem;
            }
            
            .hotels-header h1 {
                font-size: 2.5rem;
            }
            
            .hotel-description-header {
                font-size: 1.1rem !important;
                max-width: 90% !important;
            }
            
            .hotel-location-header {
                font-size: 1rem;
            }
            
            .hotel-rating-header {
                font-size: 1rem;
            }
            
            .hotel-hero-section {
                height: 100vh;
            }
            
            .hotel-hero-info {
                background: rgba(255, 255, 255, 0.8);
                padding: 1.5rem;
                max-width: 90%;
            }
            
            .hotel-hero-name {
                font-size: 2.2rem;
            }
            
            .hotel-hero-description {
                font-size: 1.1rem;
            }
            
            .hotel-hero-location,
            .hotel-hero-rating {
                font-size: 1rem;
            }
            
            .rooms-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .room-card {
                margin: 0 1rem;
            }
            
            .room-image {
                height: 220px;
            }
            
            .room-content {
                padding: 1.5rem;
            }
            
            .search-results-section {
                padding: 3rem 2% 4rem;
            }
            
            .room-actions {
                flex-direction: column;
            }
            
            .search-params {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <section class="header">
        <a href="home.php" class="logo">Flower Garden</a>
        <nav class="navbar">
            <a href="home.php">home</a>
            <a href="hotels.php" class="active">hotels</a>
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

    <section class="hotels-section">
        <div class="hotels-container">
            <?php if (!$city || !$hotel): ?>
                <div class="hotels-header">
                    <?php if ($city): ?>
                        <h1>No hotels found in <?= htmlspecialchars($city) ?></h1>
                    <?php else: ?>
                        <h1>Flower Garden Hotels</h1>
                        <p>Select a city to view available hotels and rooms</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (!$city): ?>
                <!-- Show all hotels when no city is selected -->
                <div class="hotels-grid">
                    <?php foreach ($hotels as $hotel_item): ?>
                        <?php 
                        $amenities = json_decode($hotel_item['amenities'], true) ?? [];
                        $images = json_decode($hotel_item['images'], true) ?? [];
                        $main_image = !empty($images) ? $images[0] : 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800';
                        ?>
                        
                        <div class="hotel-card">
                            <div class="hotel-image">
                                <img src="<?= htmlspecialchars($main_image) ?>" alt="<?= htmlspecialchars($hotel_item['name']) ?>">
                            </div>
                            
                            <div class="hotel-content">
                                <h2 class="hotel-name"><?= htmlspecialchars($hotel_item['name']) ?></h2>
                                <div class="hotel-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($hotel_item['city'] . ', ' . $hotel_item['country']) ?>
                                </div>
                                <div class="hotel-rating">
                                    <i class="fas fa-star"></i>
                                    <span><?= number_format($hotel_item['rating'], 1) ?> Rating</span>
                                </div>
                                <p class="hotel-description">
                                    <?= htmlspecialchars(substr($hotel_item['description'], 0, 150)) ?>...
                                </p>
                                
                                <div class="hotel-actions">
                                    <a href="hotel_details.php?id=<?= $hotel_item['id'] ?>&check_in=<?= urlencode($check_in) ?>&check_out=<?= urlencode($check_out) ?>&guests=<?= $guests ?>" class="btn btn-primary">
                                        <i class="fas fa-info-circle"></i> View Details
                                    </a>
                                    <a href="hotels.php?city=<?= urlencode($hotel_item['city']) ?>&check_in=<?= urlencode($check_in) ?>&check_out=<?= urlencode($check_out) ?>&guests=<?= $guests ?>" class="btn btn-secondary">
                                        <i class="fas fa-bed"></i> View Rooms
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php elseif ($hotel): ?>
                <?php 
                $amenities = json_decode($hotel['amenities'], true) ?? [];
                $images = json_decode($hotel['images'], true) ?? [];
                $main_image = !empty($images) ? $images[0] : 'uploads/Hotels/Colombo/Hotel-1.png';
                ?>
                
                <!-- Hotel Hero Section -->
                <div class="hotel-hero-section">
                    <div class="hotel-hero-image" style="background-image: url('<?= htmlspecialchars($main_image) ?>');">
                        <div class="hotel-hero-overlay"></div>
                        <div class="hotel-hero-content">
                            <div class="hotel-hero-info">
                                <h2 class="hotel-hero-name"><?= htmlspecialchars($hotel['name']) ?></h2>
                                <div class="hotel-hero-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <?= htmlspecialchars($hotel['address'] . ', ' . $hotel['city'] . ', ' . $hotel['country']) ?>
                                </div>
                                <div class="hotel-hero-rating">
                                    <i class="fas fa-star"></i>
                                    <span><?= number_format($hotel['rating'], 1) ?> Rating</span>
                                </div>
                                <p class="hotel-hero-description">
                                    <?= htmlspecialchars($hotel['description']) ?>
                                </p>
                                
                                <?php if (!empty($amenities)): ?>
                                    <div class="hotel-hero-amenities">
                                        <div class="amenities-title">Hotel Amenities:</div>
                                        <div class="amenities-list">
                                            <?php foreach ($amenities as $amenity): ?>
                                                <span class="amenity-tag"><?= htmlspecialchars($amenity) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="hotel-hero-actions" style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center;">
                                    <a href="hotel_details.php?id=<?= $hotel['id'] ?>&check_in=<?= urlencode($check_in) ?>&check_out=<?= urlencode($check_out) ?>&guests=<?= $guests ?>" class="btn btn-primary">
                                        <i class="fas fa-info-circle"></i> View Hotel Details
                                    </a>
                                    <a href="hotels.php" class="btn btn-secondary">
                                        <i class="fas fa-search"></i> Search Other Hotels
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="search-results-section">
                    <?php if ($check_in && $check_out): ?>
                        <div class="search-params">
                            <div class="search-info">
                                <strong>Search Results:</strong> 
                                <?= date('M j, Y', strtotime($check_in)) ?> - <?= date('M j, Y', strtotime($check_out)) ?> 
                                (<?= (new DateTime($check_in))->diff(new DateTime($check_out))->days ?> nights) 
                                for <?= $guests ?> <?= $guests === 1 ? 'guest' : 'guests' ?>
                            </div>
                            <a href="home.php" class="modify-search">
                                <i class="fas fa-edit"></i> Modify Search
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="rooms-section">
                    <h2 class="rooms-title">
                        <i class="fas fa-bed"></i>
                        Available Room Types
                    </h2>
                    
                    <?php if (empty($room_types)): ?>
                        <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);">
                            <i class="fas fa-bed" style="font-size: 4rem; color: #667eea; margin-bottom: 1rem;"></i>
                            <h3 style="color: #333; margin-bottom: 1rem;">No rooms available</h3>
                            <p style="color: #666;">Please check back later for available accommodations.</p>
                        </div>
                    <?php else: ?>
                        <div class="rooms-grid">
                            <?php foreach ($room_types as $room): ?>
                                <?php 
                                $room_amenities = json_decode($room['amenities'], true) ?? [];
                                $room_images = json_decode($room['images'], true) ?? [];
                                $room_main_image = !empty($room_images) ? $room_images[0] : 'uploads/Hotels/Colombo/Room-1.png';
                                ?>
                                
                                <div class="room-card">
                                    <div class="room-image">
                                        <img src="<?= htmlspecialchars($room_main_image) ?>" alt="<?= htmlspecialchars($room['type_name']) ?>">
                                    </div>
                                    
                                    <div class="room-content">
                                        <h3 class="room-name"><?= htmlspecialchars($room['type_name']) ?></h3>
                                        
                                        <div class="room-details">
                                            <span><i class="fas fa-users"></i> Up to <?= $room['max_occupancy'] ?> guests</span>
                                            <?php if ($room['bed_type']): ?>
                                                <span><i class="fas fa-bed"></i> <?= htmlspecialchars($room['bed_type']) ?></span>
                                            <?php endif; ?>
                                            <?php if ($room['room_size']): ?>
                                                <span><i class="fas fa-expand-arrows-alt"></i> <?= htmlspecialchars($room['room_size']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <p class="room-description">
                                            <?= htmlspecialchars($room['description']) ?>
                                        </p>
                                        
                                        <div class="room-price">
                                            <div class="price-amount">$<?= number_format($room['base_price'], 2) ?></div>
                                            <div class="price-period">per night</div>
                                        </div>
                                        
                                        <div class="room-actions">
                                            <?php if (isUserLoggedIn()): ?>
                                                <?php 
                                                $hotel_id_for_booking = $hotel ? $hotel['id'] : $room['hotel_id'];
                                                // error_log("Hotel ID for booking: " . $hotel_id_for_booking . " (hotel exists: " . ($hotel ? 'yes' : 'no') . ", room hotel_id: " . $room['hotel_id'] . ")");
                                                $booking_url = "booking.php?hotel_id=" . $hotel_id_for_booking . "&room_type_id=" . $room['id'] . "&check_in=" . urlencode($check_in) . "&check_out=" . urlencode($check_out) . "&guests=" . $guests;
                                                // error_log("Booking URL: " . $booking_url);
                                                ?>
                                                <a href="<?= $booking_url ?>" class="btn btn-primary">
                                                    <i class="fas fa-calendar-check"></i> Book Now
                                                </a>
                                            <?php else: ?>
                                                <a href="login.php" class="btn btn-primary">
                                                    <i class="fas fa-sign-in-alt"></i> Login to Book
                                                </a>
                                            <?php endif; ?>
                                            <a href="room_details.php?id=<?= $room['id'] ?>" class="btn btn-secondary">
                                                <i class="fas fa-info-circle"></i> Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="script.js"></script>
</body>
</html>