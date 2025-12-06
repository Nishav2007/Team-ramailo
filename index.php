<?php
/**
 * HOMEPAGE - Melamchi Water Alert System
 */
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?></title>
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
                <a href="register.php">Register</a>
                <a href="admin-login.php">Admin</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="container">
        <div class="card text-center" style="margin-top: 3rem; padding: 4rem 2rem;">
            <h1 style="font-size: 3rem; margin-bottom: 1rem;">💧 Never Miss Melamchi Water Again</h1>
            <p style="font-size: 1.3rem; color: #666; margin-bottom: 2rem;">
                Get instant notifications when water arrives in your area
            </p>
            <div>
                <a href="register.php" class="btn btn-primary" style="margin-right: 1rem;">Get Started Free</a>
                <a href="login.php" class="btn btn-success">Login</a>
            </div>
        </div>

        <!-- Features -->
        <h2 class="text-center" style="margin: 3rem 0 2rem;">🌟 Features</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📧</div>
                <h3>Email Alerts</h3>
                <p>Instant notifications when water arrives</p>
            </div>
            <div class="stat-card">
                <div class="icon">🗺️</div>
                <h3>Location-Based</h3>
                <p>Only alerts for your specific area</p>
            </div>
            <div class="stat-card">
                <div class="icon">💧</div>
                <h3>Live Status</h3>
                <p>Real-time water flow monitoring</p>
            </div>
            <div class="stat-card">
                <div class="icon">📊</div>
                <h3>History</h3>
                <p>Complete water supply records</p>
            </div>
        </div>

        <!-- How It Works -->
        <div class="card" style="margin-top: 3rem;">
            <h2 class="text-center">How It Works</h2>
            <ol style="margin-left: 2rem; margin-top: 1rem;">
                <li style="margin-bottom: 1rem;"><strong>Register</strong> with your email and location</li>
                <li style="margin-bottom: 1rem;"><strong>Get Alerts</strong> when water arrives in your area</li>
                <li style="margin-bottom: 1rem;"><strong>Check Dashboard</strong> for live water flow status</li>
                <li style="margin-bottom: 1rem;"><strong>View History</strong> to see patterns and trends</li>
            </ol>
        </div>
    </div>

    <!-- Footer -->
    <footer style="background: #0F3F45; color: white; text-align: center; padding: 2rem; margin-top: 4rem;">
        <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Built for Kathmandu community.</p>
    </footer>
</body>
</html>

