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
    <title>GoodDream - Melamchi Water Alert Solution</title>
    <link rel="stylesheet" href="gooddream-theme.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --teal-primary: #14b8a6;
            --teal-dark: #0d9488;
            --teal-light: #5eead4;
            --teal-accent: #2dd4bf;
            --gradient-1: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            --gradient-2: linear-gradient(135deg, #5eead4 0%, #14b8a6 100%);
            --gradient-3: linear-gradient(135deg, #0d9488 0%, #2dd4bf 100%);
            --gradient-4: linear-gradient(135deg, #14b8a6 0%, #5eead4 100%);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(20, 184, 166, 0.1);
            z-index: 1000;
            transition: box-shadow 0.3s;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }

        .nav-menu a::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--gradient-1);
            transition: width 0.3s;
        }

        .nav-menu a:hover::after {
            width: 100%;
        }

        .nav-menu a:hover {
            color: var(--teal-primary);
        }

        .hero-split {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 6rem 2rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
            gap: 4rem;
        }

        .split-left {
            z-index: 2;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: #666;
            margin-bottom: 2.5rem;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.875rem 2.25rem;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: var(--gradient-1);
            color: white;
            box-shadow: 0 4px 15px rgba(20, 184, 166, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(20, 184, 166, 0.4);
        }

        .btn-outline {
            background: transparent;
            color: var(--teal-primary);
            border: 2px solid var(--teal-primary);
        }

        .btn-outline:hover {
            background: var(--teal-primary);
            color: white;
            transform: translateY(-2px);
        }

        .hero-stats {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .mini-stat {
            text-align: center;
        }

        .mini-stat-number {
            font-size: 2rem;
            font-weight: bold;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.25rem;
        }

        .mini-stat-label {
            font-size: 0.875rem;
            color: #666;
            font-weight: 500;
        }

        .split-right {
            position: relative;
            height: 600px;
        }

        .hero-visual {
            width: 100%;
            height: 100%;
            position: relative;
        }

        .water-wave {
            position: absolute;
            border-radius: 50% 40% 60% 30%;
            animation: wave 8s ease-in-out infinite;
            opacity: 0.7;
        }

        .wave-1 {
            width: 400px;
            height: 400px;
            background: var(--gradient-2);
            top: 10%;
            left: 10%;
            animation-duration: 8s;
        }

        .wave-2 {
            width: 300px;
            height: 300px;
            background: var(--gradient-3);
            top: 30%;
            right: 10%;
            animation-duration: 10s;
            animation-delay: 1s;
        }

        .wave-3 {
            width: 250px;
            height: 250px;
            background: var(--gradient-4);
            bottom: 10%;
            left: 30%;
            animation-duration: 12s;
            animation-delay: 2s;
        }

        @keyframes wave {
            0%, 100% { 
                border-radius: 50% 40% 60% 30%; 
                transform: rotate(0deg) scale(1); 
            }
            25% { 
                border-radius: 30% 60% 40% 50%; 
                transform: rotate(90deg) scale(1.1); 
            }
            50% { 
                border-radius: 60% 30% 50% 40%; 
                transform: rotate(180deg) scale(0.9); 
            }
            75% { 
                border-radius: 40% 50% 30% 60%; 
                transform: rotate(270deg) scale(1.05); 
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        .section-title {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 1rem;
            background: var(--gradient-1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-text {
            text-align: center;
            font-size: 1.1rem;
            color: #666;
            max-width: 800px;
            margin: 0 auto 3rem;
            line-height: 1.8;
        }

        .solution-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .solution-card {
            padding: 2.5rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 2px solid transparent;
        }

        .solution-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(20, 184, 166, 0.2);
            border-color: var(--teal-primary);
        }

        .solution-card h3 {
            font-size: 1.5rem;
            margin: 1.5rem 0 1rem;
            color: var(--teal-dark);
        }

        .solution-card p {
            color: #666;
            line-height: 1.8;
        }

        .footer {
            background: var(--gradient-3);
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }

        @media (max-width: 1024px) {
            .hero-split {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .split-right {
                height: 400px;
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .nav-menu {
                gap: 1rem;
                font-size: 0.9rem;
            }
            
            .hero-stats {
                justify-content: center;
            }
            
            .solution-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">GoodDream</div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#solution">Solution</a></li>
                <li><a href="register.php">Register</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="admin-login.php">Admin</a></li>
            </ul>
        </div>
    </nav>

    <section class="hero-split" id="home">
        <div class="split-left">
            <div class="hero-content">
                <h1 class="hero-title">Never Miss Melamchi Water Again</h1>
                <p class="hero-subtitle">Real-time water flow monitoring and instant notifications for your area. Stay informed, stay prepared.</p>
                <div class="hero-buttons">
                    <a href="register.php" class="btn btn-primary">Get Started Free</a>
                    <a href="login.php" class="btn btn-outline">Login</a>
                </div>
                <div class="hero-stats">
                    <div class="mini-stat">
                        <div class="mini-stat-number">42</div>
                        <div class="mini-stat-label">Locations</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-number">Live</div>
                        <div class="mini-stat-label">Updates</div>
                    </div>
                    <div class="mini-stat">
                        <div class="mini-stat-number">24/7</div>
                        <div class="mini-stat-label">Monitoring</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="split-right">
            <div class="hero-visual">
                <div class="water-wave wave-1"></div>
                <div class="water-wave wave-2"></div>
                <div class="water-wave wave-3"></div>
            </div>
        </div>
    </section>

    <section class="solution" id="solution">
        <div class="container">
            <h2 class="section-title">Our Solution</h2>
            <p class="section-text">GoodDream provides a comprehensive water alert system for Melamchi, ensuring you never miss water supply in your area through innovative technology and real-time monitoring.</p>
            <div class="solution-grid">
                <div class="solution-card">
                    <div class="css-icon icon-email"></div>
                    <h3>Instant Alerts</h3>
                    <p>Receive immediate email notifications when water arrives in your specific location</p>
                </div>
                <div class="solution-card">
                    <div class="css-icon icon-drop"></div>
                    <h3>Live Status</h3>
                    <p>Real-time water flow monitoring with auto-updating dashboard every 30 seconds</p>
                </div>
                <div class="solution-card">
                    <div class="css-icon icon-chart"></div>
                    <h3>History Tracking</h3>
                    <p>Complete water supply records to analyze patterns and plan ahead</p>
                </div>
                <div class="solution-card">
                    <div class="css-icon icon-map"></div>
                    <h3>Location-Based</h3>
                    <p>Targeted alerts only for your registered area across 42 locations</p>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> GoodDream - Melamchi Water Alert System. All rights reserved.</p>
            <p style="margin-top: 0.5rem; opacity: 0.8;">Built with 💧 for Kathmandu community</p>
        </div>
    </footer>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', () => {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.2)';
            } else {
                navbar.style.boxShadow = '0 2px 20px rgba(20, 184, 166, 0.1)';
            }
        });

        // Parallax effect for hero visual
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const heroVisual = document.querySelector('.hero-visual');
            if (heroVisual && scrolled < window.innerHeight) {
                heroVisual.style.transform = `translateY(${scrolled * 0.3}px)`;
            }
        });
    </script>
</body>
</html>
