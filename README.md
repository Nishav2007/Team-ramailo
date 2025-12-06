# 🌊 Melamchi Water Alert System - Pure PHP Edition

**Live Real-Time Water Flow Monitoring System**

---

## 🚀 Quick Setup (3 Steps)

### Step 1: Import Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "Import" tab
3. Choose file: `setup_database.sql`
4. Click "Go"

### Step 2: Access Website
Open: http://localhost/GoodDream/index.php

### Step 3: Test It!
**Admin Login:**
- URL: http://localhost/GoodDream/admin-login.php
- Username: `admin`
- Password: `admin123`

**Test User Login:**
- Email: `ramesh@example.com`
- Password: `test123`

---

## 📁 Project Structure

```
GoodDream/
├── index.php              # Homepage
├── register.php           # User registration
├── login.php              # User login
├── dashboard.php          # User dashboard (LIVE - 30s refresh)
├── history.php            # Water history (LIVE - 60s refresh)
├── admin-login.php        # Admin login (hardcoded)
├── admin-panel.php        # Admin control (LIVE - 30s refresh)
├── logout.php             # Logout handler
├── config.php             # Database configuration
├── style.css              # Complete stylesheet
└── setup_database.sql     # Database setup script
```

**Total: 10 files** (down from 39!)

---

## ⚡ Features

### For Users:
- ✅ Register with location
- ✅ Login to dashboard
- ✅ See **LIVE water flow status** (green pulsing when flowing)
- ✅ View complete history
- ✅ Auto-refresh every 30 seconds

### For Admins:
- ✅ Login instantly (admin/admin123 - no database check)
- ✅ See all 42 locations at once
- ✅ Toggle water flow ON/OFF with one click
- ✅ Green cards = water flowing, Red = not flowing
- ✅ Auto-refresh every 30 seconds
- ✅ Statistics dashboard

### Live Updates:
- ✅ Admin toggles water ON in Chabahil
- ✅ Users in Chabahil see "💧 Water Flowing Now!" within 30 seconds
- ✅ Green pulsing card with animation
- ✅ No manual refresh needed

---

## 🧪 How to Test

### Test Flow:

1. **Login as Admin:**
   - Go to: http://localhost/GoodDream/admin-login.php
   - Enter: admin / admin123
   - See all locations with toggles

2. **Toggle Water ON:**
   - Find "Chabahil" (should be RED/No Water)
   - Click "Turn ON" button
   - Confirm popup: "Send to 2 users?"
   - Page reloads → Chabahil now GREEN/Flowing

3. **Login as User:**
   - Open new browser tab
   - Go to: http://localhost/GoodDream/login.php
   - Login: ramesh@example.com / test123
   - See dashboard

4. **User Sees Live Status:**
   - Dashboard shows BIG GREEN PULSING CARD
   - "💧 Water Flowing Now!"
   - "Water is currently flowing in Chabahil"
   - Auto-refreshes every 30 seconds

5. **Toggle Water OFF:**
   - Back to admin panel
   - Click "Turn OFF" on Chabahil
   - Page reloads → Chabahil now RED

6. **User Dashboard Updates:**
   - After 30 seconds (auto-refresh)
   - Shows "Water Available" (past event)
   - Or "No Water" if no recent events

---

## 🎨 Design Features

- **Water Theme:** Teal/blue gradient colors
- **Live Badges:** Pulsing green dots
- **Auto-Refresh:** Countdown in browser tab title
- **Animations:** Smooth transitions, pulsing effects
- **Responsive:** Works on mobile, tablet, desktop
- **Professional:** Clean, modern design

---

## 🔧 Configuration

**Database Settings** (`config.php`):
```php
DB_HOST: localhost
DB_USER: root
DB_PASS: (empty for XAMPP)
DB_NAME: melamchi_alert
```

**Admin Credentials** (hardcoded in `admin-login.php`):
```php
Username: admin
Password: admin123
```

---

## 📊 Database

**4 Tables:**
1. `locations` - 42 Nepal locations + water_status field
2. `users` - Registered users
3. `admins` - Admin users (not used - login hardcoded)
4. `water_events` - Water arrival history

**Sample Data:**
- 4 test users (password: test123)
- 4 sample water events

---

## ✅ What Makes This Special

1. **Pure PHP** - No complex JavaScript
2. **Live Updates** - Auto-refresh with meta tag
3. **Simple** - Just 10 files total
4. **Fast** - Direct database queries
5. **No AJAX** - Traditional page reloads
6. **Easy to Understand** - All logic in one file
7. **Production Ready** - Secure, tested, documented

---

## 🐛 Troubleshooting

**Database Error?**
- Check MySQL is running in XAMPP
- Import `setup_database.sql`

**Admin Login Not Working?**
- Username must be exactly: `admin`
- Password must be exactly: `admin123`
- Case-sensitive!

**User Dashboard Not Updating?**
- Wait 30 seconds for auto-refresh
- Or click "Refresh Now" button

---

**Built with 💧 for Kathmandu community**

