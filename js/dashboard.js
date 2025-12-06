/**
 * DASHBOARD PAGE
 * User dashboard with water status
 */

document.addEventListener('DOMContentLoaded', function() {
    checkSession();
    loadDashboardData();
    setupLogout();
});

// Check if user is logged in
async function checkSession() {
    try {
        const response = await fetch('php/session_check.php');
        const data = await response.json();
        
        if (!data.loggedIn) {
            window.location.href = 'login.html';
        }
    } catch (error) {
        console.error('Session check error:', error);
        window.location.href = 'login.html';
    }
}

// Load dashboard data
async function loadDashboardData() {
    try {
        const response = await fetch('php/get_dashboard_data.php');
        const data = await response.json();
        
        if (data.success) {
            displayDashboardData(data);
        } else {
            showAlert(data.message || 'Failed to load dashboard data', 'error');
        }
    } catch (error) {
        console.error('Error loading dashboard:', error);
        showAlert('Error loading dashboard data', 'error');
    }
}

// Display dashboard data
function displayDashboardData(data) {
    // User info
    document.getElementById('user-name').textContent = data.user.name || 'User';
    document.getElementById('user-location').textContent = data.user.location || 'Unknown';
    
    // Water status
    const statusCard = document.getElementById('status-card');
    const statusIcon = document.getElementById('status-icon');
    const statusTitle = document.getElementById('status-title');
    const statusMessage = document.getElementById('status-message');
    const statusTime = document.getElementById('status-time');
    
    if (data.latestEvent) {
        statusCard.className = 'status-card water-available';
        statusIcon.textContent = '💧';
        statusTitle.textContent = 'Water Available!';
        statusMessage.textContent = `Water arrived in ${data.user.location}`;
        statusTime.textContent = `Last arrival: ${formatDateTime(data.latestEvent.arrival_date, data.latestEvent.arrival_time)}`;
    } else {
        statusCard.className = 'status-card no-water';
        statusIcon.textContent = '❌';
        statusTitle.textContent = 'No Recent Water Supply';
        statusMessage.textContent = 'No water events recorded recently';
        statusTime.textContent = 'You will receive an email when water arrives';
    }
    
    // Statistics
    document.getElementById('last-supply').textContent = data.stats.lastSupply || 'N/A';
    document.getElementById('frequency').textContent = data.stats.frequency || 'N/A';
    document.getElementById('common-time').textContent = data.stats.commonTime || 'N/A';
    document.getElementById('total-events').textContent = data.stats.totalEvents || '0';
    
    // Recent events
    displayRecentEvents(data.recentEvents || []);
}

// Display recent events
function displayRecentEvents(events) {
    const eventsContainer = document.getElementById('recent-events-list');
    
    if (events.length === 0) {
        eventsContainer.innerHTML = '<p class="loading">No recent water events</p>';
        return;
    }
    
    eventsContainer.innerHTML = '';
    
    events.forEach(event => {
        const eventDiv = document.createElement('div');
        eventDiv.className = 'event-item';
        
        const date = new Date(event.arrival_date);
        const dayNum = date.getDate();
        const monthName = date.toLocaleDateString('en-US', { month: 'short' });
        
        eventDiv.innerHTML = `
            <div class="event-date">
                <span class="day">${dayNum}</span>
                <span class="month">${monthName}</span>
            </div>
            <div class="event-details">
                <h4>Water Arrived</h4>
                <p>${getDayName(event.arrival_date)} at ${formatTime(event.arrival_time)}</p>
            </div>
        `;
        
        eventsContainer.appendChild(eventDiv);
    });
}

// Format date and time
function formatDateTime(date, time) {
    const d = new Date(date);
    const dateStr = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const timeStr = formatTime(time);
    return `${dateStr} at ${timeStr}`;
}

function formatTime(timeString) {
    const [hours, minutes] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
}

function getDayName(dateString) {
    const date = new Date(dateString);
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return days[date.getDay()];
}

// Refresh dashboard
function refreshDashboard() {
    loadDashboardData();
    showAlert('Dashboard refreshed', 'success');
}

// Setup logout
function setupLogout() {
    const logoutLink = document.getElementById('logout-link');
    if (logoutLink) {
        logoutLink.addEventListener('click', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('php/logout.php');
                const data = await response.json();
                
                if (data.success) {
                    window.location.href = 'index.html';
                }
            } catch (error) {
                console.error('Logout error:', error);
                window.location.href = 'index.html';
            }
        });
    }
}

// Helper function
function showAlert(message, type) {
    if (window.MelamchiUtils) {
        window.MelamchiUtils.showAlert(message, type);
    }
}

