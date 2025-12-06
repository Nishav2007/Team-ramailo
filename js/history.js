/**
 * HISTORY PAGE
 * Water supply history and analytics
 */

let allEvents = [];

document.addEventListener('DOMContentLoaded', function() {
    checkSession();
    loadHistory();
    setupLogout();
    setupFilters();
});

// Check session
async function checkSession() {
    try {
        const response = await fetch('php/session_check.php');
        const data = await response.json();
        
        if (!data.loggedIn) {
            window.location.href = 'login.html';
        } else {
            document.getElementById('user-location').textContent = data.user.location || 'Unknown';
        }
    } catch (error) {
        console.error('Session check error:', error);
        window.location.href = 'login.html';
    }
}

// Load history
async function loadHistory(days = 30) {
    try {
        const response = await fetch(`php/get_history.php?days=${days}`);
        const data = await response.json();
        
        if (data.success) {
            allEvents = data.events || [];
            displayHistory(allEvents);
            displayStatistics(data.stats || {});
        } else {
            showAlert(data.message || 'Failed to load history', 'error');
        }
    } catch (error) {
        console.error('Error loading history:', error);
        showAlert('Error loading history data', 'error');
    }
}

// Display history table
function displayHistory(events) {
    const tbody = document.getElementById('history-tbody');
    const emptyState = document.getElementById('empty-state');
    const tableContainer = document.querySelector('.table-container');
    
    if (events.length === 0) {
        tbody.innerHTML = '';
        tableContainer.style.display = 'none';
        emptyState.style.display = 'block';
        return;
    }
    
    tableContainer.style.display = 'block';
    emptyState.style.display = 'none';
    
    tbody.innerHTML = '';
    
    events.forEach((event, index) => {
        const row = document.createElement('tr');
        
        const daysSince = index > 0 ? calculateDaysDifference(
            events[index - 1].arrival_date,
            event.arrival_date
        ) : '-';
        
        row.innerHTML = `
            <td>${events.length - index}</td>
            <td>${formatDate(event.arrival_date)}</td>
            <td>${getDayName(event.arrival_date)}</td>
            <td>${formatTime(event.arrival_time)}</td>
            <td>${event.location_name}</td>
            <td>${daysSince}</td>
        `;
        
        tbody.appendChild(row);
    });
}

// Display statistics
function displayStatistics(stats) {
    document.getElementById('total-events-history').textContent = stats.totalEvents || '0';
    document.getElementById('avg-frequency').textContent = stats.avgFrequency || '-';
    document.getElementById('most-common-day').textContent = stats.mostCommonDay || '-';
    document.getElementById('most-common-hour').textContent = stats.mostCommonHour || '-';
}

// Setup filters
function setupFilters() {
    const filterPeriod = document.getElementById('filter-period');
    
    if (filterPeriod) {
        filterPeriod.addEventListener('change', function() {
            const value = this.value;
            const days = value === 'all' ? 'all' : parseInt(value);
            loadHistory(days);
        });
    }
}

// Export history
function exportHistory() {
    if (allEvents.length === 0) {
        showAlert('No data to export', 'warning');
        return;
    }
    
    // Create CSV
    let csv = 'Date,Day,Time,Location,Days Since Last\n';
    
    allEvents.forEach((event, index) => {
        const daysSince = index > 0 ? calculateDaysDifference(
            allEvents[index - 1].arrival_date,
            event.arrival_date
        ) : '-';
        
        csv += `${event.arrival_date},${getDayName(event.arrival_date)},${event.arrival_time},${event.location_name},${daysSince}\n`;
    });
    
    // Download
    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `melamchi-water-history-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
    
    showAlert('History exported successfully', 'success');
}

// Helper functions
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
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

function calculateDaysDifference(date1, date2) {
    const d1 = new Date(date1);
    const d2 = new Date(date2);
    const diffTime = Math.abs(d1 - d2);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

function setupLogout() {
    const logoutLink = document.getElementById('logout-link');
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

function showAlert(message, type) {
    if (window.MelamchiUtils) {
        window.MelamchiUtils.showAlert(message, type);
    }
}

