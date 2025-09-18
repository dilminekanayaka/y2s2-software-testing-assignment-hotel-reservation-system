<?php
require_once 'auth.php';
require_once 'config.php';

// Redirect if not logged in
if (!isUserLoggedIn()) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';
$user = getCurrentUser();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        try {
            $result = updateUserProfile($_POST);
            if ($result['success']) {
                $success = $result['message'];
                // Refresh user data
                $user = getCurrentUser();
            } else {
                $error = $result['message'];
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    
    if ($action === 'change_password') {
        try {
            $result = changePassword($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password']);
            if ($result['success']) {
                $success = $result['message'];
            } else {
                $error = $result['message'];
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    
    if ($action === 'send_verification') {
        try {
            // Generate verification code
            $verification_code = rand(100000, 999999);
            $_SESSION['verification_code'] = $verification_code;
            $_SESSION['verification_time'] = time();
            
            // In a real application, you would send this via email
            // For demo purposes, we'll store it in session
            $success = "Verification code sent to your email: " . $user['email'] . " (Demo code: " . $verification_code . ")";
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
    
    if ($action === 'verify_and_change_password') {
        try {
            $entered_code = $_POST['verification_code'] ?? '';
            $stored_code = $_SESSION['verification_code'] ?? '';
            $verification_time = $_SESSION['verification_time'] ?? 0;
            
            // Check if verification code is valid and not expired (10 minutes)
            if ($entered_code !== $stored_code) {
                throw new Exception("Invalid verification code.");
            }
            
            if (time() - $verification_time > 600) { // 10 minutes
                throw new Exception("Verification code has expired. Please request a new one.");
            }
            
            $result = changePassword($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password']);
            if ($result['success']) {
                $success = $result['message'];
                // Clear verification data
                unset($_SESSION['verification_code']);
                unset($_SESSION['verification_time']);
            } else {
                $error = $result['message'];
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Settings - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .settings-container {
            min-height: 100vh;
            background: #f8f9fa;
            padding: 2rem 5%;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
            padding-top: 2rem;
        }
        
        .page-header h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
        }
        
        .page-header p {
            font-size: 1.2rem;
            color: #666;
        }
        
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .settings-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e1e5e9;
        }
        
        .settings-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
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
        
        .verification-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 1rem;
            border: 2px dashed #dee2e6;
        }
        
        .verification-section h4 {
            color: #495057;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }
        
        .verification-code {
            display: flex;
            gap: 1rem;
            align-items: end;
        }
        
        .verification-code input {
            flex: 1;
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
        
        @media (max-width: 768px) {
            .settings-container {
                padding: 1rem 2%;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .settings-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .verification-code {
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

    <div class="settings-container">
        <div class="page-header">
            <h1><i class="fas fa-user-cog"></i> User Settings</h1>
            <p>Manage your account settings and preferences</p>
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

        <div class="settings-grid">
            <!-- Profile Information -->
            <div class="settings-card">
                <h3><i class="fas fa-user"></i> Profile Information</h3>
                <form method="POST" action="">
                    <input type="hidden" name="action" value="update_profile">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required 
                                   value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required 
                                   value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" readonly 
                               value="<?= htmlspecialchars($user['email'] ?? '') ?>" 
                               style="background: #f8f9fa; color: #666;">
                        <small style="color: #666;">Email cannot be changed</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" 
                               value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" 
                               value="<?= htmlspecialchars($user['address'] ?? '') ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" 
                               value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Profile
                    </button>
                </form>
            </div>

            <!-- Password Change -->
            <div class="settings-card">
                <h3><i class="fas fa-lock"></i> Change Password</h3>
                
                <?php if (!isset($_SESSION['verification_code'])): ?>
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="send_verification">
                        <p style="color: #666; margin-bottom: 1.5rem;">
                            To change your password, we'll send a verification code to your email address.
                        </p>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-envelope"></i> Send Verification Code
                        </button>
                    </form>
                <?php else: ?>
                    <div class="verification-section">
                        <h4><i class="fas fa-shield-alt"></i> Email Verification Required</h4>
                        <p style="color: #666; margin-bottom: 1rem;">
                            Enter the verification code sent to your email and your new password.
                        </p>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="verify_and_change_password">
                            
                            <div class="form-group">
                                <label for="verification_code">Verification Code</label>
                                <input type="text" id="verification_code" name="verification_code" 
                                       placeholder="Enter 6-digit code" maxlength="6" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="current_password">Current Password</label>
                                <input type="password" id="current_password" name="current_password" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="new_password">New Password</label>
                                <input type="password" id="new_password" name="new_password" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="confirm_password">Confirm New Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <div style="display: flex; gap: 1rem;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key"></i> Change Password
                                </button>
                                <a href="user_settings.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="my_reservations.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reservations
            </a>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
