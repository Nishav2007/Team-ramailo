/**
 * FORM VALIDATION
 * Client-side validation for all forms
 */

// Validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Validate password strength
function validatePassword(password) {
    return {
        isValid: password.length >= 6,
        message: password.length < 6 ? 'Password must be at least 6 characters' : ''
    };
}

// Show field error
function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(`${fieldId}-error`);
    const formGroup = field ? field.closest('.form-group') : null;
    
    if (formGroup) {
        formGroup.classList.add('error');
    }
    
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.classList.add('show');
    }
}

// Clear field error
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    const errorElement = document.getElementById(`${fieldId}-error`);
    const formGroup = field ? field.closest('.form-group') : null;
    
    if (formGroup) {
        formGroup.classList.remove('error');
    }
    
    if (errorElement) {
        errorElement.textContent = '';
        errorElement.classList.remove('show');
    }
}

// Clear all errors
function clearAllErrors() {
    document.querySelectorAll('.error-message').forEach(el => {
        el.textContent = '';
        el.classList.remove('show');
    });
    
    document.querySelectorAll('.form-group').forEach(el => {
        el.classList.remove('error');
    });
}

// Real-time validation setup
document.addEventListener('DOMContentLoaded', function() {
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        input.addEventListener('blur', function() {
            const email = this.value.trim();
            if (email && !validateEmail(email)) {
                showFieldError(this.id, 'Please enter a valid email address');
            } else {
                clearFieldError(this.id);
            }
        });
    });
    
    // Password validation
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(input => {
        if (input.id === 'password') {
            input.addEventListener('blur', function() {
                const password = this.value;
                const validation = validatePassword(password);
                if (password && !validation.isValid) {
                    showFieldError(this.id, validation.message);
                } else {
                    clearFieldError(this.id);
                }
            });
        }
    });
    
    // Confirm password validation
    const confirmPasswordInput = document.getElementById('confirm_password');
    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('blur', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                showFieldError(this.id, 'Passwords do not match');
            } else {
                clearFieldError(this.id);
            }
        });
    }
    
    // Clear error on input
    const allInputs = document.querySelectorAll('input, select, textarea');
    allInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearFieldError(this.id);
        });
    });
});

// Export validation functions
if (typeof window !== 'undefined') {
    window.Validation = {
        validateEmail,
        validatePassword,
        showFieldError,
        clearFieldError,
        clearAllErrors
    };
}

