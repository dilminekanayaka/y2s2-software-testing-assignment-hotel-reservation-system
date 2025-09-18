<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Home - Flower Garden</title>

  <!-- FontAwesome + Swiper + CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="style.css" />
  <style>
    /* Inline styles to ensure they load */
    .hotel-details-section {
      padding: 8rem 5% 5rem !important;
      background: #f8f9fa !important;
    }
    
    .hotel-details-container {
      max-width: 1200px !important;
      margin: 0 auto !important;
    }
    
    .section-header {
      text-align: center !important;
      margin-bottom: 4rem !important;
    }
    
    .section-header h2 {
      font-size: 3rem !important;
      font-weight: 700 !important;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
      -webkit-background-clip: text !important;
      -webkit-text-fill-color: transparent !important;
      background-clip: text !important;
      margin-bottom: 1rem !important;
    }
    
    .section-header p {
      font-size: 1.2rem !important;
      color: #666 !important;
      max-width: 600px !important;
      margin: 0 auto !important;
    }
    
    .hotels-overview {
      display: grid !important;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
      gap: 2rem !important;
    }
    
    .hotel-card {
      background: white !important;
      border-radius: 20px !important;
      overflow: hidden !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
      transition: all 0.3s ease !important;
    }
    
    .hotel-card:hover {
      transform: translateY(-10px) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }
    
    .hotel-image {
      position: relative !important;
      height: 200px !important;
      overflow: hidden !important;
    }
    
    .hotel-image img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      transition: transform 0.3s ease !important;
    }
    
    .hotel-card:hover .hotel-image img {
      transform: scale(1.1) !important;
    }
    
    .hotel-overlay {
      position: absolute !important;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      height: 100% !important;
      background: linear-gradient(135deg, rgba(102, 126, 234, 0.8) 0%, rgba(118, 75, 162, 0.8) 100%) !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      opacity: 0 !important;
      transition: opacity 0.3s ease !important;
    }
    
    .hotel-card:hover .hotel-overlay {
      opacity: 1 !important;
    }
    
    .hotel-icon {
      width: 60px !important;
      height: 60px !important;
      background: rgba(255, 255, 255, 0.9) !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      color: #667eea !important;
      font-size: 1.5rem !important;
    }
    
    .hotel-content {
      padding: 2rem !important;
      text-align: center !important;
    }
    
    
    .hotel-card h3 {
      font-size: 1.8rem !important;
      font-weight: 600 !important;
      color: #333 !important;
      margin-bottom: 1rem !important;
    }
    
    .hotel-card p {
      color: #666 !important;
      line-height: 1.6 !important;
      margin-bottom: 1.5rem !important;
    }
    
    .hotel-rating {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 0.5rem !important;
      color: #ffd700 !important;
      font-weight: 600 !important;
    }
    
    /* Facilities Section */
    .facilities-section {
      padding: 8rem 5% 5rem !important;
      background: white !important;
    }
    
    .facilities-container {
      max-width: 1200px !important;
      margin: 0 auto !important;
    }
    
    .facilities-grid {
      display: grid !important;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)) !important;
      gap: 2rem !important;
    }
    
    .facility-item {
      text-align: center !important;
      padding: 2rem !important;
      border-radius: 15px !important;
      transition: all 0.3s ease !important;
    }
    
    .facility-item:hover {
      background: #f8f9fa !important;
      transform: translateY(-5px) !important;
    }
    
    .facility-icon {
      width: 70px !important;
      height: 70px !important;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      margin: 0 auto 1.5rem !important;
    }
    
    .facility-icon i {
      font-size: 2rem !important;
      color: white !important;
    }
    
    .facility-item h3 {
      font-size: 1.5rem !important;
      font-weight: 600 !important;
      color: #333 !important;
      margin-bottom: 1rem !important;
    }
    
    .facility-item p {
      color: #666 !important;
      line-height: 1.6 !important;
    }
    
    /* Customer Feedback Section */
    .feedback-section {
      padding: 8rem 5% 5rem !important;
      background: #f8f9fa !important;
    }
    
    .feedback-container {
      max-width: 1200px !important;
      margin: 0 auto !important;
    }
    
    .testimonials-grid {
      display: grid !important;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
      gap: 2rem !important;
    }
    
    .testimonial-card {
      background: white !important;
      padding: 2rem !important;
      border-radius: 20px !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
      transition: all 0.3s ease !important;
    }
    
    .testimonial-card:hover {
      transform: translateY(-5px) !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
    }
    
    .testimonial-content {
      margin-bottom: 1.5rem !important;
    }
    
    .stars {
      display: flex !important;
      gap: 0.3rem !important;
      margin-bottom: 1rem !important;
      color: #ffd700 !important;
    }
    
    .testimonial-content p {
      color: #666 !important;
      line-height: 1.6 !important;
      font-style: italic !important;
    }
    
    .testimonial-author {
      border-top: 1px solid #eee !important;
      padding-top: 1rem !important;
    }
    
    .author-info h4 {
      font-size: 1.2rem !important;
      font-weight: 600 !important;
      color: #333 !important;
      margin-bottom: 0.3rem !important;
    }
    
    .author-info span {
      color: #667eea !important;
      font-size: 0.9rem !important;
    }
    
    /* Footer Section */
    .footer {
      background: #2c3e50 !important;
      color: white !important;
      padding: 4rem 5% 2rem !important;
    }
    
    .footer-container {
      max-width: 1200px !important;
      margin: 0 auto !important;
    }
    
    .footer-content {
      display: grid !important;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
      gap: 3rem !important;
      margin-bottom: 2rem !important;
    }
    
    .footer-section h3 {
      font-size: 1.8rem !important;
      font-weight: 600 !important;
      margin-bottom: 1rem !important;
      color: #667eea !important;
    }
    
    .footer-section h4 {
      font-size: 1.3rem !important;
      font-weight: 600 !important;
      margin-bottom: 1rem !important;
      color: #667eea !important;
    }
    
    .footer-section p {
      color: #bdc3c7 !important;
      line-height: 1.6 !important;
      margin-bottom: 1.5rem !important;
    }
    
    .footer-section ul {
      list-style: none !important;
    }
    
    .footer-section ul li {
      margin-bottom: 0.8rem !important;
    }
    
    .footer-section ul li a {
      color: #bdc3c7 !important;
      text-decoration: none !important;
      transition: color 0.3s ease !important;
    }
    
    .footer-section ul li a:hover {
      color: #667eea !important;
    }
    
    .social-links {
      display: flex !important;
      gap: 1rem !important;
    }
    
    .social-links a {
      width: 40px !important;
      height: 40px !important;
      background: #667eea !important;
      border-radius: 50% !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      color: white !important;
      text-decoration: none !important;
      transition: all 0.3s ease !important;
    }
    
    .social-links a:hover {
      background: #764ba2 !important;
      transform: translateY(-3px) !important;
    }
    
    .contact-info p {
      display: flex !important;
      align-items: center !important;
      gap: 0.8rem !important;
      margin-bottom: 0.8rem !important;
      color: #bdc3c7 !important;
    }
    
    .contact-info i {
      color: #667eea !important;
      width: 20px !important;
    }
    
    .footer-bottom {
      border-top: 1px solid #34495e !important;
      padding-top: 2rem !important;
      text-align: center !important;
    }
    
    .footer-bottom p {
      color: #bdc3c7 !important;
      margin: 0 !important;
    }
    
    /* Image Slider Styles */
    .home .swiper-slide {
      position: relative !important;
      height: 100vh !important;
      overflow: hidden !important;
    }
    
    .image-container {
      position: absolute !important;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      height: 100% !important;
      z-index: 1 !important;
    }
    
    .image-container img {
      width: 100% !important;
      height: 100% !important;
      object-fit: cover !important;
      object-position: center !important;
      transition: transform 0.3s ease !important;
    }
    
    .image-overlay {
      position: absolute !important;
      top: 0 !important;
      left: 0 !important;
      width: 100% !important;
      height: 100% !important;
      background: linear-gradient(135deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.6) 100%) !important;
      z-index: 2 !important;
    }
    
    .home .swiper-slide .content {
      position: absolute !important;
      top: 20% !important;
      left: 50% !important;
      transform: translate(-50%, -50%) !important;
      text-align: center !important;
      z-index: 3 !important;
      color: white !important;
      max-width: 600px !important;
      padding: 0 2rem !important;
    }
    
    .home .swiper-slide .content span {
      font-size: 1.3rem !important;
      font-weight: 500 !important;
      color: rgba(255, 255, 255, 0.95) !important;
      display: block !important;
      margin-bottom: 1.5rem !important;
      text-transform: uppercase !important;
      letter-spacing: 3px !important;
      text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3) !important;
    }
    
    .home .swiper-slide .content h3 {
      font-size: 4rem !important;
      font-weight: 700 !important;
      margin-bottom: 0 !important;
      text-shadow: 0 3px 6px rgba(0, 0, 0, 0.6) !important;
      line-height: 1.1 !important;
      letter-spacing: -1px !important;
    }
    
    
    /* Swiper Navigation */
    .home .swiper-button-next,
    .home .swiper-button-prev {
      color: white !important;
      background: rgba(255, 255, 255, 0.1) !important;
      backdrop-filter: blur(10px) !important;
      border-radius: 50% !important;
      width: 60px !important;
      height: 60px !important;
      margin-top: -30px !important;
      transition: all 0.3s ease !important;
    }
    
    .home .swiper-button-next:hover,
    .home .swiper-button-prev:hover {
      background: rgba(255, 255, 255, 0.2) !important;
      transform: scale(1.1) !important;
    }
    
    .home .swiper-button-next:after,
    .home .swiper-button-prev:after {
      font-size: 20px !important;
      font-weight: 900 !important;
    }
    
    /* Swiper Pagination */
    .home .swiper-pagination {
      bottom: 30px !important;
    }
    
    .home .swiper-pagination-bullet {
      background: rgba(255, 255, 255, 0.5) !important;
      width: 12px !important;
      height: 12px !important;
      margin: 0 8px !important;
      transition: all 0.3s ease !important;
    }
    
    .home .swiper-pagination-bullet-active {
      background: white !important;
      transform: scale(1.2) !important;
    }
    
    /* Responsive Design for Image Slider */
    @media (max-width: 768px) {
      .home .swiper-slide .content {
        top: 12% !important;
        padding: 0 1rem !important;
      }
      
      .home .swiper-slide .content h3 {
        font-size: 2.5rem !important;
        letter-spacing: -0.5px !important;
      }
      
      .home .swiper-slide .content span {
        font-size: 1rem !important;
        letter-spacing: 2px !important;
        margin-bottom: 0.8rem !important;
      }
      
      .search-overlay {
        padding: 3rem 2% 1.5rem !important;
      }
      
      .home .swiper-button-next,
      .home .swiper-button-prev {
        width: 50px !important;
        height: 50px !important;
        margin-top: -25px !important;
      }
      
      .home .swiper-button-next:after,
      .home .swiper-button-prev:after {
        font-size: 16px !important;
      }
    }
    
    @media (max-width: 480px) {
      .home .swiper-slide .content {
        top: 10% !important;
      }
      
      .home .swiper-slide .content h3 {
        font-size: 2rem !important;
      }
      
      .home .swiper-slide .content span {
        font-size: 0.9rem !important;
        letter-spacing: 1.5px !important;
        margin-bottom: 0.6rem !important;
      }
      
      .search-overlay {
        padding: 2.5rem 2% 1rem !important;
      }
      
      .search-overlay .search-form {
        padding: 2rem !important;
      }
    }

    /* Search Overlay Styles */
    .search-overlay {
      position: absolute !important;
      bottom: 0 !important;
      left: 0 !important;
      right: 0 !important;
      top: 35% !important;
      background: linear-gradient(to top, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 50%, transparent 100%) !important;
      padding: 4rem 5% 2rem !important;
      z-index: 4 !important;
    }
    
    .search-form-container {
      max-width: 1000px !important;
      margin: 0 auto !important;
    }
    
    .search-overlay .search-header {
      text-align: center !important;
      margin-bottom: 2rem !important;
    }
    
    .search-overlay .search-header h2 {
      font-size: 2.5rem !important;
      font-weight: 700 !important;
      color: white !important;
      margin-bottom: 0.5rem !important;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5) !important;
    }
    
    .search-overlay .search-header p {
      font-size: 1.1rem !important;
      color: rgba(255, 255, 255, 0.9) !important;
      max-width: 600px !important;
      margin: 0 auto !important;
    }
    
    .search-overlay .search-form {
      background: rgba(255, 255, 255, 0.95) !important;
      backdrop-filter: blur(10px) !important;
      border-radius: 20px !important;
      padding: 2.5rem !important;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
    }
    
    .search-overlay .form-grid {
      display: grid !important;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)) !important;
      gap: 1.5rem !important;
      margin-bottom: 1.5rem !important;
    }
    
    .search-overlay .form-group {
      display: flex !important;
      flex-direction: column !important;
    }
    
    .search-overlay .form-group label {
      font-weight: 600 !important;
      color: #333 !important;
      margin-bottom: 0.5rem !important;
      font-size: 0.9rem !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
    }
    
    .search-overlay .form-group select,
    .search-overlay .form-group input {
      padding: 1rem !important;
      border: 2px solid #e1e5e9 !important;
      border-radius: 10px !important;
      font-size: 1rem !important;
      transition: all 0.3s ease !important;
      background: white !important;
    }
    
    .search-overlay .form-group select:focus,
    .search-overlay .form-group input:focus {
      outline: none !important;
      border-color: #667eea !important;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
    }
    
    .search-overlay .search-btn {
      width: 100% !important;
      padding: 1.2rem 2rem !important;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
      color: white !important;
      border: none !important;
      border-radius: 10px !important;
      font-size: 1.1rem !important;
      font-weight: 600 !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 0.5rem !important;
    }
    
    .search-overlay .search-btn:hover {
      transform: translateY(-2px) !important;
      box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3) !important;
    }
    
    /* User avatar dropdown styling */
    .user-avatar-dropdown {
      position: relative !important;
      margin-left: 1rem !important;
    }
    
    .user-avatar-dropdown:hover .dropdown-menu {
      opacity: 1 !important;
      visibility: visible !important;
      transform: translateY(0) !important;
    }
    
    .user-avatar-dropdown:hover .user-avatar .dropdown-arrow {
      transform: rotate(180deg) !important;
    }
    
    .user-avatar {
      display: flex !important;
      align-items: center !important;
      gap: 0.75rem !important;
      padding: 0.75rem 1.5rem !important;
      background: rgba(102, 126, 234, 0.1) !important;
      border-radius: 30px !important;
      border: 1px solid rgba(102, 126, 234, 0.2) !important;
      cursor: pointer !important;
      transition: all 0.3s ease !important;
      color: #667eea !important;
      font-weight: 600 !important;
      font-size: 1.1rem !important;
    }
    
    .user-avatar:hover {
      background: rgba(102, 126, 234, 0.2) !important;
      transform: translateY(-1px) !important;
    }
    
    .user-avatar i.fa-user-circle {
      font-size: 1.5rem !important;
    }
    
    .dropdown-arrow {
      font-size: 0.8rem !important;
      transition: transform 0.3s ease !important;
    }
    
    .user-avatar.active .dropdown-arrow {
      transform: rotate(180deg) !important;
    }
    
    .dropdown-menu {
      position: absolute !important;
      top: 100% !important;
      right: 0 !important;
      background: white !important;
      border-radius: 15px !important;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15) !important;
      border: 1px solid #e1e5e9 !important;
      min-width: 250px !important;
      z-index: 1000 !important;
      opacity: 0 !important;
      visibility: hidden !important;
      transform: translateY(-10px) !important;
      transition: all 0.3s ease !important;
      margin-top: 0.5rem !important;
    }
    
    .dropdown-menu.show {
      opacity: 1 !important;
      visibility: visible !important;
      transform: translateY(0) !important;
    }
    
    .dropdown-header {
      padding: 1rem !important;
      border-bottom: 1px solid #f1f3f4 !important;
    }
    
    .user-info {
      display: flex !important;
      align-items: center !important;
      gap: 0.75rem !important;
    }
    
    .user-info i {
      font-size: 2rem !important;
      color: #667eea !important;
    }
    
    .user-full-name {
      font-weight: 600 !important;
      color: #333 !important;
      font-size: 1.1rem !important;
    }
    
    .user-email {
      color: #666 !important;
      font-size: 0.95rem !important;
    }
    
    .dropdown-divider {
      height: 1px !important;
      background: #f1f3f4 !important;
      margin: 0.5rem 0 !important;
    }
    
    .dropdown-item {
      display: flex !important;
      align-items: center !important;
      gap: 0.75rem !important;
      padding: 0.75rem 1rem !important;
      color: #333 !important;
      text-decoration: none !important;
      transition: all 0.2s ease !important;
      font-size: 1rem !important;
      font-weight: 500 !important;
    }
    
    .user-avatar-dropdown .dropdown-item {
      font-size: 1rem !important;
    }
    
    .user-avatar-dropdown .dropdown-item i {
      font-size: 1rem !important;
    }
    
    .dropdown-item:hover {
      background: #f8f9fa !important;
      color: #667eea !important;
    }
    
    .dropdown-item.logout {
      color: #dc3545 !important;
    }
    
    .dropdown-item.logout:hover {
      background: #f8d7da !important;
      color: #721c24 !important;
    }
    
    .dropdown-item i {
      width: 16px !important;
      text-align: center !important;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
      .search-overlay .search-header h2 {
        font-size: 2rem !important;
      }
      
      .search-overlay {
        padding: 3rem 2% 1.5rem !important;
      }
      
      .search-overlay .search-form {
        padding: 2rem !important;
      }
      
      .search-overlay .form-grid {
        grid-template-columns: 1fr !important;
        gap: 1rem !important;
      }
      
      .section-header h2 {
        font-size: 2.5rem !important;
      }
      
      .hotels-overview {
        grid-template-columns: 1fr !important;
      }
      
      .facilities-grid {
        grid-template-columns: 1fr !important;
      }
      
      .testimonials-grid {
        grid-template-columns: 1fr !important;
      }
      
      .footer-content {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
      }
      
      .hotel-details-section,
      .facilities-section,
      .feedback-section {
        padding: 6rem 2% 3rem !important;
      }
    }
  </style>
