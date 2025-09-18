<?php
/**
 * User Authentication Functions
 * Handles user registration, login, and session management
 */

require_once 'config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Register a new user
 */
function registerUser($data) {
    global $pdo;
    
    try {
        // Validate required fields
        $required_fields = ['first_name', 'last_name', 'email', 'password', 'confirm_password'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                throw new Exception("All fields are required");
            }
        }
        
        // Validate email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        // Check if passwords match
        if ($data['password'] !== $data['confirm_password']) {
            throw new Exception("Passwords do not match");
        }
        
        // Check password strength
        if (strlen($data['password']) < 6) {
            throw new Exception("Password must be at least 6 characters long");
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            throw new Exception("Email already registered");
        }
        
        // Hash password
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        
        // Insert new user
        $stmt = $pdo->prepare("
            INSERT INTO users (first_name, last_name, email, password, phone, address, date_of_birth) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            sanitizeInput($data['first_name']),
            sanitizeInput($data['last_name']),
            sanitizeInput($data['email']),
            $hashed_password,
            sanitizeInput($data['phone'] ?? ''),
            sanitizeInput($data['address'] ?? ''),
            $data['date_of_birth'] ?? null
        ]);
        
        return [
            'success' => true,
            'message' => 'Registration successful! Your account has been created.',
            'user_id' => $pdo->lastInsertId()
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * Login user
 */
function loginUser($email, $password) {
    global $pdo;
    
    try {
        // Find user by email
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password, is_active FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception("Invalid email or password");
        }
        
        // Check if account is active
        if (!$user['is_active']) {
            throw new Exception("Account is deactivated. Please contact support.");
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            throw new Exception("Invalid email or password");
        }
        
        // Set session variables
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['login_time'] = time();
        
        return [
            'success' => true,
            'message' => 'Login successful!',
            'user' => $user
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

/**
 * Logout user
 */
function logoutUser() {
    // Destroy session
    session_destroy();
    
    // Start new session
    session_start();
    session_regenerate_id(true);
    
    return [
        'success' => true,
        'message' => 'Logged out successfully'
    ];
}

/**
 * Check if user is logged in
 */
function isUserLoggedIn() {
    if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
        return false;
    }
    
    // Check session age (24 hours)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 86400)) {
        logoutUser();
        return false;
    }
    
    return true;
}

/**
 * Get current user data
 */
function getCurrentUser() {
    if (!isUserLoggedIn()) {
        return null;
    }
    
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, address, date_of_birth FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Update user profile
 */
function updateUserProfile($data) {
    global $pdo;
    
    if (!isUserLoggedIn()) {
        return ['success' => false, 'message' => 'Not logged in'];
    }
    
    try {
        $stmt = $pdo->prepare("
            UPDATE users 
            SET first_name = ?, last_name = ?, phone = ?, address = ?, date_of_birth = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([
            sanitizeInput($data['first_name']),
            sanitizeInput($data['last_name']),
            sanitizeInput($data['phone'] ?? ''),
            sanitizeInput($data['address'] ?? ''),
            $data['date_of_birth'] ?? null,
            $_SESSION['user_id']
        ]);
        
        // Update session name
        $_SESSION['user_name'] = $data['first_name'] . ' ' . $data['last_name'];
        
        return [
            'success' => true,
            'message' => 'Profile updated successfully'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Failed to update profile: ' . $e->getMessage()
        ];
    }
}

/**
 * Change user password
 */
function changePassword($current_password, $new_password, $confirm_password) {
    global $pdo;
    
    if (!isUserLoggedIn()) {
        return ['success' => false, 'message' => 'Not logged in'];
    }
    
    try {
        // Get current password hash
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if (!$user) {
            throw new Exception("User not found");
        }
        
        // Verify current password
        if (!password_verify($current_password, $user['password'])) {
            throw new Exception("Current password is incorrect");
        }
        
        // Validate new password
        if ($new_password !== $confirm_password) {
            throw new Exception("New passwords do not match");
        }
        
        if (strlen($new_password) < 6) {
            throw new Exception("New password must be at least 6 characters long");
        }
        
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        
        return [
            'success' => true,
            'message' => 'Password changed successfully'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}
?>
