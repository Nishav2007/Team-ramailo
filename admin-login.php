<?php
/**
 * ADMIN LOGIN - Hardcoded Credentials
 * Username: admin
 * Password: admin123
 */
require_once 'config.php';

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Hardcoded credentials check - NO database
    if ($username === 'admin' && $password === 'admin123') {
        // Login successful
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = 'admin';
        $_SESSION['admin_id'] = 1;
        
        header('Location: admin-panel.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - GoodDream</title>
    <link rel="stylesheet" href="gooddream-theme.css">
    <style>
        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 0.5rem 1rem;
            border-radius: 999px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            border: 2px solid #f59e0b;
        }
        .credentials-hint {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 1.5rem;
            border-radius: 12px;
            margin-top: 2rem;
            border: 2px solid #f59e0b;
        }
        .credentials-hint code {
            background: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: var(--teal-dark);
            font-weight: 600;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" style="background: rgba(255, 255, 255, 0.98);">
        <div class="container">
            <a href="index.php" class="navbar-brand">GoodDream</a>
            <div class="navbar-links">
                <a href="index.php">Home</a>
                <a href="login.php">User Login</a>
            </div>
        </div>
    </nav>

    <!-- Admin Login Form -->
    <div class="form-container">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="admin-badge">🔒 ADMIN ACCESS</div>
            <div class="css-icon icon-user" style="margin: 1rem auto;"></div>
        </div>
        
        <h1 class="text-center" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Admin Panel Login</h1>
        <p class="text-center" style="color: #666; margin-bottom: 2rem;">Authorized personnel only</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="admin-login.php">
            <div class="form-group">
                <label style="color: #d97706;">Admin Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="Enter admin username" required autofocus>
            </div>

            <div class="form-group">
                <label style="color: #d97706;">Admin Password</label>
                <input type="password" name="password" placeholder="Enter admin password" required>
            </div>

            <button type="submit" class="btn btn-block" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);">
                🔐 Login to Admin Panel
            </button>
        </form>

        <div class="credentials-hint">
            <p style="margin-bottom: 0.75rem; font-weight: 600; color: #92400e;">For Testing:</p>
            <p style="margin-bottom: 0.5rem; color: #78350f;">Username: <code>admin</code></p>
            <p style="color: #78350f;">Password: <code>admin123</code></p>
        </div>
    </div>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 2px 20px rgba(245, 158, 11, 0.2)';
            } else {
                navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.1)';
            }
        });
    </script>
</body>
</html>
