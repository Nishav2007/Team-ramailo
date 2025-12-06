# 🧪 Testing Guide - Melamchi Water Alert System

Complete testing checklist to ensure everything works properly.

---

## 📋 Pre-Testing Checklist

Before testing, verify:

- [ ] XAMPP Apache is running (green in control panel)
- [ ] XAMPP MySQL is running (green in control panel)
- [ ] Database `melamchi_alert` exists
- [ ] All 4 tables created (admins, locations, users, water_events)
- [ ] Sample data inserted
- [ ] Website accessible at `http://localhost/GoodDream/`

---

## 🧪 Test Suite 1: Basic Functionality

### Test 1.1: Homepage Load

**Steps:**
1. Open browser
2. Go to `http://localhost/GoodDream/index.html`

**Expected Results:**
- ✅ Page loads without errors
- ✅ Navigation bar appears with teal theme
- ✅ Hero section shows water droplets animation
- ✅ All sections visible (Problem, How It Works, Features)
- ✅ Footer displays

**Status:** [ ] Pass [ ] Fail

---

### Test 1.2: Location Dropdown

**Steps:**
1. Go to registration page
2. Click on location search box
3. Type "chab"

**Expected Results:**
- ✅ Dropdown appears with locations
- ✅ "Chabahil" appears in filtered results
- ✅ Click on "Chabahil" populates the field
- ✅ Dropdown closes after selection

**Status:** [ ] Pass [ ] Fail

---

### Test 1.3: User Registration

**Test Data:**
```
Name: Test User Alpha
Email: testalpha@example.com
Password: test123456
Confirm Password: test123456
Location: Chabahil
```

**Steps:**
1. Go to `register.html`
2. Fill all fields
3. Click Register

**Expected Results:**
- ✅ Form validates (no errors)
- ✅ Success message appears
- ✅ Redirects to login page after 2 seconds
- ✅ Database check: User exists in `users` table

**Database Verification:**
```sql
SELECT * FROM users WHERE email = 'testalpha@example.com';
```

**Status:** [ ] Pass [ ] Fail

---

### Test 1.4: Registration Validation

**Test Cases:**

#### Test 1.4a: Empty Email
- Leave email blank
- **Expected:** Error "Email is required"
- **Status:** [ ] Pass [ ] Fail

#### Test 1.4b: Invalid Email
- Enter: "notanemail"
- **Expected:** Error "Please enter a valid email address"
- **Status:** [ ] Pass [ ] Fail

#### Test 1.4c: Password Too Short
- Enter: "123"
- **Expected:** Error "Password must be at least 6 characters"
- **Status:** [ ] Pass [ ] Fail

#### Test 1.4d: Passwords Don't Match
- Password: "test123"
- Confirm: "test456"
- **Expected:** Error "Passwords do not match"
- **Status:** [ ] Pass [ ] Fail

#### Test 1.4e: No Location Selected
- Don't select location
- **Expected:** Error "Please select your location"
- **Status:** [ ] Pass [ ] Fail

---

### Test 1.5: User Login

**Steps:**
1. Go to `login.html`
2. Enter:
   - Email: `testalpha@example.com`
   - Password: `test123456`
3. Click Login

**Expected Results:**
- ✅ "Login successful" message appears
- ✅ Redirects to dashboard
- ✅ Dashboard shows user name "Test User Alpha"
- ✅ Dashboard shows location "Chabahil"

**Status:** [ ] Pass [ ] Fail

---

### Test 1.6: Invalid Login

**Steps:**
1. Try logging in with wrong password

**Expected Results:**
- ✅ Error message "Invalid email or password"
- ✅ Does NOT redirect
- ✅ Password field clears

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 2: Admin Functionality

### Test 2.1: Admin Login

**Steps:**
1. Go to `admin-login.html`
2. Enter:
   - Username: `admin`
   - Password: `admin123`
3. Click Login

**Expected Results:**
- ✅ Success message
- ✅ Redirects to admin panel
- ✅ Shows "Admin Panel" in navigation
- ✅ Displays admin username

**Status:** [ ] Pass [ ] Fail

---

### Test 2.2: Admin Statistics Load

**Steps:**
1. Login as admin
2. Observe admin panel

