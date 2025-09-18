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
    if (isset($_POST['add_admin'])) {
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];
        $email = sanitizeInput($_POST['email']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password, email, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$username, $password, $email]);
            $success = "Admin user added successfully!";
        } catch (PDOException $e) {
            $error = "Error adding admin: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['update_admin'])) {
        $id = $_POST['admin_id'];
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];
        $email = sanitizeInput($_POST['email']);
        
        try {
            if (!empty($password)) {
                $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, password = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $password, $email, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ? WHERE id = ?");
                $stmt->execute([$username, $email, $id]);
            }
            $success = "Admin user updated successfully!";
        } catch (PDOException $e) {
            $error = "Error updating admin: " . $e->getMessage();
        }
    }
    
    if (isset($_POST['delete_admin'])) {
        $id = $_POST['admin_id'];
        
        try {
            $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
            $stmt->execute([$id]);
            $success = "Admin user deleted successfully!";
        } catch (PDOException $e) {
            $error = "Error deleting admin: " . $e->getMessage();
        }
    }
}

// Get all admins
try {
    $stmt = $pdo->query("SELECT * FROM admin_users ORDER BY created_at DESC");
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error loading admins: " . $e->getMessage();
    $admins = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - Flower Garden</title>
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
        
        .form-group input {
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
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        
        .data-table th,
        .data-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .data-table tr:hover {
            background: #f8f9fa;
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
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
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
            margin: 5% auto;
            padding: 2rem;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
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
            
            .action-buttons {
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
            <a href="manage_admins.php" class="active">admins</a>
            <a href="manage_rooms.php">rooms</a>
            <a href="manage_reservations.php">reservations</a>
            <a href="logout.php">logout</a>
        </nav>
        <div id="menu-btn" class="fas fa-bars"></div>
    </section>

    <section class="admin-portal">
        <div class="portal-container">
            <div class="portal-header">
                <h1>Admin Management Portal</h1>
            </div>

            <div class="portal-nav">
                <a href="dashboard.php" class="nav-btn">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="manage_admins.php" class="nav-btn active">
                    <i class="fas fa-users-cog"></i> Admins
                </a>
                <a href="manage_rooms.php" class="nav-btn">
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

            <!-- Add Admin Form -->
            <div class="crud-section">
                <h2 class="section-title">
                    <i class="fas fa-user-plus"></i>
                    Add New Admin
                </h2>
                
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                    </div>
                    
                    <button type="submit" name="add_admin" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Admin
                    </button>
                </form>
            </div>

            <!-- Admins List -->
            <div class="crud-section">
                <h2 class="section-title">
                    <i class="fas fa-users"></i>
                    Current Admins
                </h2>
                
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td><?= $admin['id'] ?></td>
                                <td><?= htmlspecialchars($admin['username']) ?></td>
                                <td><?= htmlspecialchars($admin['email']) ?></td>
                                <td><?= date('M j, Y', strtotime($admin['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-warning" onclick="editAdmin(<?= htmlspecialchars(json_encode($admin)) ?>)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <?php if ($admin['id'] != $_SESSION['admin_user_id']): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this admin?')">
                                                <input type="hidden" name="admin_id" value="<?= $admin['id'] ?>">
                                                <button type="submit" name="delete_admin" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Edit Admin Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Edit Admin</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            
            <form method="POST" id="editForm">
                <input type="hidden" name="admin_id" id="edit_admin_id">
                
                <div class="form-group">
                    <label for="edit_username">Username</label>
                    <input type="text" id="edit_username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_password">Password (leave blank to keep current)</label>
                    <input type="password" id="edit_password" name="password">
                </div>
                
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                
                <button type="submit" name="update_admin" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Admin
                </button>
            </form>
        </div>
    </div>

    <script src="../script.js"></script>
    <script>
        function editAdmin(admin) {
            document.getElementById('edit_admin_id').value = admin.id;
            document.getElementById('edit_username').value = admin.username;
            document.getElementById('edit_email').value = admin.email;
            document.getElementById('edit_password').value = '';
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
