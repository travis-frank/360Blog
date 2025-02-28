document.addEventListener('DOMContentLoaded', function() {
    const manageUsersLink = document.querySelector('[data-section="users"]');
    const managePostsLink = document.querySelector('[data-section="posts"]');
    const usersSection = document.querySelector('#users-section');
    const postsSection = document.querySelector('#posts-section');

    // Function to switch active section
    function switchSection(activeSection) {
        usersSection.style.display = 'none';
        postsSection.style.display = 'none';

        // Remove active class from all links
        manageUsersLink.classList.remove('active');
        managePostsLink.classList.remove('active');

        // Show active section and highlight link
        if (activeSection === 'users') {
            usersSection.style.display = 'block';
            manageUsersLink.classList.add('active');
        } else {
            postsSection.style.display = 'block';
            managePostsLink.classList.add('active');
        }
    }

    // Click event listeners
    manageUsersLink.addEventListener('click', (e) => {
        e.preventDefault();
        switchSection('users');
    });

    managePostsLink.addEventListener('click', (e) => {
        e.preventDefault();
        switchSection('posts');
    });

    // Initialize with users section active
    switchSection('users');
});
