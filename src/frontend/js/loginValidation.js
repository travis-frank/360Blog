document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const email = document.getElementById('email');
    const password = document.getElementById('password');

    // Password validation
    function isPasswordValid(password) {
        return password.length >= 8 && 
            /[A-Z]/.test(password) && 
            /[a-z]/.test(password) && 
            /[0-9]/.test(password) && 
            /[!@#$%^&*]/.test(password); 
    }

    form.addEventListener('submit', function(event) {
        let isValid = true;
        let errorMessage = '';

        // Check email
        if (!email.value.includes('@')) {
            errorMessage += 'Please enter a valid email\n';
            isValid = false;
        }

        // Check password
        if (!isPasswordValid(password.value)) {
            errorMessage += 'Password must be 8+ characters with uppercase, lowercase, number and special character\n';
            isValid = false;
        }

        if (!isValid) {
            event.preventDefault();
            alert(errorMessage);
        }
    });
});
