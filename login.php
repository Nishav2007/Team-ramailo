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
    <title>Login - GoodDream</title>
    <link rel="stylesheet" href="gooddream-theme.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">GoodDream</a>
            <div class="navbar-links">
                <a href="index.php">Home</a>
                <a href="register.php">Register</a>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="form-container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="css-icon icon-user"></div>
        </div>
        
        <h1 class="text-center" style="background: var(--gradient-1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Welcome Back</h1>
        <p class="text-center" style="color: #666; margin-bottom: 2rem;">Login to view your water alerts dashboard</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
            <div class="alert alert-success">Registration successful! Please login below.</div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="your@email.com" required autofocus>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login to Dashboard</button>
        </form>

        <div style="margin-top: 1.5rem; text-align: center;">
            <p style="color: #666; margin-bottom: 0.5rem;">
                Don't have an account? <a href="register.php" style="color: var(--teal-primary); font-weight: 600; text-decoration: none;">Register here</a>
            </p>
            <p style="color: #666;">
                <a href="admin-login.php" style="color: #f59e0b; font-weight: 600; text-decoration: none;">Admin Login</a>
            </p>
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
