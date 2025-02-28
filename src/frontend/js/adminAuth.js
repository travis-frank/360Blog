document.addEventListener('DOMContentLoaded', function() {
    const userRole = localStorage.getItem('userRole');
    
    if (userRole !== 'admin') {
        window.location.href = 'userDash.html';
    }
});
