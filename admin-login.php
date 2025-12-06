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
        /* Lock Icon CSS */
        .icon-lock {
            width: 60px;
            height: 70px;
            position: relative;
            margin: 0 auto;
        }

        .icon-lock::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 35px;
            height: 25px;
            border: 5px solid #f59e0b;
            border-bottom: none;
            border-radius: 20px 20px 0 0;
        }

        .icon-lock::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 40px;
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .icon-lock-inner {
            position: absolute;
            bottom: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 12px;
            background: white;
            border-radius: 2px;
            z-index: 1;
        }

        .icon-lock-inner::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 4px;
            height: 4px;
            background: white;
            border-radius: 50%;
        }

        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            padding: 0.5rem 1.5rem;
            border-radius: 999px;
            font-weight: bold;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            border: 2px solid #fbbf24;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.2);
        }

        .btn-admin {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
        }

        .test-credentials {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            padding: 1.25rem;
            border-radius: 12px;
            margin-top: 2rem;
            font-size: 0.9rem;
            border: 2px solid #fbbf24;
        }

        .test-credentials strong {
            color: #92400e;
        }

        .test-credentials code {
            background: white;
            padding: 3px 8px;
            border-radius: 4px;
            color: #f59e0b;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
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
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="icon-lock">
                <div class="icon-lock-inner"></div>
            </div>
        </div>

        <div style="text-align: center;">
            <div class="admin-badge">ADMIN ACCESS</div>
        </div>
        
        <h1 class="text-center" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Admin Panel Login</h1>
        <p class="text-center" style="color: #666; margin-bottom: 2rem;">Authorized personnel only</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="admin-login.php">
            <div class="form-group">
                <label>Admin Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" placeholder="Enter admin username" required autofocus>
            </div>

            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="password" placeholder="Enter admin password" required>
            </div>

            <button type="submit" class="btn btn-admin btn-block">Login to Admin Panel</button>
        </form>

        <div class="test-credentials">
            <p style="margin-bottom: 0.75rem;"><strong>Test Credentials:</strong></p>
            <p style="margin-bottom: 0.5rem; color: #78350f;">Username: <code>admin</code></p>
            <p style="color: #78350f;">Password: <code>admin123</code></p>
        </div>
    </div>

    <script>
        // Smooth scroll
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.2)';
            } else {
                navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.1)';
            }
        });
    </script>
</body>
</html>

