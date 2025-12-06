# ⚡ Quick Start Guide - 5 Minutes Setup

Get the Melamchi Water Alert System running in 5 minutes!

---

## 🚀 Step 1: Install XAMPP (2 minutes)

1. Download XAMPP: [apachefriends.org](https://www.apachefriends.org/)
2. Install it
3. Open XAMPP Control Panel
4. Click **Start** for Apache and MySQL

✅ Both should show green "Running"

---

## 📁 Step 2: Copy Files (30 seconds)

Copy the `GoodDream` folder to:

**Windows:** `C:\xampp\htdocs\GoodDream\`

**Mac:** `/Applications/XAMPP/htdocs/GoodDream/`

**Linux:** `/opt/lampp/htdocs/GoodDream/`

---

## 🗄️ Step 3: Create Database (1 minute)

1. Open: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Click **"Import"** tab
3. Choose file: `GoodDream/database/schema.sql`
4. Click **"Go"**

✅ Wait for success message

---

## 🧪 Step 4: Test It! (1 minute)

### Test 1: Homepage
Open: [http://localhost/GoodDream/index.html](http://localhost/GoodDream/index.html)

Should see: Beautiful teal-themed homepage ✅

### Test 2: Register a User
1. Click **Register**
2. Fill form (use any test email)
3. Select location: **Chabahil**
4. Click Register

Should see: "Registration successful!" ✅

### Test 3: Login as Admin
1. Go to: [http://localhost/GoodDream/admin-login.html](http://localhost/GoodDream/admin-login.html)
2. Username: `admin`
3. Password: `admin123`
4. Click Login

Should see: Admin panel ✅

### Test 4: Mark Water Arrival
1. In admin panel, search location: **Chabahil**
2. Click **"Mark Water as Arrived"**
3. Confirm

Should see: "Success! X email alerts sent" ✅

---

## ✅ You're Done!

**Your system is now running!**

### What you can do now:

1. **Register real users** with their locations
2. **Mark water arrivals** from admin panel
3. **View history** of water events
4. **Check dashboard** for statistics

---

## 📚 Next Steps

- Read **README.md** for full documentation
- See **SETUP_GUIDE.md** for detailed configuration
- Check **TESTING.md** to test all features

---

## 🐛 Having Issues?

### Issue: "Database connection failed"
→ Make sure MySQL is running in XAMPP (green)
→ Re-import `schema.sql`

### Issue: "Location dropdown empty"
→ Check database has locations: `SELECT * FROM locations;`
→ Verify Apache and MySQL both running

### Issue: "Page not found"
→ Check URL: `http://localhost/GoodDream/index.html`
→ Verify folder name is correct

---

## 🎉 That's it!

**You now have a working water alert system!**

**Default Admin Login:**
- Username: `admin`
- Password: `admin123`

⚠️ **Remember to change the admin password for production!**

---

**Built with 💧 for the Kathmandu community**