</head>
<body>
  <section class="header">
    <a href="home.php" class="logo">Flower Garden</a>
    <nav class="navbar">
      <a href="home.php" class="active">home</a>
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

  <section class="home">
    <div class="swiper home-slider">
      <div class="swiper-wrapper">
        
        <div class="swiper-slide slide">
          <div class="image-container">
            <img src="uploads/Hotels/Colombo/Hotel-1.png" alt="Flower Garden - Colombo" />
            <div class="image-overlay"></div>
          </div>
          <div class="content">
            <span>luxury, comfort, relaxation</span>
            <h3>find your perfect room</h3>
          </div>
          
          <!-- Search Form Overlay -->
          <div class="search-overlay">
            <div class="search-form-container">
              <div class="search-header">
                <h2>Find Your Perfect Hotel</h2>
                <p>Discover luxury accommodations at Flower Garden branches across Sri Lanka</p>
              </div>
              
              <form method="GET" action="hotels.php" class="search-form">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="city">Select City</label>
                    <select id="city" name="city" required>
                      <option value="">Choose a city</option>
                      <option value="Colombo">Colombo - Western Province</option>
                      <option value="Ella">Ella - Uva Province</option>
                      <option value="Matara">Matara - Southern Province</option>
                      <option value="Nuwara Eliya">Nuwara Eliya - Central Province</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_in">Check-in Date</label>
                    <input type="date" id="check_in" name="check_in" min="<?= date('Y-m-d') ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_out">Check-out Date</label>
                    <input type="date" id="check_out" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="guests">Guests</label>
                    <select id="guests" name="guests">
                      <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
                
                <button type="submit" class="search-btn">
                  <i class="fas fa-search"></i> Search Hotels
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="swiper-slide slide">
          <div class="image-container">
            <img src="uploads/Hotels/Ella/Hotel-1.png" alt="Flower Garden - Ella" />
            <div class="image-overlay"></div>
          </div>
          <div class="content">
            <span>premium, elegant, memorable</span>
            <h3>experience luxury hospitality</h3>
          </div>
          
          <!-- Search Form Overlay -->
          <div class="search-overlay">
            <div class="search-form-container">
              <div class="search-header">
                <h2>Find Your Perfect Hotel</h2>
                <p>Discover luxury accommodations at Flower Garden branches across Sri Lanka</p>
              </div>
              
              <form method="GET" action="hotels.php" class="search-form">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="city2">Select City</label>
                    <select id="city2" name="city" required>
                      <option value="">Choose a city</option>
                      <option value="Colombo">Colombo - Western Province</option>
                      <option value="Ella">Ella - Uva Province</option>
                      <option value="Matara">Matara - Southern Province</option>
                      <option value="Nuwara Eliya">Nuwara Eliya - Central Province</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_in2">Check-in Date</label>
                    <input type="date" id="check_in2" name="check_in" min="<?= date('Y-m-d') ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_out2">Check-out Date</label>
                    <input type="date" id="check_out2" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="guests2">Guests</label>
                    <select id="guests2" name="guests">
                      <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
                
                <button type="submit" class="search-btn">
                  <i class="fas fa-search"></i> Search Hotels
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="swiper-slide slide">
          <div class="image-container">
            <img src="uploads/Hotels/Nuwara Eliya/Hotel-1.png" alt="Flower Garden - Nuwara Eliya" />
            <div class="image-overlay"></div>
          </div>
          <div class="content">
            <span>Discover, Relax, Enjoy</span>
            <h3>Your Dream Stay Awaits</h3>
          </div>
          
          <!-- Search Form Overlay -->
          <div class="search-overlay">
            <div class="search-form-container">
              <div class="search-header">
                <h2>Find Your Perfect Hotel</h2>
                <p>Discover luxury accommodations at Flower Garden branches across Sri Lanka</p>
              </div>
              
              <form method="GET" action="hotels.php" class="search-form">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="city3">Select City</label>
                    <select id="city3" name="city" required>
                      <option value="">Choose a city</option>
                      <option value="Colombo">Colombo - Western Province</option>
                      <option value="Ella">Ella - Uva Province</option>
                      <option value="Matara">Matara - Southern Province</option>
                      <option value="Nuwara Eliya">Nuwara Eliya - Central Province</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_in3">Check-in Date</label>
                    <input type="date" id="check_in3" name="check_in" min="<?= date('Y-m-d') ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_out3">Check-out Date</label>
                    <input type="date" id="check_out3" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="guests3">Guests</label>
                    <select id="guests3" name="guests">
                      <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
                
                <button type="submit" class="search-btn">
                  <i class="fas fa-search"></i> Search Hotels
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="swiper-slide slide">
          <div class="image-container">
            <img src="uploads/Hotels/Matara/Matara-1.png" alt="Flower Garden - Matara" />
            <div class="image-overlay"></div>
          </div>
          <div class="content">
            <span>Nature, Serenity, Bliss</span>
            <h3>Flower Garden Experience</h3>
          </div>
          
          <!-- Search Form Overlay -->
          <div class="search-overlay">
            <div class="search-form-container">
              <div class="search-header">
                <h2>Find Your Perfect Hotel</h2>
                <p>Discover luxury accommodations at Flower Garden branches across Sri Lanka</p>
              </div>
              
              <form method="GET" action="hotels.php" class="search-form">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="city4">Select City</label>
                    <select id="city4" name="city" required>
                      <option value="">Choose a city</option>
                      <option value="Colombo">Colombo - Western Province</option>
                      <option value="Ella">Ella - Uva Province</option>
                      <option value="Matara">Matara - Southern Province</option>
                      <option value="Nuwara Eliya">Nuwara Eliya - Central Province</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_in4">Check-in Date</label>
                    <input type="date" id="check_in4" name="check_in" min="<?= date('Y-m-d') ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_out4">Check-out Date</label>
                    <input type="date" id="check_out4" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="guests4">Guests</label>
                    <select id="guests4" name="guests">
                      <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
                
                <button type="submit" class="search-btn">
                  <i class="fas fa-search"></i> Search Hotels
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="swiper-slide slide">
          <div class="image-container">
            <img src="uploads/Hotels/Colombo/Hotel-2.png" alt="Flower Garden - Colombo Premium" />
            <div class="image-overlay"></div>
          </div>
          <div class="content">
            <span>Relaxation, Wellness, Tranquility</span>
            <h3>Premium Amenities</h3>
          </div>
          
          <!-- Search Form Overlay -->
          <div class="search-overlay">
            <div class="search-form-container">
              <div class="search-header">
                <h2>Find Your Perfect Hotel</h2>
                <p>Discover luxury accommodations at Flower Garden branches across Sri Lanka</p>
              </div>
              
              <form method="GET" action="hotels.php" class="search-form">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="city5">Select City</label>
                    <select id="city5" name="city" required>
                      <option value="">Choose a city</option>
                      <option value="Colombo">Colombo - Western Province</option>
                      <option value="Ella">Ella - Uva Province</option>
                      <option value="Matara">Matara - Southern Province</option>
                      <option value="Nuwara Eliya">Nuwara Eliya - Central Province</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_in5">Check-in Date</label>
                    <input type="date" id="check_in5" name="check_in" min="<?= date('Y-m-d') ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_out5">Check-out Date</label>
                    <input type="date" id="check_out5" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="guests5">Guests</label>
                    <select id="guests5" name="guests">
                      <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
                
                <button type="submit" class="search-btn">
                  <i class="fas fa-search"></i> Search Hotels
                </button>
              </form>
            </div>
          </div>
        </div>

        <div class="swiper-slide slide">
          <div class="image-container">
            <img src="uploads/Hotels/Ella/Hotel-2.png" alt="Flower Garden - Ella Premium" />
            <div class="image-overlay"></div>
          </div>
          <div class="content">
            <span>Fine Dining, Culinary Excellence</span>
            <h3>Gourmet Experience</h3>
          </div>
          
          <!-- Search Form Overlay -->
          <div class="search-overlay">
            <div class="search-form-container">
              <div class="search-header">
                <h2>Find Your Perfect Hotel</h2>
                <p>Discover luxury accommodations at Flower Garden branches across Sri Lanka</p>
              </div>
              
              <form method="GET" action="hotels.php" class="search-form">
                <div class="form-grid">
                  <div class="form-group">
                    <label for="city6">Select City</label>
                    <select id="city6" name="city" required>
                      <option value="">Choose a city</option>
                      <option value="Colombo">Colombo - Western Province</option>
                      <option value="Ella">Ella - Uva Province</option>
                      <option value="Matara">Matara - Southern Province</option>
                      <option value="Nuwara Eliya">Nuwara Eliya - Central Province</option>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_in6">Check-in Date</label>
                    <input type="date" id="check_in6" name="check_in" min="<?= date('Y-m-d') ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="check_out6">Check-out Date</label>
                    <input type="date" id="check_out6" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                  </div>
                  
                  <div class="form-group">
                    <label for="guests6">Guests</label>
                    <select id="guests6" name="guests">
                      <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?> <?= $i === 1 ? 'Guest' : 'Guests' ?></option>
                      <?php endfor; ?>
                    </select>
                  </div>
                </div>
                
                <button type="submit" class="search-btn">
                  <i class="fas fa-search"></i> Search Hotels
                </button>
              </form>
            </div>
          </div>
        </div>

      </div>

      <!-- Swiper buttons -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      
      <!-- Swiper pagination -->
      <div class="swiper-pagination"></div>
    </div>
  </section>


  <!-- Hotel Details Section -->
  <section class="hotel-details-section">
    <div class="hotel-details-container">
      <div class="section-header">
        <h2>About Flower Garden Hotels</h2>
        <p>Experience luxury and comfort across our four beautiful locations in Sri Lanka</p>
      </div>
      
      <div class="hotels-overview">
        <div class="hotel-card">
          <div class="hotel-image">
            <img src="uploads/Hotels/Colombo/Hotel-1.png" alt="Flower Garden - Colombo" />
            <div class="hotel-overlay">
              <div class="hotel-icon">
                <i class="fas fa-city"></i>
              </div>
            </div>
          </div>
          <div class="hotel-content">
            <h3>Flower Garden - Colombo</h3>
            <p>A modern city hotel in the heart of Colombo offering business and leisure facilities. Conveniently located with easy access to shopping, dining, and cultural attractions.</p>
            <div class="hotel-rating">
              <i class="fas fa-star"></i>
              <span>4.4/5 Rating</span>
            </div>
          </div>
        </div>
        
        <div class="hotel-card">
          <div class="hotel-image">
            <img src="uploads/Hotels/Ella/Hotel-1.png" alt="Flower Garden - Ella" />
            <div class="hotel-overlay">
              <div class="hotel-icon">
                <i class="fas fa-mountain"></i>
              </div>
            </div>
          </div>
          <div class="hotel-content">
            <h3>Flower Garden - Ella</h3>
            <p>A luxurious boutique hotel nestled in the heart of Ella, surrounded by lush tea plantations and offering breathtaking mountain views. Experience the perfect blend of modern comfort and traditional Sri Lankan hospitality.</p>
            <div class="hotel-rating">
              <i class="fas fa-star"></i>
              <span>4.8/5 Rating</span>
            </div>
          </div>
        </div>
        
        <div class="hotel-card">
          <div class="hotel-image">
            <img src="uploads/Hotels/Matara/Matara-1.png" alt="Flower Garden - Matara" />
            <div class="hotel-overlay">
              <div class="hotel-icon">
                <i class="fas fa-water"></i>
              </div>
            </div>
          </div>
          <div class="hotel-content">
            <h3>Flower Garden - Matara</h3>
            <p>A serene beachfront hotel in Matara offering stunning ocean views and tropical gardens. Perfect for relaxation and beach activities with modern amenities and warm hospitality.</p>
            <div class="hotel-rating">
              <i class="fas fa-star"></i>
              <span>4.6/5 Rating</span>
            </div>
          </div>
        </div>
        
        <div class="hotel-card">
          <div class="hotel-image">
            <img src="uploads/Hotels/Nuwara Eliya/Hotel-1.png" alt="Flower Garden - Nuwara Eliya" />
            <div class="hotel-overlay">
              <div class="hotel-icon">
                <i class="fas fa-leaf"></i>
              </div>
            </div>
          </div>
          <div class="hotel-content">
            <h3>Flower Garden - Nuwara Eliya</h3>
            <p>A charming hill station hotel in Nuwara Eliya surrounded by tea plantations and cool mountain air. Experience the beauty of Sri Lanka's tea country with luxury accommodations.</p>
            <div class="hotel-rating">
              <i class="fas fa-star"></i>
              <span>4.7/5 Rating</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Facilities Section -->
  <section class="facilities-section">
    <div class="facilities-container">
      <div class="section-header">
        <h2>Our Premium Facilities</h2>
        <p>World-class amenities and services for your comfort and convenience</p>
      </div>
      
      <div class="facilities-grid">
        <div class="facility-item">
          <div class="facility-icon">
            <i class="fas fa-wifi"></i>
          </div>
          <h3>Free WiFi</h3>
          <p>High-speed internet access throughout all our properties</p>
        </div>
        
        <div class="facility-item">
          <div class="facility-icon">
            <i class="fas fa-swimming-pool"></i>
          </div>
          <h3>Swimming Pool</h3>
          <p>Refreshing pools with stunning views at select locations</p>
        </div>
        
        <div class="facility-item">
          <div class="facility-icon">
            <i class="fas fa-spa"></i>
          </div>
          <h3>Spa & Wellness</h3>
          <p>Relaxing spa treatments and wellness services</p>
        </div>
        
        <div class="facility-item">
          <div class="facility-icon">
            <i class="fas fa-utensils"></i>
          </div>
          <h3>Fine Dining</h3>
          <p>Exquisite restaurants serving local and international cuisine</p>
        </div>
        
        <div class="facility-item">
          <div class="facility-icon">
            <i class="fas fa-car"></i>
          </div>
          <h3>Free Parking</h3>
          <p>Complimentary parking facilities for all guests</p>
        </div>
        
        <div class="facility-item">
          <div class="facility-icon">
            <i class="fas fa-concierge-bell"></i>
          </div>
          <h3>24/7 Concierge</h3>
          <p>Round-the-clock concierge service for all your needs</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Customer Feedback Section -->
  <section class="feedback-section">
    <div class="feedback-container">
      <div class="section-header">
        <h2>What Our Guests Say</h2>
        <p>Real experiences from our valued customers</p>
      </div>
      
      <div class="testimonials-grid">
        <div class="testimonial-card">
          <div class="testimonial-content">
            <div class="stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p>"Absolutely amazing stay at Flower Garden Ella! The mountain views were breathtaking and the service was exceptional. Highly recommended!"</p>
          </div>
          <div class="testimonial-author">
            <div class="author-info">
              <h4>Sarah Johnson</h4>
              <span>Ella Branch Guest</span>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-content">
            <div class="stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p>"Perfect beachfront location in Matara! The rooms were luxurious and the staff went above and beyond to make our stay memorable."</p>
          </div>
          <div class="testimonial-author">
            <div class="author-info">
              <h4>Michael Chen</h4>
              <span>Matara Branch Guest</span>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-content">
            <div class="stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p>"Business trip made comfortable at Flower Garden Colombo. Great location, excellent facilities, and professional service."</p>
          </div>
          <div class="testimonial-author">
            <div class="author-info">
              <h4>David Williams</h4>
              <span>Colombo Branch Guest</span>
            </div>
          </div>
        </div>
        
        <div class="testimonial-card">
          <div class="testimonial-content">
            <div class="stars">
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
              <i class="fas fa-star"></i>
            </div>
            <p>"Tea plantation views from Nuwara Eliya branch were incredible! The cool mountain air and luxury amenities made it perfect."</p>
          </div>
          <div class="testimonial-author">
            <div class="author-info">
              <h4>Emma Rodriguez</h4>
              <span>Nuwara Eliya Branch Guest</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

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

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="script.js"></script>
    <script>
      // Set minimum check-out date based on check-in date
      document.getElementById('check_in').addEventListener('change', function() {
        const checkInDate = this.value;
        const checkOutInput = document.getElementById('check_out');
        
        if (checkInDate) {
          const minCheckOut = new Date(checkInDate);
          minCheckOut.setDate(minCheckOut.getDate() + 1);
          checkOutInput.min = minCheckOut.toISOString().split('T')[0];
          
          // If current check-out is before new minimum, update it
          if (checkOutInput.value && checkOutInput.value <= checkInDate) {
            checkOutInput.value = minCheckOut.toISOString().split('T')[0];
          }
        }
      });

      // Image slider optimization
      document.addEventListener('DOMContentLoaded', function() {
        const images = document.querySelectorAll('.swiper-slide img');
        
        images.forEach(function(img) {
          img.addEventListener('load', function() {
            console.log('Image loaded successfully:', this.src);
          });
          
          img.addEventListener('error', function() {
            console.error('Image failed to load:', this.src);
            // Add fallback background image if image fails
            this.style.display = 'none';
            const container = this.closest('.image-container');
            if (container) {
              container.style.backgroundImage = 'url("https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920&h=1080&fit=crop")';
              container.style.backgroundSize = 'cover';
              container.style.backgroundPosition = 'center';
            }
          });
        });
        
        // Stop slider when city is selected
        const citySelects = document.querySelectorAll('select[name="city"]');
        citySelects.forEach(function(select) {
          select.addEventListener('change', function() {
            if (this.value !== '') {
              // Stop the swiper autoplay
              if (window.swiper) {
                window.swiper.autoplay.stop();
                console.log('Slider stopped - city selected:', this.value);
              }
            } else {
              // Resume autoplay if city is deselected
              if (window.swiper) {
                window.swiper.autoplay.start();
                console.log('Slider resumed - no city selected');
              }
            }
          });
        });
      });
    </script>
</body>
</html>