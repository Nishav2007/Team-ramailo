# 🌊 Melamchi Water Alert System

A full-stack web application that sends automatic email notifications when Melamchi water arrives in specific areas of Kathmandu, Nepal. Built for community service to help people never miss their water supply.

![Project Status](https://img.shields.io/badge/status-active-success.svg)
![Tech Stack](https://img.shields.io/badge/stack-HTML%20%7C%20CSS%20%7C%20JS%20%7C%20PHP%20%7C%20MySQL-blue.svg)

---

## 📋 Table of Contents

- [Problem Statement](#-problem-statement)
- [Solution](#-solution)
- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Project Structure](#-project-structure)
- [Screenshots](#-screenshots)
- [Future Enhancements](#-future-enhancements)
- [Contributing](#-contributing)
- [License](#-license)

---

## 🎯 Problem Statement

The Melamchi water supply in Kathmandu Valley arrives **unpredictably** with no fixed schedule. This causes:

- 😰 People constantly checking taps, wasting time
- 💧 Families missing the water supply completely
- ❓ No transparency about water arrival patterns
- 👵 Difficulty for elderly and busy workers to track water

**There is no existing notification system to alert people when water arrives.**

---

## 💡 Solution

A smart web platform that:

1. **Registers users** with their location (searchable dropdown of Nepal locations)
2. **Tracks water arrivals** through an admin panel
3. **Sends instant email alerts** to all users in affected areas automatically
4. **Provides historical data** showing water supply patterns and trends

---

## ✨ Features

### For Users

- 📧 **Email Notifications** - Instant alerts when water arrives in your area
- 🗺️ **Location-Based** - Only receive alerts for your specific location
- 📊 **Historical Dashboard** - View complete water supply history
- 📈 **Analytics** - See patterns like frequency, common times, trends
- 🔍 **Searchable Location Dropdown** - Easy selection from all Nepal locations
- 📱 **Responsive Design** - Works on desktop, tablet, and mobile

### For Admins

- 🚰 **Mark Water Arrivals** - Simple interface to record water events
- 📧 **Automatic Email Sending** - System sends alerts to all relevant users
- 📊 **System Statistics** - Total users, events, emails sent
- 👥 **User Management** - View users by location
- 📈 **Analytics Dashboard** - Recent events and trends

---

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 7.4+
- **Database**: MySQL (via XAMPP)
- **Email**: PHP Mail / PHPMailer with Gmail SMTP
- **Server**: Apache (XAMPP)

---

## 📥 Installation

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP)
- Web browser (Chrome, Firefox, Edge, etc.)
- Text editor (VS Code, Sublime Text, etc.)

### Step 1: Install XAMPP

1. Download and install XAMPP from [apachefriends.org](https://www.apachefriends.org/)
2. Start **Apache** and **MySQL** from XAMPP Control Panel

### Step 2: Setup Project Files

1. Copy the entire `GoodDream` folder to `C:\xampp\htdocs\`
   - Full path should be: `C:\xampp\htdocs\GoodDream\`

### Step 3: Create Database

1. Open phpMyAdmin: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **"Import"** tab
3. Choose file: `GoodDream/database/schema.sql`
4. Click **"Go"** to execute

**OR** run the SQL manually:

1. Click **"SQL"** tab in phpMyAdmin
2. Copy entire contents of `database/schema.sql`
3. Paste and click **"Go"**

### Step 4: Verify Database

Check that the database was created:

```sql
USE melamchi_alert;
SHOW TABLES;
```

You should see 4 tables:
- `admins`
- `locations`
- `users`
- `water_events`

### Step 5: Access the Application

Open your browser and go to:

**User Website**: [http://localhost/GoodDream/index.html](http://localhost/GoodDream/index.html)

**Admin Panel**: [http://localhost/GoodDream/admin-login.html](http://localhost/GoodDream/admin-login.html)

---

## ⚙️ Configuration

### Database Configuration

Edit `php/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');              // Default XAMPP password is empty
define('DB_NAME', 'melamchi_alert');
```

### Email Configuration (Optional)

For testing, the system uses PHP's `mail()` function. For production with Gmail:

1. Edit `php/config.php`:

```php
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');  // Gmail App Password
```

2. Enable **2-Step Verification** on Gmail
3. Generate an **App Password**: [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
4. Use the generated password in config

### Default Admin Credentials

```
Username: admin
Password: admin123
```

⚠️ **Important**: Change these credentials in production!

To create a new admin:

```sql
INSERT INTO admins (username, password) VALUES 
('newadmin', '$2y$10$...');  -- Use password_hash() in PHP
```

Generate password hash in PHP:
```php
echo password_hash('your_password', PASSWORD_DEFAULT);
```

---

## 🚀 Usage

### User Flow

#### 1. Registration

1. Go to [http://localhost/GoodDream/register.html](http://localhost/GoodDream/register.html)
2. Fill in:
   - Name
   - Email address (for alerts)
   - Password
   - Location (search and select from dropdown)
3. Click **Register**

#### 2. Login

1. Go to [http://localhost/GoodDream/login.html](http://localhost/GoodDream/login.html)
2. Enter email and password
3. Click **Login**

#### 3. View Dashboard

- See current water status for your area
- View recent water arrivals
- Check statistics (frequency, common times, etc.)

#### 4. View History

- Complete list of past water events
- Filter by time period (7/30/90 days, all time)
- Export data as CSV

### Admin Flow

#### 1. Admin Login

1. Go to [http://localhost/GoodDream/admin-login.html](http://localhost/GoodDream/admin-login.html)
2. Enter:
   - Username: `admin`
   - Password: `admin123`
3. Click **Login**

#### 2. Mark Water Arrival

1. In admin panel, use the searchable dropdown to select a location
2. Click **"Mark Water as Arrived & Send Alerts"**
3. Confirm the action
4. System will:
   - Record the event in database
   - Send email notifications to all users in that location
   - Display success message with number of emails sent

#### 3. View Statistics

- Total registered users
- Number of locations covered
- Events recorded today
- Emails sent today

---

## 📁 Project Structure

```
GoodDream/
│
├── index.html              # Homepage
├── register.html           # User registration
├── login.html              # User login
├── dashboard.html          # User dashboard
├── history.html            # Water supply history
├── admin-login.html        # Admin login
├── admin-panel.html        # Admin control panel
│
├── css/
│   ├── style.css           # Main stylesheet (teal theme)
│   ├── forms.css           # Form styling
│   ├── dashboard.css       # Dashboard styles
│   └── admin.css           # Admin panel styles
│
├── js/
│   ├── main.js             # General utilities
│   ├── searchable-dropdown.js  # Location dropdown
│   ├── validation.js       # Form validation
│   ├── register.js         # Registration logic
│   ├── login.js            # Login logic
│   ├── dashboard.js        # Dashboard logic
│   ├── history.js          # History page logic
│   ├── admin-login.js      # Admin login logic
│   └── admin-panel.js      # Admin panel logic
│
├── php/
│   ├── config.php          # Database configuration
│   ├── register.php        # User registration handler
│   ├── login.php           # User login handler
│   ├── admin_login.php     # Admin login handler
│   ├── logout.php          # Logout handler
│   ├── session_check.php   # Session verification
│   ├── get_locations.php   # Fetch all locations
│   ├── get_dashboard_data.php  # Dashboard data
│   ├── get_history.php     # Water history data
│   ├── get_admin_stats.php # Admin statistics
│   ├── mark_water_arrival.php  # Record water event
│   └── send_email_alerts.php   # Email sending
│
├── database/
│   └── schema.sql          # Database structure + sample data
│
└── README.md               # This file
```

---

## 🎨 Color Palette

The project uses a professional water-themed teal color palette:

- **#8FC4D4** - Light blue (backgrounds)
- **#6BA8B8** - Light blue (cards)
- **#5A7F8F** - Medium blue (borders)
- **#3A7F8A** - Light teal (buttons)
- **#256A73** - **PRIMARY BRAND** (main color)
- **#185860** - Dark teal (headers)
- **#0F3F45** - Very dark teal (navigation)
- **#0A2A2E** - Darkest teal (text)

Plus semantic colors:
- **#10B981** - Success (green)
- **#F59E0B** - Warning (orange)
- **#EF4444** - Error (red)

---

## 📸 Screenshots

### Homepage
Clean landing page explaining the service with call-to-action buttons.

### Registration with Searchable Dropdown
Users can search and select their exact location from a pre-populated list.

### User Dashboard
Shows current water status, recent arrivals, and statistics.

### Admin Panel
Simple interface to mark water arrivals and send notifications.

---

## 🚀 Future Enhancements

### Phase 2
- 📱 SMS alerts (in addition to email)
- 🤖 IoT sensor integration (automatic detection)
- 📊 Advanced analytics (graphs, predictions)
- 🔔 Browser push notifications
- 🗺️ Interactive map showing water availability

### Phase 3
- 📱 Mobile app (iOS/Android)
- 🌐 Multi-language support (Nepali, English)
- 🔗 Government API integration
- 🚰 Expand to other utilities (electricity, gas)
- 🏙️ Expand to other cities (Pokhara, Biratnagar, etc.)

---

## 🐛 Troubleshooting

### Database Connection Failed

**Problem**: "Database connection failed"

**Solution**:
1. Check XAMPP MySQL is running
2. Verify database name in `php/config.php` matches
3. Run `database/schema.sql` again

### Email Not Sending

**Problem**: Emails not being sent

**Solution**:
1. For testing: Check if PHP mail is configured in XAMPP
2. For production: Use PHPMailer with Gmail (see `send_email_alerts.php`)
3. Verify Gmail App Password is correct

### Location Dropdown Not Loading

**Problem**: Dropdown is empty

**Solution**:
1. Check browser console for errors
2. Verify `php/get_locations.php` is accessible
3. Check database has locations data

### Session Not Persisting

**Problem**: Logged out immediately

**Solution**:
1. Check PHP sessions are enabled
2. Clear browser cookies
3. Verify `session_start()` in `config.php`

---

## 🤝 Contributing

Contributions are welcome! To contribute:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is open source and available for community use.

**Built with ❤️ for the people of Kathmandu**

---

## 👥 Team

Developed for the community to solve real water supply problems.

---

## 📞 Support

For issues or questions:
- Email: info@melamchialert.com
- Create an issue on the repository

---

## 🙏 Acknowledgments

- Thanks to all communities facing water supply challenges
- XAMPP for providing easy local development
- PHPMailer for reliable email sending
- The open-source community

---

**Made with 💧 to help communities manage their water supply**

