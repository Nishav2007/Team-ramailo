/**
 * REGISTRATION PAGE
 * Handles user registration
 */

document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    
    if (!registerForm) return;
    
    registerForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous errors
        if (window.Validation) {
            window.Validation.clearAllErrors();
        }
        
        // Get form values
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const locationId = document.getElementById('location_id').value;
        
        // Validate
        let hasError = false;
        
        if (!name) {
            showFieldError('name', 'Name is required');
            hasError = true;
        }
        
        if (!email) {
            showFieldError('email', 'Email is required');
            hasError = true;
        } else if (!validateEmail(email)) {
            showFieldError('email', 'Please enter a valid email address');
            hasError = true;
        }
        
        if (!password) {
            showFieldError('password', 'Password is required');
            hasError = true;
        } else if (password.length < 6) {
            showFieldError('password', 'Password must be at least 6 characters');
            hasError = true;
        }
        
        if (!confirmPassword) {
            showFieldError('confirm_password', 'Please confirm your password');
            hasError = true;
        } else if (password !== confirmPassword) {
            showFieldError('confirm_password', 'Passwords do not match');
            hasError = true;
        }
        
        if (!locationId) {
            showFieldError('location-search', 'Please select your location');
            hasError = true;
        }
        
        if (hasError) return;
        
        // Submit form
        const submitBtn = document.getElementById('register-btn');
        setButtonLoading(submitBtn, true);
        
        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('location_id', locationId);
            
            const response = await fetch('php/register.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('Registration successful! Redirecting to login...', 'success');
                setTimeout(() => {
                    window.location.href = 'login.html';
                }, 2000);
            } else {
                showAlert(data.message || 'Registration failed. Please try again.', 'error');
                setButtonLoading(submitBtn, false);
            }
        } catch (error) {
            console.error('Registration error:', error);
            showAlert('An error occurred. Please try again.', 'error');
            setButtonLoading(submitBtn, false);
        }
    });
});

// Helper functions (fallback if main.js not loaded)
function showFieldError(fieldId, message) {
    if (window.Validation) {
        window.Validation.showFieldError(fieldId, message);
    }
}

function validateEmail(email) {
    if (window.Validation) {
        return window.Validation.validateEmail(email);
    }
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
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

