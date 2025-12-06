/**
 * SEARCHABLE DROPDOWN
 * Handles location selection with search functionality
 */

let locations = [];
let selectedLocationId = null;

// Initialize searchable dropdown
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('location-search');
    const dropdownList = document.getElementById('location-dropdown');
    const hiddenInput = document.getElementById('location_id');
    
    if (!searchInput || !dropdownList) return;
    
    // Load locations from PHP
    loadLocations();
    
    // Show dropdown on focus
    searchInput.addEventListener('focus', function() {
        dropdownList.classList.add('show');
        if (locations.length === 0) {
            loadLocations();
        }
    });
    
    // Filter on input
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterLocations(searchTerm);
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdownList.contains(e.target)) {
            dropdownList.classList.remove('show');
        }
    });
});

// Load locations from server
async function loadLocations() {
    const dropdownList = document.getElementById('location-dropdown');
    
    try {
        const response = await fetch('php/get_locations.php');
        const data = await response.json();
        
        if (data.success) {
            locations = data.locations;
            displayLocations(locations);
        } else {
            dropdownList.innerHTML = '<div class="dropdown-empty">Failed to load locations</div>';
        }
    } catch (error) {
        console.error('Error loading locations:', error);
        dropdownList.innerHTML = '<div class="dropdown-empty">Error loading locations</div>';
    }
}

// Display locations in dropdown
function displayLocations(locationsToShow) {
    const dropdownList = document.getElementById('location-dropdown');
    
    if (locationsToShow.length === 0) {
        dropdownList.innerHTML = '<div class="dropdown-empty">No locations found</div>';
        return;
    }
    
    dropdownList.innerHTML = '';
    
    locationsToShow.forEach(location => {
        const item = document.createElement('div');
        item.className = 'dropdown-item';
        item.textContent = location.location_name;
        if (location.district) {
            item.textContent += ` (${location.district})`;
        }
        item.dataset.id = location.id;
        item.dataset.name = location.location_name;
        
        item.addEventListener('click', function() {
            selectLocation(location.id, location.location_name);
        });
        
        dropdownList.appendChild(item);
    });
}

// Filter locations based on search
function filterLocations(searchTerm) {
    if (searchTerm === '') {
        displayLocations(locations);
        return;
    }
    
    const filtered = locations.filter(location => {
        const name = location.location_name.toLowerCase();
        const district = location.district ? location.district.toLowerCase() : '';
        return name.includes(searchTerm) || district.includes(searchTerm);
    });
    
    displayLocations(filtered);
}

// Select a location
function selectLocation(id, name) {
    const searchInput = document.getElementById('location-search');
    const dropdownList = document.getElementById('location-dropdown');
    const hiddenInput = document.getElementById('location_id');
    
    selectedLocationId = id;
    
    // Update inputs
    searchInput.value = name;
    hiddenInput.value = id;
    
    // Hide dropdown
    dropdownList.classList.remove('show');
    
    // Update selected location display (for admin panel)
    const selectedLocationDiv = document.getElementById('selected-location');
    const locationBadge = document.getElementById('location-badge');
    
    if (selectedLocationDiv && locationBadge) {
        locationBadge.textContent = name;
        selectedLocationDiv.style.display = 'block';
    }
    
    // Clear error if any
    const errorMessage = document.getElementById('location-error');
    if (errorMessage) {
        errorMessage.style.display = 'none';
    }
}

// Get selected location ID
function getSelectedLocationId() {
    return selectedLocationId;
}

// Export for use in other scripts
if (typeof window !== 'undefined') {
    window.LocationDropdown = {
        getSelectedLocationId,
        selectLocation
    };
}

