/**
 * LOGIN PAGE
 * Handles user login
 */

document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (!loginForm) return;
    
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous errors
        if (window.Validation) {
            window.Validation.clearAllErrors();
        }
        
        // Get form values
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        
        // Validate
        let hasError = false;
        
        if (!email) {
            showFieldError('email', 'Email is required');
            hasError = true;
        }
        
        if (!password) {
            showFieldError('password', 'Password is required');
            hasError = true;
        }
        
        if (hasError) return;
        
        // Submit form
        const submitBtn = document.getElementById('login-btn');
        setButtonLoading(submitBtn, true);
        
        try {
            const formData = new FormData();
            formData.append('email', email);
            formData.append('password', password);
            
            const response = await fetch('php/login.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('Login successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = 'dashboard.html';
                }, 1500);
            } else {
                showAlert(data.message || 'Invalid email or password', 'error');
                setButtonLoading(submitBtn, false);
            }
        } catch (error) {
            console.error('Login error:', error);
            showAlert('An error occurred. Please try again.', 'error');
            setButtonLoading(submitBtn, false);
        }
    });
});

// Helper functions
function showFieldError(fieldId, message) {
    if (window.Validation) {
        window.Validation.showFieldError(fieldId, message);
    }
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

