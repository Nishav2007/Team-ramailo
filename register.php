<?php
/**
 * USER REGISTRATION
 */
require_once 'config.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $location_id = intval($_POST['location_id'] ?? 0);
    
    // Validate
    if (empty($name) || empty($email) || empty($password) || $location_id <= 0) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            // Register user
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, location_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('sssi', $name, $email, $password_hash, $location_id);
            
            if ($stmt->execute()) {
                $success = 'Registration successful! Redirecting to login...';
                header('Refresh: 2; URL=login.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

// Get all locations for dropdown
$locations = [];
$result = $conn->query("SELECT id, location_name, district FROM locations ORDER BY location_name ASC");
while ($row = $result->fetch_assoc()) {
    $locations[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - GoodDream</title>
    <link rel="stylesheet" href="gooddream-theme.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">GoodDream</a>
            <div class="navbar-links">
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
            </div>
        </div>
    </nav>

    <!-- Registration Form -->
    <div class="form-container">
        <div style="text-align: center; margin-bottom: 2rem;">
            <div class="css-icon icon-drop"></div>
        </div>
        
        <h1 class="text-center" style="background: var(--gradient-1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">Create Your Account</h1>
        <p class="text-center" style="color: #666; margin-bottom: 2rem;">Join GoodDream and never miss water alerts</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" placeholder="Enter your full name" required autofocus>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="your@email.com" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimum 6 characters" required>
                <small style="color: #666; font-size: 0.85rem;">Use a strong password with letters and numbers</small>
            </div>

            <div class="form-group">
                <label>Your Location</label>
                <select name="location_id" required>
                    <option value="">Select your location</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= $loc['id'] ?>" <?= (isset($_POST['location_id']) && $_POST['location_id'] == $loc['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($loc['location_name']) ?> - <?= htmlspecialchars($loc['district']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </form>

        <p class="text-center" style="margin-top: 1.5rem; color: #666;">
            Already have an account? <a href="login.php" style="color: var(--teal-primary); font-weight: 600; text-decoration: none;">Login here</a>
        </p>
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
