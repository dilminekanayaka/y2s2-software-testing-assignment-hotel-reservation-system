<?php
require_once 'auth.php';
require_once 'config.php';

// Get room type ID from URL
$room_id = $_GET['id'] ?? '';
$check_in = $_GET['check_in'] ?? '';
$check_out = $_GET['check_out'] ?? '';
$guests = $_GET['guests'] ?? 1;

$room = null;
$hotel = null;
$error = '';

try {
    if ($room_id) {
        // Get room type details
        $stmt = $pdo->prepare("SELECT rt.*, h.name as hotel_name, h.address, h.city, h.description as hotel_description, h.amenities as hotel_amenities FROM room_types rt JOIN hotels h ON rt.hotel_id = h.id WHERE rt.id = ? AND rt.status = 'active' AND h.status = 'active'");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch();
        
        if ($room) {
            // Get hotel details
            $stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ? AND status = 'active'");
            $stmt->execute([$room['hotel_id']]);
            $hotel = $stmt->fetch();
        }
    }
    
    if (!$room) {
        $error = 'Room not found or no longer available.';
    }
} catch (Exception $e) {
    $error = 'Error loading room details: ' . $e->getMessage();
}

$page_title = $room ? $room['type_name'] . ' - ' . $room['hotel_name'] : 'Room Details';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?> - SereneTripsLK</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .room-hero {
            position: relative;
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
            overflow: hidden;
            background-image: url('https://images.unsplash.com/photo-1631049307264-da0ec9d70304?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .room-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.8) 50%, rgba(0, 0, 0, 0.6) 100%);
            z-index: 1;
        }
        
        .room-hero::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.2;
            z-index: 2;
        }
        
        .room-hero-content {
            position: relative;
            z-index: 3;
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .room-hero-content h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, #ffffff 0%, #f0f8ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease-out;
        }
        
        .room-hero-content .hotel-name {
            font-size: 1.4rem;
            opacity: 0.95;
            margin-bottom: 1rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            animation: fadeInUp 1s ease-out 0.2s both;
        }
        
        .room-hero-content .location {
            font-size: 1.1rem;
            opacity: 0.85;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            animation: fadeInUp 1s ease-out 0.4s both;
        }
        
        .room-hero-content .location i {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .room-hero-content h1 {
                font-size: 2.5rem;
            }
            
            .room-hero-content .hotel-name {
                font-size: 1.2rem;
            }
            
            .room-hero-content .location {
                font-size: 1rem;
            }
        }
        
        .room-details-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .room-details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .room-image-section {
            position: relative;
        }
        
        .room-main-image {
            width: 100%;
            height: 400px;
            background-size: cover;
            background-position: center;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .room-info-section {
            padding: 20px 0;
        }
        
        .room-title {
            font-size: 2rem;
            color: #333;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        
        .room-price {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .price-amount {
            font-size: 2.5rem;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .price-period {
            color: #666;
            font-size: 1rem;
        }
        
        .room-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .feature-item i {
            color: #667eea;
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .room-description {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .room-description h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        
        .room-description p {
            color: #666;
            line-height: 1.6;
            font-size: 1rem;
        }
        
        .room-amenities {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .room-amenities h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .amenities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .amenity-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
        }
        
        .amenity-item i {
            color: #28a745;
            margin-right: 10px;
            width: 20px;
        }
        
        .booking-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            text-align: center;
        }
        
        .booking-section h3 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        
        .booking-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .booking-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 5px 0;
        }
        
        .booking-info-item:last-child {
            margin-bottom: 0;
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        
        .btn-book {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.3s ease;
        }
        
        .btn-book:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 40px auto;
            max-width: 600px;
        }
        
        @media (max-width: 768px) {
            .room-details-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .room-hero-content h1 {
                font-size: 2rem;
            }
            
            .room-title {
                font-size: 1.5rem;
            }
            
            .price-amount {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Header -->
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
            <h2><i class="fas fa-exclamation-triangle"></i> Error</h2>
            <p><?= htmlspecialchars($error) ?></p>
            <a href="hotels.php" class="btn btn-secondary">Back to Hotels</a>
        </div>
    <?php else: ?>
        <!-- Room Hero Section -->
        <section class="room-hero">
            <div class="room-hero-content">
                <h1><?= htmlspecialchars($room['type_name']) ?></h1>
                <div class="hotel-name"><?= htmlspecialchars($room['hotel_name']) ?></div>
                <div class="location">
                    <i class="fas fa-map-marker-alt"></i>
                    <?= htmlspecialchars($room['address'] . ', ' . $room['city']) ?>
                </div>
            </div>
        </section>

        <!-- Room Details -->
        <div class="room-details-container">
            <div class="room-details-grid">
                <!-- Room Image -->
                <div class="room-image-section">
                    <?php 
                    $images = json_decode($room['images'] ?? '[]', true);
                    $main_image = $images[0] ?? 'uploads/Hotels/Colombo/Room-1.png';
                    ?>
                    <div class="room-main-image" style="background-image: url('<?= htmlspecialchars($main_image) ?>')"></div>
                </div>

                <!-- Room Info -->
                <div class="room-info-section">
                    <h2 class="room-title"><?= htmlspecialchars($room['type_name']) ?></h2>
                    
                    <div class="room-price">
                        <div class="price-amount">$<?= number_format($room['base_price'], 2) ?></div>
                        <div class="price-period">per night</div>
                    </div>

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

                    <div class="booking-section">
                        <h3>Ready to Book?</h3>
                        <div class="booking-info">
                            <div class="booking-info-item">
                                <span>Room Type:</span>
                                <span><?= htmlspecialchars($room['type_name']) ?></span>
                            </div>
                            <div class="booking-info-item">
                                <span>Price per night:</span>
                                <span>$<?= number_format($room['base_price'], 2) ?></span>
                            </div>
                            <div class="booking-info-item">
                                <span>Max Occupancy:</span>
                                <span><?= $room['max_occupancy'] ?> guests</span>
                            </div>
                        </div>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php 
                            $booking_url = "booking.php?hotel_id=" . $room['hotel_id'] . "&room_type_id=" . $room['id'];
                            if ($check_in) $booking_url .= "&check_in=" . urlencode($check_in);
                            if ($check_out) $booking_url .= "&check_out=" . urlencode($check_out);
                            if ($guests) $booking_url .= "&guests=" . $guests;
                            ?>
                            <a href="<?= $booking_url ?>" class="btn-book">
                                <i class="fas fa-calendar-check"></i> Book This Room
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn-book">
                                <i class="fas fa-sign-in-alt"></i> Login to Book
                            </a>
                        <?php endif; ?>
                        
                        <a href="hotels.php" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Hotels
                        </a>
                    </div>
                </div>
            </div>

            <!-- Room Description -->
            <div class="room-description">
                <h3>About This Room</h3>
                <p><?= htmlspecialchars($room['description']) ?></p>
            </div>

            <!-- Room Amenities -->
            <?php if ($room['amenities']): ?>
            <div class="room-amenities">
                <h3>Room Amenities</h3>
                <div class="amenities-grid">
                    <?php 
                    $amenities = json_decode($room['amenities'], true);
                    if (is_array($amenities)):
                        foreach ($amenities as $amenity):
                    ?>
                        <div class="amenity-item">
                            <i class="fas fa-check"></i>
                            <span><?= htmlspecialchars($amenity) ?></span>
                        </div>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Flower Garden Hotels</h3>
                    <p>Experience luxury and comfort across our four beautiful locations in Sri Lanka. From bustling Colombo to serene tea plantations, we offer exceptional hospitality.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h4>Our Locations</h4>
                    <ul>
                        <li><a href="hotels.php?city=Colombo">Colombo</a></li>
                        <li><a href="hotels.php?city=Ella">Ella</a></li>
                        <li><a href="hotels.php?city=Matara">Matara</a></li>
                        <li><a href="hotels.php?city=Nuwara Eliya">Nuwara Eliya</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="home.php">Home</a></li>
                        <li><a href="hotels.php">Hotels</a></li>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Register</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <div class="contact-info">
                        <p><i class="fas fa-phone"></i> +94 11 234 5678</p>
                        <p><i class="fas fa-envelope"></i> info@flowergarden.com</p>
                        <p><i class="fas fa-map-marker-alt"></i> Sri Lanka</p>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2024 Flower Garden Hotels. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Header scroll effect - Exact match with homepage
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // User dropdown functionality
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const userAvatar = document.querySelector('.user-avatar');
            
            if (!userAvatar.contains(event.target) && dropdown.classList.contains('active')) {
                dropdown.classList.remove('active');
            }
        });

        // Mobile menu toggle - Exact match with homepage
        document.getElementById('menu-btn').addEventListener('click', function() {
            const navbar = document.querySelector('.navbar');
            const menuBtn = document.getElementById('menu-btn');
            const body = document.body;
            
            navbar.classList.toggle('active');
            menuBtn.classList.toggle('fa-times');
            body.classList.toggle('menu-open');
        });

        // Close mobile menu when clicking on a link
        document.querySelectorAll('.navbar a').forEach(link => {
            link.addEventListener('click', function() {
                const navbar = document.querySelector('.navbar');
                const menuBtn = document.getElementById('menu-btn');
                const body = document.body;
                
                navbar.classList.remove('active');
                menuBtn.classList.remove('fa-times');
                body.classList.remove('menu-open');
            });
        });
    </script>
</body>
</html>
