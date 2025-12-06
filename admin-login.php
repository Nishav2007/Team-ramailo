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
    <title>Admin Login - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" style="background: #0A2A2E;">
        <div class="container">
            <a href="index.php" class="navbar-brand">🔒 <?= SITE_NAME ?> - Admin</a>
            <div class="navbar-links">
                <a href="index.php">Home</a>
                <a href="login.php">User Login</a>
            </div>
        </div>
    </nav>

    <!-- Admin Login Form -->
    <div class="form-container">
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div style="display: inline-block; background: #FEF3C7; color: #92400E; padding: 0.5rem 1rem; border-radius: 999px; font-weight: bold; font-size: 0.9rem; margin-bottom: 1rem;">
                🔒 ADMIN ACCESS
            </div>
        </div>
        
        <h1 class="text-center">Admin Panel Login</h1>
        <p class="text-center" style="color: #666; margin-bottom: 2rem;">Authorized personnel only</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="admin-login.php">
            <div class="form-group">
                <label>Admin Username</label>
                <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label>Admin Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-block" style="background: #F59E0B; color: white;">🔐 Login to Admin Panel</button>
        </form>

        <div style="background: #FEF3C7; padding: 1rem; border-radius: 8px; margin-top: 2rem; font-size: 0.9rem;">
            <p style="margin-bottom: 0.5rem;"><strong>For Testing:</strong></p>
            <p style="margin-bottom: 0.25rem;">Username: <code style="background: white; padding: 2px 6px; border-radius: 3px;">admin</code></p>
            <p>Password: <code style="background: white; padding: 2px 6px; border-radius: 3px;">admin123</code></p>
        </div>
    </div>
</body>
</html>

