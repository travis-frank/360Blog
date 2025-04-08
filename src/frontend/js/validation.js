document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signupForm');
    const password = document.getElementById('password');
    const email = document.getElementById('email');
    const name = document.getElementById('name');
    const profileImage = document.getElementById('profileImage');

    // Simple password validation
    function isPasswordValid(password) {
        return password.length >= 8 && 
            /[A-Z]/.test(password) && 
            /[a-z]/.test(password) && 
            /[0-9]/.test(password) && 
            /[!@#$%^&*]/.test(password); 
    }

    // Form submission
    form.addEventListener('submit', function(event) {
        let isValid = true;
        let errorMessage = '';

        // Check name
        if (name.value.trim() === '') {
            errorMessage += 'Name is required\n';
            isValid = false;
        }

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

        // Check image
        if (profileImage.files[0]) {
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!validTypes.includes(profileImage.files[0].type)) {
                errorMessage += 'Please select a valid image file (JPEG or PNG)\n';
                isValid = false;
            }
        }

        if (!isValid) {
            event.preventDefault();
            alert(errorMessage);
        }
    });
});