**Expected Results:**
- ✅ Total Users count displays
- ✅ Total Locations count displays
- ✅ Events Today count displays
- ✅ Emails Sent Today count displays
- ✅ Recent events table loads
- ✅ Users by location table loads

**Status:** [ ] Pass [ ] Fail

---

### Test 2.3: Mark Water Arrival

**Steps:**
1. Login as admin
2. In location dropdown, search "Chabahil"
3. Select "Chabahil"
4. Click "Mark Water as Arrived & Send Alerts"
5. Confirm the popup

**Expected Results:**
- ✅ Selected location badge appears
- ✅ Confirmation dialog shows
- ✅ Success message appears
- ✅ Shows "X email alerts sent"
- ✅ Recent events table updates
- ✅ Form resets after submission

**Database Verification:**
```sql
SELECT * FROM water_events ORDER BY created_at DESC LIMIT 1;
```
Should show new event for Chabahil

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 3: User Dashboard

### Test 3.1: Dashboard Data Load

**Prerequisites:**
- User logged in
- At least one water event exists for user's location

**Steps:**
1. Login as test user (Chabahil)
2. View dashboard

**Expected Results:**
- ✅ Status card shows "Water Available" or "No Recent Water Supply"
- ✅ If water available, shows date/time
- ✅ Statistics display:
  - Last Supply date
  - Frequency
  - Common Time
  - Total Events
- ✅ Recent events list shows past events
- ✅ Quick actions cards appear

**Status:** [ ] Pass [ ] Fail

---

### Test 3.2: Dashboard Refresh

**Steps:**
1. On dashboard, click "Refresh Status"

**Expected Results:**
- ✅ Success message "Dashboard refreshed"
- ✅ Data reloads
- ✅ Shows updated information

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 4: History Page

### Test 4.1: History Load

**Steps:**
1. Login as user
2. Navigate to History page

**Expected Results:**
- ✅ Page loads
- ✅ Location name displays correctly
- ✅ Filter dropdown works
- ✅ Statistics cards display
- ✅ History table loads with events
- ✅ Table shows: Date, Day, Time, Location, Days Since Last

**Status:** [ ] Pass [ ] Fail

---

### Test 4.2: History Filters

**Steps:**
1. On history page
2. Change filter to "Last 7 Days"
3. Change to "Last 90 Days"
4. Change to "All Time"

**Expected Results:**
- ✅ Table updates when filter changes
- ✅ Statistics recalculate
- ✅ Correct number of events shown

**Status:** [ ] Pass [ ] Fail

---

### Test 4.3: Export History

**Steps:**
1. On history page with data
2. Click "Export Data"

**Expected Results:**
- ✅ CSV file downloads
- ✅ Filename includes current date
- ✅ CSV contains all columns
- ✅ Data matches table
- ✅ Success message appears

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 5: Email Notifications

### Test 5.1: Email Sending (Manual Check)

**Note:** For XAMPP testing, emails may not actually send unless mail is configured.

**Steps:**
1. Register user with real email
2. Login as admin
3. Mark water arrival for that user's location

**Expected Results:**
- ✅ System reports "X emails sent"
- ✅ No errors in browser console
- ✅ If mail configured: Email received
- ✅ Email contains correct location
- ✅ Email contains current date/time

**Email Content Checklist:**
- [ ] Subject: "💧 Water Alert - [Location]"
- [ ] Greeting with user name
- [ ] Location mentioned
- [ ] Current date/time
- [ ] Message to collect water
- [ ] Links to history and dashboard

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 6: Session Management

### Test 6.1: User Session Persistence

**Steps:**
1. Login as user
2. Go to dashboard
3. Open new tab
4. Go to `http://localhost/GoodDream/history.html`

**Expected Results:**
- ✅ Still logged in (no redirect to login)
- ✅ History page loads correctly

**Status:** [ ] Pass [ ] Fail

---

### Test 6.2: User Logout

**Steps:**
1. Login as user
2. Click "Logout" in navigation
3. Try to access `dashboard.html` directly

**Expected Results:**
- ✅ Redirects to homepage or login
- ✅ Session destroyed
- ✅ Can't access protected pages

**Status:** [ ] Pass [ ] Fail

