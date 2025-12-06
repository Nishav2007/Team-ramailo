/**
 * ADMIN LOGIN PAGE
 * Handles admin authentication
 */

document.addEventListener('DOMContentLoaded', function() {
    const adminLoginForm = document.getElementById('adminLoginForm');
    
    if (!adminLoginForm) return;
    
    adminLoginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Clear previous errors
        if (window.Validation) {
            window.Validation.clearAllErrors();
        }
        
        // Get form values
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        
        // Validate
        let hasError = false;
        
        if (!username) {
            showFieldError('username', 'Username is required');
            hasError = true;
        }
        
        if (!password) {
            showFieldError('password', 'Password is required');
            hasError = true;
        }
        
        if (hasError) return;
        
        // Submit form
        const submitBtn = document.getElementById('admin-login-btn');
        setButtonLoading(submitBtn, true);
        
        try {
            const formData = new FormData();
            formData.append('username', username);
            formData.append('password', password);
            
            const response = await fetch('php/admin_login.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('Login successful! Redirecting to admin panel...', 'success');
                setTimeout(() => {
                    window.location.href = 'admin-panel.html';
                }, 1500);
            } else {
                showAlert(data.message || 'Invalid username or password', 'error');
                setButtonLoading(submitBtn, false);
            }
        } catch (error) {
            console.error('Admin login error:', error);
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

