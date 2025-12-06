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
    <title>Register - <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="navbar-brand">💧 <?= SITE_NAME ?></a>
            <div class="navbar-links">
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
            </div>
        </div>
    </nav>

    <!-- Registration Form -->
    <div class="form-container">
        <h1 class="text-center">Create Your Account</h1>
        <p class="text-center" style="color: #666; margin-bottom: 2rem;">Get water alerts for your area</p>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
                <small style="color: #666;">Minimum 6 characters</small>
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

            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>

        <p class="text-center" style="margin-top: 1.5rem;">
            Already have an account? <a href="login.php" style="color: #256A73; font-weight: 600;">Login here</a>
        </p>
    </div>
</body>
</html>

