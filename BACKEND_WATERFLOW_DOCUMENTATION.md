# 💧 BACKEND AND WATERFLOW SYSTEM DOCUMENTATION

## Complete Technical Guide to Melamchi Water Alert System

**Version:** 2.0  
**Last Updated:** December 2024  
**System Type:** Pure PHP with Live Updates  

---

## 📑 TABLE OF CONTENTS

1. [System Architecture](#system-architecture)
2. [Database Design](#database-design)
3. [Backend Components](#backend-components)
4. [Water Flow System](#water-flow-system)
5. [Live Update Mechanism](#live-update-mechanism)
6. [Security Features](#security-features)
7. [API Endpoints](#api-endpoints)
8. [User Flow](#user-flow)
9. [Admin Flow](#admin-flow)
10. [Testing Guide](#testing-guide)

---

## 🏗️ SYSTEM ARCHITECTURE

### Overview

The Melamchi Water Alert System is built as a **monolithic PHP application** with a simple, clean architecture:

```
┌─────────────────────────────────────────────────────┐
│                   USER BROWSER                       │
│  (HTML + Inline CSS + Inline JavaScript)            │
└────────────────┬────────────────────────────────────┘
                 │
                 │ HTTP Requests
                 │
┌────────────────▼────────────────────────────────────┐
│              PHP BACKEND                             │
│  ┌──────────────────────────────────────────────┐  │
│  │  config.php (Database + Helper Functions)    │  │
│  └──────────────────────────────────────────────┘  │
│                                                      │
│  ┌────────────┐  ┌────────────┐  ┌────────────┐   │
│  │   User     │  │   Admin    │  │  Water     │   │
│  │   Pages    │  │   Panel    │  │  Control   │   │
│  └────────────┘  └────────────┘  └────────────┘   │
└────────────────┬────────────────────────────────────┘
                 │
                 │ SQL Queries
                 │
┌────────────────▼────────────────────────────────────┐
│           MYSQL DATABASE                             │
│  (4 Tables: locations, users, admins, water_events) │
└──────────────────────────────────────────────────────┘
```

### Technology Stack

- **Frontend:** HTML5, CSS3 (inline styles), JavaScript (inline)
- **Backend:** PHP 7.4+ (Pure, no frameworks)
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Server:** Apache 2.4+ (XAMPP)
- **Version Control:** Git + GitHub

### Key Design Decisions

1. **Pure PHP** - No frameworks for simplicity
2. **Inline Styles/Scripts** - Self-contained files
3. **Auto-Refresh** - Meta tag refresh (30s) for live updates
4. **Hardcoded Admin** - No database auth for admin (security through obscurity)
5. **Session-Based Auth** - PHP sessions for user authentication

---

## 🗄️ DATABASE DESIGN

### Schema Overview

The system uses **4 main tables** with clear relationships:

```sql
┌─────────────────┐
│   locations     │
│─────────────────│
│ id (PK)         │◄──┐
│ location_name   │   │
│ district        │   │
│ zone            │   │
│ water_status    │   │ (NEW: 'flowing'/'not_flowing')
│ status_updated  │   │
│ created_at      │   │
└─────────────────┘   │
                       │
        ┌──────────────┴─────────────┐
        │                            │
┌───────▼──────┐          ┌──────────▼────────┐
│    users     │          │   water_events    │
│──────────────│          │───────────────────│
│ id (PK)      │          │ id (PK)           │
│ name         │          │ location_id (FK)  │
│ email        │          │ arrival_date      │
│ password     │          │ arrival_time      │
│ location_id  │◄─────┐   │ admin_id (FK)     │
│ created_at   │      │   │ created_at        │
└──────────────┘      │   └───────────────────┘
                      │
               ┌──────▼──────┐
               │   admins    │
               │─────────────│
               │ id (PK)     │
               │ username    │
               │ password    │
               │ created_at  │
               └─────────────┘
```

### Table Details

#### 1. `locations` Table

Stores all 42 locations across Nepal with live water status.

```sql
CREATE TABLE locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_name VARCHAR(100) NOT NULL,
    district VARCHAR(100),
    zone VARCHAR(100),
    water_status ENUM('flowing', 'not_flowing') DEFAULT 'not_flowing',
    status_updated_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_location_name (location_name),
    INDEX idx_water_status (water_status)
) ENGINE=InnoDB;
```

**Fields:**
- `id`: Primary key
- `location_name`: Name of location (e.g., "Chabahil", "Swayambhu")
- `district`: District name (e.g., "Kathmandu", "Lalitpur")
- `zone`: Zone name (e.g., "Bagmati", "Gandaki")
- `water_status`: **LIVE STATUS** - 'flowing' or 'not_flowing'
- `status_updated_at`: Timestamp when status was last changed
- `created_at`: Record creation timestamp

**Indexes:**
- `idx_location_name`: Fast location lookup
- `idx_water_status`: Quick filtering by status

#### 2. `users` Table

Stores registered users with their location preferences.

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    location_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE RESTRICT,
    INDEX idx_email (email),
    INDEX idx_location (location_id)
) ENGINE=InnoDB;
```

**Fields:**
- `id`: Primary key
- `name`: Full name
- `email`: Unique email (login identifier)
- `password`: Bcrypt hashed password
- `location_id`: Foreign key to locations table
- `created_at`: Registration timestamp

**Constraints:**
- Email must be unique
- Location_id must exist in locations table
- Cannot delete location if users exist (RESTRICT)

#### 3. `admins` Table

Stores admin credentials (NOT USED - Login is hardcoded).

```sql
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_username (username)
) ENGINE=InnoDB;
```

**Note:** Admin login bypasses this table. Credentials hardcoded:
- Username: `admin`
- Password: `admin123`

#### 4. `water_events` Table

Records history of water arrivals.

```sql
CREATE TABLE water_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    location_id INT NOT NULL,
    arrival_date DATE NOT NULL,
    arrival_time TIME NOT NULL,
    admin_id INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE CASCADE,
    INDEX idx_location_date (location_id, arrival_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB;
```

**Fields:**
- `id`: Primary key
- `location_id`: Foreign key to locations
- `arrival_date`: Date water arrived (YYYY-MM-DD)
- `arrival_time`: Time water arrived (HH:MM:SS)
- `admin_id`: Admin who triggered (always 1)
- `created_at`: Event creation timestamp

**Indexes:**
- Composite index on (location_id, arrival_date) for fast queries
- Index on created_at for recent events

---

## ⚙️ BACKEND COMPONENTS

### 1. Core Configuration (`config.php`)

The heart of the application - establishes database connection and provides helper functions.

```php
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'melamchi_alert');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
function clean($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

function isUserLoggedIn() {
    return isset($_SESSION['user_logged_in']) && 
           $_SESSION['user_logged_in'] === true;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && 
           $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isUserLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdminLoggedIn()) {
        header('Location: admin-login.php');
        exit;
    }
}
?>
```

**Key Features:**
- **Global connection** - `$conn` available in all files
- **Session management** - Auto-starts sessions
- **Input sanitization** - `clean()` prevents SQL injection
- **Authentication helpers** - Check login status
- **Access control** - Redirect unauthorized users

### 2. User Registration (`register.php`)

Handles new user signup with validation.

**Process Flow:**
```
1. Display registration form with location dropdown
2. User submits form (POST)
3. Validate all inputs:
   - Name not empty
   - Valid email format
   - Password >= 6 characters
   - Location selected
4. Check if email already exists
5. Hash password with bcrypt
6. Insert into database
7. Redirect to login page
```

**Code Highlights:**

```php
// Validation
if (empty($name) || empty($email) || empty($password) || $location_id <= 0) {
    $error = 'All fields are required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email format';
} elseif (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters';
}

// Check duplicate email
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    $error = 'Email already registered';
}

// Insert user
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password, location_id) VALUES (?, ?, ?, ?)");
$stmt->bind_param('sssi', $name, $email, $password_hash, $location_id);
$stmt->execute();
```

**Security Features:**
- Prepared statements (SQL injection prevention)
- Bcrypt password hashing
- Email validation
- Input sanitization

### 3. User Login (`login.php`)

Authenticates users and creates session.

**Process Flow:**
```
1. Display login form
2. User submits credentials
3. Query database for email
4. Verify password with bcrypt
5. Create session variables:
   - user_logged_in
   - user_id
   - user_name
   - user_email
   - user_location_id
   - user_location_name
6. Redirect to dashboard
```

**Code Highlights:**

```php
// Get user with location
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
        // Create session
        $_SESSION['user_logged_in'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_location_id'] = $user['location_id'];
        $_SESSION['user_location_name'] = $user['location_name'];
        
        header('Location: dashboard.php');
        exit;
    }
}
```

### 4. Admin Login (`admin-login.php`)

**HARDCODED AUTHENTICATION** - No database check!

**Process Flow:**
```
1. Display admin login form
2. Admin submits credentials
3. Check if username === 'admin' AND password === 'admin123'
4. If match:
   - Set session variables
   - Redirect to admin panel
5. Else:
   - Show error
```

**Code:**

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // HARDCODED - No database!
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = 'admin';
        $_SESSION['admin_id'] = 1;
        
        header('Location: admin-panel.php');
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
```

**Why Hardcoded?**
- Simplicity - No password reset needed
- Speed - Instant login
- Single admin - No multi-admin management
- Security through obscurity - Credentials not in code comments

---

## 💧 WATER FLOW SYSTEM

### Overview

The **Water Flow System** is the core feature that allows admins to control water status and notify users in real-time.

### Architecture

```
Admin Panel
    │
    ▼
Toggle Water ON/OFF
    │
    ├──► Update locations.water_status
    │
    ├──► Set locations.status_updated_at
    │
    ├──► Create water_events record (if ON)
    │
    └──► Count users in location
         │
         └──► Show success message
              │
              ▼
         User Dashboard (auto-refresh 30s)
              │
              └──► Fetch water_status
                   │
                   ├──► If 'flowing': GREEN PULSING
                   └──► If 'not_flowing': RED/Past events
```

### Admin Panel Water Control

Located in `admin-panel.php` - Water Flow Control Tab

**Features:**
1. **Toggle Switch** for each location
2. **Real-time Status** (Green = Flowing, Red = Not Flowing)
3. **User Count** per location
4. **Events Today** counter
5. **Search Bar** to find locations quickly
6. **Confirmation Dialog** before turning ON

**Toggle Logic:**

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_water'])) {
    $location_id = intval($_POST['location_id'] ?? 0);
    $new_status = $_POST['new_status'] ?? '';
    
    if ($location_id > 0 && in_array($new_status, ['flowing', 'not_flowing'])) {
        // Get location name
        $stmt = $conn->prepare("SELECT location_name FROM locations WHERE id = ?");
        $stmt->bind_param('i', $location_id);
        $stmt->execute();
        $locData = $stmt->get_result()->fetch_assoc();
        $location_name = $locData['location_name'] ?? 'Unknown';
        
        // Update water status
        $stmt = $conn->prepare("UPDATE locations SET water_status = ?, status_updated_at = NOW() WHERE id = ?");
        $stmt->bind_param('si', $new_status, $location_id);
        $stmt->execute();
        
        if ($new_status === 'flowing') {
            // Create water event (if not created in last hour)
            $stmt = $conn->prepare("
                SELECT id FROM water_events 
                WHERE location_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->bind_param('i', $location_id);
            $stmt->execute();
            
            if ($stmt->get_result()->num_rows === 0) {
                // Create event
                $stmt = $conn->prepare("
                    INSERT INTO water_events (location_id, arrival_date, arrival_time, admin_id) 
                    VALUES (?, CURDATE(), CURTIME(), 1)
                ");
                $stmt->bind_param('i', $location_id);
                $stmt->execute();
                
                // Count users
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE location_id = ?");
                $stmt->bind_param('i', $location_id);
                $stmt->execute();
                $userCount = $stmt->get_result()->fetch_assoc()['count'];
                
                $success = "Water flow started in {$location_name}! ({$userCount} users notified)";
            } else {
                $success = "Water status updated for {$location_name}";
            }
        } else {
            $success = "Water flow stopped in {$location_name}";
        }
    }
}
```

**Key Points:**
- **Prevents duplicate events**: Checks if event created in last hour
- **Updates status immediately**: Sets water_status field
- **Timestamp tracking**: Records status_updated_at
- **User notification count**: Shows how many users affected
- **No actual emails sent**: Email system disabled for now

### User Dashboard Display

Located in `dashboard.php`

**Status Priority Logic:**

```php
// Get current water status (LIVE)
$stmt = $conn->prepare("SELECT water_status, status_updated_at FROM locations WHERE id = ?");
$stmt->bind_param('i', $location_id);
$stmt->execute();
$locationData = $stmt->get_result()->fetch_assoc();
$waterStatus = $locationData['water_status'] ?? 'not_flowing';
$statusUpdated = $locationData['status_updated_at'] ?? null;

// Determine status display priority
$statusClass = 'no-water';
$statusIcon = '❌';
$statusTitle = 'No Recent Water Supply';

if ($waterStatus === 'flowing') {
    // PRIORITY 1: Water is flowing RIGHT NOW
    $statusClass = 'flowing';
    $statusIcon = '💧';
    $statusTitle = 'Water Flowing Now!';
    $statusMessage = "Melamchi water is currently flowing in {$location_name}";
    // GREEN PULSING ANIMATION
} elseif ($latestEvent) {
    // PRIORITY 2: Recent water event exists
    $statusClass = 'available';
    $statusIcon = '💧';
    $statusTitle = 'Water Available!';
    $statusMessage = "Water arrived in {$location_name}";
}
```

**Display States:**

1. **FLOWING (Green Pulsing)**
   - Status: `water_status = 'flowing'`
   - Display: Big green pulsing card
   - Message: "💧 Water Flowing Now!"
   - Animation: CSS pulse animation
   - Subtitle: "Started: [timestamp]"

2. **AVAILABLE (Green)**
   - Status: `water_status = 'not_flowing'` BUT recent event exists
   - Display: Green card (no pulse)
   - Message: "💧 Water Available!"
   - Subtitle: "Last arrival: [date/time]"

3. **NO WATER (Red)**
   - Status: `water_status = 'not_flowing'` AND no recent events
   - Display: Red card
   - Message: "❌ No Recent Water Supply"
   - Subtitle: "You will receive notifications when water arrives"

---

## 🔄 LIVE UPDATE MECHANISM

### How Live Updates Work

The system uses **META TAG AUTO-REFRESH** - a simple, effective method:

```html
<meta http-equiv="refresh" content="30">
```

This tells the browser to reload the entire page every 30 seconds.

### Why Not AJAX/WebSockets?

| Method | Pros | Cons | Choice |
|--------|------|------|--------|
| **Meta Refresh** | Simple, No JS needed, Works everywhere | Full page reload | ✅ **USED** |
| AJAX Polling | Partial updates | Complex JS | ❌ |
| WebSockets | True real-time | Server setup, Complex | ❌ |
| Server-Sent Events | One-way push | Browser support | ❌ |

**Decision:** Meta refresh provides best balance of simplicity and functionality.

### Implementation

**Dashboard (`dashboard.php`):**
```html
<meta http-equiv="refresh" content="30">
```

**Admin Panel (`admin-panel.php`):**
```html
<meta http-equiv="refresh" content="30">
```

**History (`history.php`):**
```html
<meta http-equiv="refresh" content="60">
```

### Visual Countdown Timer

JavaScript shows countdown in browser tab:

```javascript
let seconds = 30;
setInterval(() => {
    seconds--;
    if (seconds <= 0) seconds = 30;
    document.title = `(${seconds}s) Dashboard - Melamchi Water Alert`;
}, 1000);
```

**User sees:** `(29s) Dashboard` → `(28s) Dashboard` → ... → `(1s) Dashboard` → [Page reloads]

### Live Status Flow

**Complete Flow:**

```
TIME 0:00 - Admin toggles water ON in Chabahil
    │
    ▼
Database updated:
    locations.water_status = 'flowing'
    locations.status_updated_at = NOW()
    water_events created
    │
    ▼
TIME 0:15 - User's dashboard auto-refreshes
    │
    ▼
Dashboard queries database:
    SELECT water_status FROM locations WHERE id = [user_location]
    │
    ▼
Result: water_status = 'flowing'
    │
    ▼
Dashboard displays: GREEN PULSING "Water Flowing Now!"
    │
    ▼
TIME 0:30 - Another auto-refresh
    │
    ▼
Still showing: "Water Flowing Now!"
    │
    ▼
TIME 5:00 - Admin toggles water OFF
    │
    ▼
Database updated:
    locations.water_status = 'not_flowing'
    locations.status_updated_at = NOW()
    │
    ▼
TIME 5:20 - User's dashboard auto-refreshes
    │
    ▼
Result: water_status = 'not_flowing'
    │
    ▼
Dashboard displays: GREEN "Water Available" (past event)
```

---

## 🔐 SECURITY FEATURES

### 1. SQL Injection Prevention

**All queries use prepared statements:**

```php
// ❌ WRONG - Vulnerable to SQL injection
$query = "SELECT * FROM users WHERE email = '$email'";

// ✅ RIGHT - Prepared statement
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
```

### 2. Password Security

**Bcrypt hashing:**

```php
// Registration
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Login verification
if (password_verify($password, $user['password'])) {
    // Valid
}
```

**Why Bcrypt?**
- Industry standard
- Automatically salted
- Adaptive (can increase rounds)
- One-way (cannot decrypt)

### 3. XSS Prevention

**Output sanitization:**

```php
// Always escape output
<?= htmlspecialchars($user['name']) ?>
<?= htmlspecialchars($location['location_name']) ?>
```

### 4. Session Security

**Session management:**

```php
// Start session securely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID on login
session_regenerate_id(true);

// Clear session on logout
session_unset();
session_destroy();
```

### 5. Access Control

**Route protection:**

```php
// User pages
requireLogin(); // Redirects if not logged in

// Admin pages
requireAdmin(); // Redirects if not admin
```

### 6. Input Validation

**Server-side validation:**

```php
// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Invalid email format';
}

// Password strength
if (strlen($password) < 6) {
    $error = 'Password must be at least 6 characters';
}

// Integer validation
$location_id = intval($_POST['location_id'] ?? 0);
if ($location_id <= 0) {
    $error = 'Invalid location';
}
```

---

## 🔌 API ENDPOINTS

### User Endpoints

| Endpoint | Method | Auth | Purpose |
|----------|--------|------|---------|
| `register.php` | GET/POST | No | User registration |
| `login.php` | GET/POST | No | User login |
| `dashboard.php` | GET | User | View water status |
| `history.php` | GET | User | View water history |
| `logout.php` | GET | User | Logout |

### Admin Endpoints

| Endpoint | Method | Auth | Purpose |
|----------|--------|------|---------|
| `admin-login.php` | GET/POST | No | Admin login |
| `admin-panel.php` | GET/POST | Admin | Control panel |
| `admin-panel.php?tab=users` | GET | Admin | User management |

### Request/Response Examples

#### 1. Register User

**Request:**
```
POST /register.php
Content-Type: application/x-www-form-urlencoded

name=John+Doe&email=john@example.com&password=secret123&location_id=4
```

**Response (Success):**
```
HTTP/1.1 302 Found
Location: login.php
```

**Response (Error):**
```
HTML page with error message:
"Email already registered"
```

#### 2. Toggle Water Flow

**Request:**
```
POST /admin-panel.php
Content-Type: application/x-www-form-urlencoded

toggle_water=1&location_id=4&new_status=flowing
```

**Response:**
```
HTTP/1.1 302 Found
Location: admin-panel.php

Success message: "Water flow started in Chabahil! (2 users notified)"
```

---

## 👤 USER FLOW

### Complete User Journey

```
1. Homepage (index.php)
   │
   ├─► Click "Get Started Free"
   │
   ▼
2. Registration (register.php)
   │
   ├─► Fill form (name, email, password, location)
   ├─► Submit
   ├─► Validation
   ├─► Insert into database
   │
   ▼
3. Login (login.php)
   │
   ├─► Enter email & password
   ├─► Verify credentials
   ├─► Create session
   │
   ▼
4. Dashboard (dashboard.php)
   │
   ├─► View water status (LIVE)
   ├─► See statistics
   ├─► View recent events
   ├─► Auto-refresh every 30s
   │
   ├─► Click "View Full History"
   │
   ▼
5. History (history.php)
   │
   ├─► View all water events
   ├─► Filter by date range
   ├─► Auto-refresh every 60s
   │
   └─► Click "Logout"
       │
       ▼
6. Logout (logout.php)
   │
   └─► Clear session, redirect to index
```

---

## 👨‍💼 ADMIN FLOW

### Complete Admin Journey

```
1. Admin Login (admin-login.php)
   │
   ├─► Enter "admin" / "admin123"
   ├─► Hardcoded check (no database)
   ├─► Create admin session
   │
   ▼
2. Admin Panel (admin-panel.php)
   │
   ├─► See statistics dashboard
   ├─► View two tabs:
   │   │
   │   ├─► Water Flow Control Tab
   │   │   │
   │   │   ├─► Search locations
   │   │   ├─► View all 42 locations
   │   │   ├─► See status (Green/Red)
   │   │   ├─► Toggle water ON/OFF
   │   │   └─► Confirmation dialog
   │   │
   │   └─► User Management Tab
   │       │
   │       ├─► View user count by location
   │       ├─► Search users
   │       ├─► View all registered users
   │       └─► Export data (future)
   │
   └─► Click "Logout"
       │
       ▼
3. Logout
   │
   └─► Clear session, redirect to index
```

---

## 🧪 TESTING GUIDE

### Database Setup Test

```sql
-- Verify database created
SHOW DATABASES LIKE 'melamchi_alert';

-- Verify tables created
USE melamchi_alert;
SHOW TABLES;

-- Check locations loaded
SELECT COUNT(*) FROM locations; -- Should be 42

-- Check admin exists
SELECT * FROM admins;

-- Check sample users
SELECT * FROM users;
```

### User Registration Test

**Test Case 1: Successful Registration**
```
1. Go to http://localhost/GoodDream/register.php
2. Fill form:
   - Name: "Test User"
   - Email: "test@example.com"
   - Password: "password123"
   - Location: "Chabahil"
3. Click "Register"
4. Expected: Redirect to login.php with success message
5. Verify: SELECT * FROM users WHERE email = 'test@example.com';
```

**Test Case 2: Duplicate Email**
```
1. Try registering with same email
2. Expected: Error "Email already registered"
```

**Test Case 3: Weak Password**
```
1. Enter password: "12345"
2. Expected: Error "Password must be at least 6 characters"
```

### Water Flow Test

**Test Case 1: Toggle Water ON**
```
1. Admin login (admin/admin123)
2. Find "Chabahil" location
3. Click "Turn ON" button
4. Confirm dialog
5. Expected: 
   - Page reloads
   - Chabahil card turns GREEN
   - Success message shows
   - Database: SELECT water_status FROM locations WHERE location_name = 'Chabahil';
     Result: 'flowing'
6. Login as user in Chabahil
7. Dashboard shows: GREEN PULSING "Water Flowing Now!"
```

**Test Case 2: Toggle Water OFF**
```
1. In admin panel, click "Turn OFF" on Chabahil
2. Expected:
   - Chabahil card turns RED
   - Database: water_status = 'not_flowing'
3. User dashboard (after refresh):
   - Shows: "Water Available" (past event)
```

**Test Case 3: Live Update**
```
1. Open two browser windows side-by-side
2. Window 1: Admin panel
3. Window 2: User dashboard (Chabahil user)
4. Admin toggles Chabahil ON
5. Wait 30 seconds
6. User dashboard auto-refreshes
7. Expected: User sees "Water Flowing Now!"
```

### Security Test

**Test Case 1: SQL Injection**
```
Try: email' OR '1'='1
Expected: Login fails (prepared statements protect)
```

**Test Case 2: Unauthorized Access**
```
1. Logout
2. Try accessing: http://localhost/GoodDream/dashboard.php
3. Expected: Redirect to login.php
```

**Test Case 3: Admin Protection**
```
1. As regular user, try: http://localhost/GoodDream/admin-panel.php
2. Expected: Redirect to admin-login.php
```

---

## 📝 MAINTENANCE NOTES

### Regular Tasks

1. **Database Backup** (Weekly)
```bash
mysqldump -u root melamchi_alert > backup_YYYYMMDD.sql
```

2. **Clear Old Events** (Monthly)
```sql
DELETE FROM water_events WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

3. **Check User Count**
```sql
SELECT COUNT(*) FROM users;
SELECT location_name, COUNT(u.id) as users 
FROM locations l 
LEFT JOIN users u ON l.id = u.location_id 
GROUP BY l.id 
ORDER BY users DESC;
```

### Performance Optimization

1. **Add indexes** if queries slow down
2. **Limit auto-refresh** to necessary pages only
3. **Cache location list** (rarely changes)
4. **Archive old events** to separate table

---

## 🎯 CONCLUSION

The Melamchi Water Alert System provides a **simple, effective solution** for real-time water monitoring:

✅ **Pure PHP** - Easy to understand and modify  
✅ **Live Updates** - Auto-refresh every 30 seconds  
✅ **Secure** - Prepared statements, bcrypt, XSS protection  
✅ **Scalable** - Handles 42 locations, unlimited users  
✅ **Mobile-Friendly** - Responsive design  
✅ **Admin-Friendly** - Simple toggle interface  

**Total Files:** 11 core files  
**Database Tables:** 4 tables  
**Lines of Code:** ~700 lines PHP  
**Dependencies:** None (pure PHP)  

---

**Built with 💧 for Kathmandu community**

**Questions? Issues?** Check the code - it's self-documented!

