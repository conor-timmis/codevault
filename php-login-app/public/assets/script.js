// Password change form handling
document.addEventListener('DOMContentLoaded', function() {
    const changePasswordForm = document.querySelector('form[action=""]');
    
    if (changePasswordForm) {
        const oldPasswordField = document.getElementById('old_password');
        const newPasswordField = document.getElementById('new_password');
        const confirmPasswordField = document.getElementById('confirm_password');
        
        // Clear any autofilled values on page load
        setTimeout(() => {
            if (oldPasswordField) oldPasswordField.value = '';
            if (newPasswordField) newPasswordField.value = '';
            if (confirmPasswordField) confirmPasswordField.value = '';
        }, 100);
        
        // Add form validation
        changePasswordForm.addEventListener('submit', function(e) {
            const oldPassword = oldPasswordField.value.trim();
            const newPassword = newPasswordField.value.trim();
            const confirmPassword = confirmPasswordField.value.trim();
            
            // Clear any leading/trailing whitespace
            oldPasswordField.value = oldPassword;
            newPasswordField.value = newPassword;
            confirmPasswordField.value = confirmPassword;
            
            if (!oldPassword || !newPassword || !confirmPassword) {
                e.preventDefault();
                alert('Please fill in all fields');
                return false;
            }
            
            if (newPassword.length < 6) {
                e.preventDefault();
                alert('New password must be at least 6 characters long');
                return false;
            }
            
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('New passwords do not match');
                return false;
            }
            
            if (oldPassword === newPassword) {
                e.preventDefault();
                alert('New password must be different from your current password');
                return false;
            }
        });
    }
});

// General form enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Add focus effects to form inputs
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });
});
