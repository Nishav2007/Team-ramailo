/**
 * ADMIN PANEL
 * Water arrival marking and system management
 */

document.addEventListener('DOMContentLoaded', function() {
    checkAdminSession();
    loadAdminData();
    setupWaterArrivalForm();
    setupLogout();
    updateCurrentTime();
});

// Check admin session
async function checkAdminSession() {
    try {
        const response = await fetch('php/session_check.php?admin=1');
        const data = await response.json();
        
        if (!data.loggedIn || !data.isAdmin) {
            window.location.href = 'admin-login.html';
        } else {
            document.getElementById('admin-username').textContent = data.admin.username || 'Admin';
        }
    } catch (error) {
        console.error('Session check error:', error);
        window.location.href = 'admin-login.html';
    }
}

// Load admin data
async function loadAdminData() {
    try {
        const response = await fetch('php/get_admin_stats.php');
        const data = await response.json();
        
        if (data.success) {
            displayAdminStats(data.stats);
            displayRecentEvents(data.recentEvents || []);
            displayUsersByLocation(data.usersByLocation || []);
        }
    } catch (error) {
        console.error('Error loading admin data:', error);
    }
}

// Display admin statistics
function displayAdminStats(stats) {
    document.getElementById('total-users').textContent = stats.totalUsers || '0';
    document.getElementById('total-locations').textContent = stats.totalLocations || '0';
    document.getElementById('total-events-today').textContent = stats.eventsToday || '0';
    document.getElementById('emails-sent-today').textContent = stats.emailsSentToday || '0';
}

// Display recent events
function displayRecentEvents(events) {
    const tbody = document.getElementById('recent-events-tbody');
    
    if (events.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="loading-row">No recent events</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    
    events.forEach(event => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${event.id}</td>
            <td>${event.location_name}</td>
            <td>${formatDate(event.arrival_date)}</td>
            <td>${formatTime(event.arrival_time)}</td>
            <td>${event.emails_sent || '-'}</td>
            <td>${event.admin_username || 'Admin'}</td>
        `;
        tbody.appendChild(row);
    });
}

// Display users by location
function displayUsersByLocation(locations) {
    const tbody = document.getElementById('users-by-location-tbody');
    
    if (locations.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="loading-row">No data available</td></tr>';
        return;
    }
    
    tbody.innerHTML = '';
    
    locations.forEach(location => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${location.location_name}</strong></td>
            <td>${location.user_count}</td>
            <td>${location.last_event || 'Never'}</td>
            <td>
                <button class="btn-table-action" onclick="quickMarkWater(${location.id}, '${location.location_name}')">
                    Mark Water
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Setup water arrival form
function setupWaterArrivalForm() {
    const form = document.getElementById('waterArrivalForm');
    
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const locationId = document.getElementById('location_id').value;
        
        if (!locationId) {
            showAlert('Please select a location', 'error');
            return;
        }
        
        const submitBtn = document.getElementById('mark-water-btn');
        
        // Confirm action
        const locationName = document.getElementById('location-search').value;
        if (!confirm(`Mark water as arrived in ${locationName}?\n\nThis will send email notifications to all registered users in this area.`)) {
            return;
        }
        
        setButtonLoading(submitBtn, true);
        
        try {
            const formData = new FormData();
            formData.append('location_id', locationId);
            
            const response = await fetch('php/mark_water_arrival.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert(`Success! ${data.emailsSent} email alerts sent to users in ${locationName}`, 'success');
                
                // Reset form
                document.getElementById('location-search').value = '';
                document.getElementById('location_id').value = '';
                document.getElementById('selected-location').style.display = 'none';
                
                // Reload data
                loadAdminData();
            } else {
                showAlert(data.message || 'Failed to mark water arrival', 'error');
            }
        } catch (error) {
            console.error('Error marking water arrival:', error);
            showAlert('An error occurred. Please try again.', 'error');
        } finally {
            setButtonLoading(submitBtn, false);
        }
    });
}

// Quick mark water (from table)
async function quickMarkWater(locationId, locationName) {
    if (!confirm(`Mark water as arrived in ${locationName}?\n\nThis will send email notifications to all registered users in this area.`)) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('location_id', locationId);
        
        const response = await fetch('php/mark_water_arrival.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(`Success! ${data.emailsSent} email alerts sent to users in ${locationName}`, 'success');
            loadAdminData();
        } else {
            showAlert(data.message || 'Failed to mark water arrival', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('An error occurred. Please try again.', 'error');
    }
}

// Update current time
function updateCurrentTime() {
    const timeElement = document.getElementById('current-time');
    if (!timeElement) return;
    
    function update() {
        const now = new Date();
        const options = {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        timeElement.textContent = now.toLocaleString('en-US', options);
    }
    
    update();
    setInterval(update, 1000);
}

// Setup logout
function setupLogout() {
    const logoutLink = document.getElementById('admin-logout');
    if (logoutLink) {
        logoutLink.addEventListener('click', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('php/logout.php');
                window.location.href = 'index.html';
            } catch (error) {
                window.location.href = 'index.html';
            }
        });
    }
}

// Helper functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

function showAlert(message, type) {
    if (window.MelamchiUtils) {
        window.MelamchiUtils.showAlert(message, type);
    }
}

function setButtonLoading(btn, loading) {
    if (window.MelamchiUtils) {
        window.MelamchiUtils.setButtonLoading(btn, loading);
    }
}