---

### Test 6.3: Admin Session Isolation

**Steps:**
1. Login as admin
2. Try to access `dashboard.html` (user dashboard)

**Expected Results:**
- ✅ Should redirect or show error
- ✅ Admin session separate from user session

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 7: Security

### Test 7.1: SQL Injection Protection

**Steps:**
1. On login page, try:
   - Email: `admin' OR '1'='1`
   - Password: `anything`

**Expected Results:**
- ✅ Login fails
- ✅ No database error shown
- ✅ Error message: "Invalid email or password"

**Status:** [ ] Pass [ ] Fail

---

### Test 7.2: XSS Protection

**Steps:**
1. Register with name: `<script>alert('XSS')</script>`
2. Login and view dashboard

**Expected Results:**
- ✅ Script does NOT execute
- ✅ Name displays as text (escaped)

**Status:** [ ] Pass [ ] Fail

---

### Test 7.3: Password Hashing

**Steps:**
1. Check database:
```sql
SELECT password FROM users LIMIT 1;
```

**Expected Results:**
- ✅ Password is hashed (not plain text)
- ✅ Starts with `$2y$` (bcrypt)
- ✅ At least 60 characters long

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 8: Responsive Design

### Test 8.1: Mobile View (375px)

**Steps:**
1. Open browser DevTools (F12)
2. Enable device toolbar
3. Select "iPhone SE" or similar
4. Navigate through all pages

**Expected Results:**
- ✅ Homepage displays correctly
- ✅ Navigation menu works
- ✅ Forms are usable
- ✅ Tables scroll horizontally
- ✅ Buttons are tappable
- ✅ No horizontal overflow

**Status:** [ ] Pass [ ] Fail

---

### Test 8.2: Tablet View (768px)

**Steps:**
1. Set to iPad or tablet size
2. Test all pages

**Expected Results:**
- ✅ Layout adjusts properly
- ✅ Cards stack appropriately
- ✅ Navigation readable

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 9: Browser Compatibility

### Test 9.1: Chrome

**Status:** [ ] Pass [ ] Fail

### Test 9.2: Firefox

**Status:** [ ] Pass [ ] Fail

### Test 9.3: Edge

**Status:** [ ] Pass [ ] Fail

### Test 9.4: Safari (if available)

**Status:** [ ] Pass [ ] Fail

---

## 🧪 Test Suite 10: Performance

### Test 10.1: Page Load Time

**Steps:**
1. Open DevTools Network tab
2. Load homepage
3. Check load time

**Expected Results:**
- ✅ Page loads in < 3 seconds
- ✅ All CSS files load
- ✅ All JS files load
- ✅ No 404 errors

**Status:** [ ] Pass [ ] Fail

---

### Test 10.2: Database Query Performance

**Steps:**
```sql
-- Check locations load quickly
SELECT COUNT(*) FROM locations;

-- Check water events query
SELECT * FROM water_events WHERE location_id = 4 LIMIT 10;
```

**Expected Results:**
- ✅ Queries return in < 100ms
- ✅ No slow query warnings

**Status:** [ ] Pass [ ] Fail

---

## 📊 Test Results Summary

### Overall Results

| Test Suite | Tests | Passed | Failed | % |
|-----------|-------|--------|--------|---|
| Basic Functionality | 6 | | | |
| Admin Functionality | 3 | | | |
| User Dashboard | 2 | | | |
| History Page | 3 | | | |
| Email Notifications | 1 | | | |
| Session Management | 3 | | | |
| Security | 3 | | | |
| Responsive Design | 2 | | | |
| Browser Compatibility | 4 | | | |
| Performance | 2 | | | |
| **TOTAL** | **29** | | | |

---

## 🐛 Issues Found

List any issues discovered during testing:

| # | Issue | Severity | Steps to Reproduce | Status |
|---|-------|----------|-------------------|--------|
| 1 | | | | |
| 2 | | | | |

---

## ✅ Testing Complete

**Tested by:** _________________

**Date:** _________________

**Environment:** _________________

**Overall Status:** [ ] All Pass [ ] Some Failures

---

**Next Steps:**
- Fix any failing tests
- Document any known issues
- Retest after fixes
- Deploy when all tests pass

