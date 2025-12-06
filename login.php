<?php
/**
 * USER LOGIN
 */
require_once 'config.php';

$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        // Get user from database
        $stmt = $conn->prepare("
            SELECT u.id, u.name, u.email, u.password, u.location_id, l.location_name 
            FROM users u 
            JOIN locations l ON u.location_id = l.id 
            WHERE u.email = ?
        ");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error = 'Invalid email or password';
        } else {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Login successful - create session
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_location_id'] = $user['location_id'];
                $_SESSION['user_location_name'] = $user['location_name'];
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">💧 <?= SITE_NAME ?></a>
            <div class="navbar-links">
                <a href="index.php">Home</a>
                <a href="register.php">Register</a>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="form-container">
        <h1 class="text-center">Welcome Back</h1>
        <p class="text-center" style="color: #666; margin-bottom: 2rem;">Login to view your dashboard</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Registration successful! Please login below.</div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p class="text-center" style="margin-top: 1.5rem;">
            Don't have an account? <a href="register.php" style="color: #256A73; font-weight: 600;">Register here</a>
        </p>
        <p class="text-center" style="margin-top: 0.5rem;">
            <a href="admin-login.php" style="color: #F59E0B; font-weight: 600;">Admin Login</a>
        </p>
    </div>
</body>
</html>

