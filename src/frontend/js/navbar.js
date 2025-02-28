document.addEventListener('DOMContentLoaded', function() {
    const userRole = localStorage.getItem('userRole');
    const adminLink = document.querySelector('.admin-only');
    
    if (adminLink && userRole === 'admin') {
        adminLink.style.display = 'block';
    }
});
