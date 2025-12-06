# 🚀 Melamchi Water Alert System - Setup Guide

Complete step-by-step installation and configuration guide.

---

## 📋 Prerequisites Checklist

Before starting, make sure you have:

- [ ] Windows/Mac/Linux computer
- [ ] XAMPP installed (Apache + MySQL + PHP)
- [ ] Modern web browser (Chrome, Firefox, Edge)
- [ ] Text editor (VS Code recommended)
- [ ] 10 minutes of your time

---

## 📥 Step-by-Step Installation

### Step 1: Download and Install XAMPP

#### Windows:

1. Go to [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Download XAMPP for Windows (PHP 7.4 or higher)
3. Run the installer
4. Install to default location: `C:\xampp`
5. Complete installation

#### Mac:

1. Download XAMPP for Mac OS X
2. Open the `.dmg` file
3. Drag XAMPP to Applications folder
4. Run XAMPP from Applications

#### Linux:

```bash
chmod +x xampp-linux-installer.run
sudo ./xampp-linux-installer.run
```

### Step 2: Start XAMPP Services

1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache**
3. Click **Start** next to **MySQL**

✅ Both should show green "Running" status

![XAMPP Control Panel](https://i.imgur.com/xampp-example.png)

### Step 3: Copy Project Files

1. Locate your XAMPP installation folder:
   - Windows: `C:\xampp\htdocs\`
   - Mac: `/Applications/XAMPP/htdocs/`
   - Linux: `/opt/lampp/htdocs/`

2. Copy the entire `GoodDream` folder into `htdocs`

3. Final path should be:
   - Windows: `C:\xampp\htdocs\GoodDream\`
   - Mac: `/Applications/XAMPP/htdocs/GoodDream/`
   - Linux: `/opt/lampp/htdocs/GoodDream/`

### Step 4: Create the Database

#### Option A: Using phpMyAdmin (Recommended for beginners)

1. Open your browser
2. Go to: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
3. Click **"Import"** tab at the top
4. Click **"Choose File"**
5. Navigate to `GoodDream/database/schema.sql`
6. Click **"Go"** at the bottom
7. Wait for success message ✅

#### Option B: Using SQL Tab

1. Open [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **"SQL"** tab
3. Open `database/schema.sql` in a text editor
4. Copy **entire contents**
5. Paste into SQL box
6. Click **"Go"**

### Step 5: Verify Database Creation

1. In phpMyAdmin, look at left sidebar
2. You should see **`melamchi_alert`** database
3. Click on it
4. You should see 4 tables:
   - ✅ `admins`
   - ✅ `locations`
   - ✅ `users`
   - ✅ `water_events`

### Step 6: Test the Application

#### Test 1: Homepage

Open: [http://localhost/GoodDream/index.html](http://localhost/GoodDream/index.html)

✅ You should see the homepage with water theme

#### Test 2: Registration

1. Click **"Register"** or go to: [http://localhost/GoodDream/register.html](http://localhost/GoodDream/register.html)
2. Try the location dropdown - it should load locations
3. Fill in the form (use a test email)
4. Click Register

✅ You should see "Registration successful!"

#### Test 3: Login

1. Go to: [http://localhost/GoodDream/login.html](http://localhost/GoodDream/login.html)
2. Use credentials you just created
3. Click Login

✅ You should be redirected to dashboard

#### Test 4: Admin Panel

1. Go to: [http://localhost/GoodDream/admin-login.html](http://localhost/GoodDream/admin-login.html)
2. Login with:
   - Username: `admin`
   - Password: `admin123`

✅ You should see admin panel

---

## ⚙️ Configuration

### Database Configuration

If you changed MySQL settings, edit `php/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');                // Your MySQL password
define('DB_NAME', 'melamchi_alert');
```

### Email Configuration (For Production)

#### Gmail SMTP Setup:

1. **Enable 2-Step Verification** on your Gmail account:
   - Go to [myaccount.google.com/security](https://myaccount.google.com/security)
   - Enable 2-Step Verification

2. **Create App Password**:
   - Go to [myaccount.google.com/apppasswords](https://myaccount.google.com/apppasswords)
   - Select "Mail" and "Other"
   - Name it "Melamchi Alert"
   - Click Generate
   - **Copy the 16-character password**

3. **Update `php/config.php`**:

```php
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'abcd efgh ijkl mnop');  // App password (no spaces)
```

4. **Download PHPMailer** (for Gmail):
   - Download from: [github.com/PHPMailer/PHPMailer](https://github.com/PHPMailer/PHPMailer)
   - Extract to `php/PHPMailer/`
   - Uncomment Gmail code in `php/send_email_alerts.php`

---

## 🧪 Testing the System

### Complete Test Flow

#### 1. Register a Test User

```
Name: Test User
Email: test@example.com
Password: test123
Location: Chabahil
```

#### 2. Login as Admin

```
Username: admin
Password: admin123
```

#### 3. Mark Water Arrival

1. In admin panel, search for "Chabahil"
2. Click "Mark Water as Arrived"
3. Confirm the action
4. Check the success message

#### 4. Check Email

Look for email at `test@example.com` (or check spam folder)

#### 5. View User Dashboard

1. Logout from admin
2. Login as test user
3. Check dashboard shows latest water arrival

---

## 🗺️ Adding More Locations

To add locations not in the default list:

1. Open phpMyAdmin
2. Go to `melamchi_alert` database
3. Click on `locations` table
4. Click **"Insert"** tab
5. Fill in:
   - `location_name`: Area name
   - `district`: District name
   - `zone`: Zone name
6. Click **"Go"**

**OR** run SQL:

```sql
INSERT INTO locations (location_name, district, zone) VALUES
('New Area', 'District Name', 'Zone Name');
```

---

## 👤 Managing Admin Users

### Create New Admin

1. Generate password hash in PHP:

```php
<?php
echo password_hash('your_password', PASSWORD_DEFAULT);
?>
```

2. Run in phpMyAdmin SQL:

```sql
INSERT INTO admins (username, password) VALUES 
('newadmin', '$2y$10$...');  -- Use hash from step 1
```

### Change Admin Password

```sql
UPDATE admins 
SET password = '$2y$10$...'  -- New password hash
WHERE username = 'admin';
```

### Delete Admin

```sql
DELETE FROM admins WHERE username = 'oldadmin';
```

---

## 🐛 Common Issues & Solutions

### Issue 1: "Database connection failed"

**Symptoms**: Red error on any page

**Solutions**:
```bash
# Check MySQL is running in XAMPP
# Verify database exists
USE melamchi_alert;
SHOW TABLES;

# Re-run schema.sql if tables are missing
```

### Issue 2: "Location dropdown is empty"

**Symptoms**: No locations appear when searching

**Solutions**:
1. Open browser console (F12)
2. Look for JavaScript errors
3. Check: [http://localhost/GoodDream/php/get_locations.php](http://localhost/GoodDream/php/get_locations.php)
4. Should return JSON with locations
5. If error, check database has locations

### Issue 3: "Session not persisting / Logged out immediately"

**Symptoms**: Redirected to login after logging in

**Solutions**:
```bash
# Clear browser cookies
# Clear browser cache
# Try different browser
# Check PHP sessions in php.ini
```

### Issue 4: "Email not sending"

**Symptoms**: Water marked but no emails received

**Solutions**:
1. For testing: Emails may not work with default XAMPP
2. Check `php/send_email_alerts.php` is being called
3. For production: Setup Gmail SMTP (see configuration above)

### Issue 5: "Warning: session_start()"

**Symptoms**: PHP warning about sessions

**Solutions**:
```bash
# Windows: Check C:\xampp\tmp exists
# Create folder if missing
# Set permissions to read/write
```

### Issue 6: "404 Not Found"

**Symptoms**: Pages not loading

**Solutions**:
```bash
# Verify URL is correct:
http://localhost/GoodDream/index.html
# NOT: http://localhost/index.html

# Check folder name matches (case-sensitive on Linux/Mac)
# Restart Apache in XAMPP
```

---

## 📊 Database Backup

### Backup Database

1. Open phpMyAdmin
2. Click on `melamchi_alert` database
3. Click **"Export"** tab
4. Select **"Quick"** export method
5. Format: **SQL**
6. Click **"Go"**
7. Save file as `melamchi_backup_YYYY-MM-DD.sql`

### Restore Database

1. Create new database if needed
2. Click **"Import"** tab
3. Choose your backup `.sql` file
4. Click **"Go"**

---

## 🔐 Security Recommendations

### For Production Deployment

1. **Change Admin Password**:
   ```sql
   UPDATE admins SET password = '$2y$10$...' WHERE username = 'admin';
   ```

2. **Use Strong Passwords**:
   - Minimum 12 characters
   - Mix of letters, numbers, symbols

3. **Enable HTTPS**:
   - Get SSL certificate
   - Force HTTPS in `.htaccess`

4. **Update config.php**:
   ```php
   // Change these for production
   define('DB_PASS', 'strong_password_here');
   define('SMTP_PASSWORD', 'gmail_app_password');
   ```

5. **Protect PHP files**:
   - Move `config.php` outside web root if possible
   - Add `.htaccess` to block direct PHP file access

6. **Regular Backups**:
   - Backup database weekly
   - Backup files monthly

---

## 🌐 Deploying to Live Server

### Requirements
- Web hosting with PHP 7.4+ and MySQL
- cPanel or similar control panel
- Domain name (optional)

### Steps

1. **Export Database**:
   - Backup from local XAMPP

2. **Upload Files**:
   - FTP/Upload all files to `public_html/`

3. **Import Database**:
   - Use hosting phpMyAdmin
   - Import your `.sql` backup

4. **Update config.php**:
   ```php
   define('DB_HOST', 'localhost');  // or hosting MySQL host
   define('DB_USER', 'your_db_user');
   define('DB_PASS', 'your_db_password');
   define('DB_NAME', 'your_db_name');
   define('SITE_URL', 'https://yourdomain.com');
   ```

5. **Test Everything**:
   - Registration
   - Login
   - Admin panel
   - Email sending

---

## 📞 Getting Help

### Resources

- **README.md** - Full documentation
- **schema.sql** - Database structure with comments
- **config.php** - Configuration options

### Support

If you're stuck:
1. Check this guide thoroughly
2. Look at error messages in browser console (F12)
3. Check Apache/MySQL error logs in XAMPP
4. Create an issue with:
   - What you're trying to do
   - What error you're getting
   - Screenshots if helpful

---

## ✅ Setup Complete!

You should now have:
- ✅ XAMPP running Apache and MySQL
- ✅ Database created with sample data
- ✅ Website accessible at localhost
- ✅ Admin panel working
- ✅ User registration and login functional

**Next Steps:**
1. Test the complete flow (register → login → mark water → check dashboard)
2. Customize locations for your needs
3. Configure email for production
4. Add real users and start tracking water!

---

**Need help? Don't hesitate to ask! 🚀**

