<?php
require_once 'auth.php';

// Redirect if already logged in
if (isUserLoggedIn()) {
    header("Location: home.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = registerUser($_POST);
    if ($result['success']) {
        $success = $result['message'] . " Please <a href='login.php'>click here to login</a> and start booking your perfect stay.";
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Flower Garden</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem;
        }
        
        .auth-form {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .auth-header p {
            color: #666;
            font-size: 1.1rem;
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
            background: rgba(255, 255, 255, 0.8);
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
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .auth-links {
            text-align: center;
            margin-top: 2rem;
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-success a {
            color: #155724;
            font-weight: 600;
            text-decoration: underline;
        }
        
        .alert-success a:hover {
            color: #0d4a1a;
            text-decoration: none;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .auth-container {
                padding: 1rem;
            }
            
            .auth-form {
                padding: 2rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
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
            <a href="login.php">login</a>
            <a href="register.php" class="active">register</a>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <div class="auth-container">
        <div class="auth-form">
            <div class="auth-header">
                <h1><i class="fas fa-seedling"></i> Flower Garden</h1>
                <p>Create your account to start booking</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>
            
            <!-- JavaScript validation messages -->
            <div id="validation-messages" style="display: none;">
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <span id="validation-text"></span>
                </div>
            </div>
            
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name *</label>
                        <input type="text" id="first_name" name="first_name" required 
                               value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name *</label>
                        <input type="text" id="last_name" name="last_name" required 
                               value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" required 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" id="date_of_birth" name="date_of_birth" 
                           value="<?= htmlspecialchars($_POST['date_of_birth'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" 
                           value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Sign In</a></p>
                <p><a href="home.php">← Back to Home</a></p>
            </div>
        </div>
    </div>
    
    <script src="script.js"></script>
    <script>
        // Enhanced form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const validationMessages = document.getElementById('validation-messages');
            const validationText = document.getElementById('validation-text');
            
            // Hide validation messages initially
            validationMessages.style.display = 'none';
            
            // Form submission validation
            form.addEventListener('submit', function(e) {
                const firstName = document.getElementById('first_name').value.trim();
                const lastName = document.getElementById('last_name').value.trim();
                const email = document.getElementById('email').value.trim();
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                // Clear previous validation messages
                validationMessages.style.display = 'none';
                
                // Validate required fields
                if (!firstName) {
                    showValidationError('First name is required');
                    e.preventDefault();
                    return false;
                }
                
                if (!lastName) {
                    showValidationError('Last name is required');
                    e.preventDefault();
                    return false;
                }
                
                if (!email) {
                    showValidationError('Email address is required');
                    e.preventDefault();
                    return false;
                }
                
                // Validate email format
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    showValidationError('Please enter a valid email address');
                    e.preventDefault();
                    return false;
                }
                
                if (!password) {
                    showValidationError('Password is required');
                    e.preventDefault();
                    return false;
                }
                
                if (password.length < 6) {
                    showValidationError('Password must be at least 6 characters long');
                    e.preventDefault();
                    return false;
                }
                
                if (!confirmPassword) {
                    showValidationError('Please confirm your password');
                    e.preventDefault();
                    return false;
                }
                
                if (password !== confirmPassword) {
                    showValidationError('Passwords do not match');
                    e.preventDefault();
                    return false;
                }
                
                // If all validations pass, allow form submission
                return true;
            });
            
            function showValidationError(message) {
                validationText.textContent = message;
                validationMessages.style.display = 'block';
                
                // Scroll to validation message
                validationMessages.scrollIntoView({ behavior: 'smooth' });
            }
            
            // Real-time password confirmation validation
            const passwordField = document.getElementById('password');
            const confirmPasswordField = document.getElementById('confirm_password');
            
            confirmPasswordField.addEventListener('input', function() {
                if (passwordField.value && confirmPasswordField.value) {
                    if (passwordField.value !== confirmPasswordField.value) {
                        confirmPasswordField.setCustomValidity('Passwords do not match');
                    } else {
                        confirmPasswordField.setCustomValidity('');
                    }
                }
            });
            
            // Real-time email validation
            const emailField = document.getElementById('email');
            emailField.addEventListener('blur', function() {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (this.value && !emailRegex.test(this.value)) {
                    this.setCustomValidity('Please enter a valid email address');
                } else {
                    this.setCustomValidity('');
                }
            });
        });
    </script>
</body>
</html>
