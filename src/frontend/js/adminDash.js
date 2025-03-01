document.addEventListener('DOMContentLoaded', function() {
    const manageUsersLink = document.querySelector('[data-section="users"]');
    const managePostsLink = document.querySelector('[data-section="posts"]');
    const manageTopicsLink = document.querySelector('[data-section="topics"]');
    const usersSection = document.querySelector('#users-section');
    const postsSection = document.querySelector('#posts-section');
    const topicsSection = document.querySelector('#topics-section');

    // Function to switch active section
    function switchSection(activeSection) {
        usersSection.style.display = 'none';
        postsSection.style.display = 'none';
        topicsSection.style.display = 'none';

        // Remove active class from all links
        manageUsersLink.classList.remove('active');
        managePostsLink.classList.remove('active');
        manageTopicsLink.classList.remove('active');

        // Show active section and highlight link
        if (activeSection === 'users') {
            usersSection.style.display = 'block';
            manageUsersLink.classList.add('active');
        } else if (activeSection === 'posts') {
            postsSection.style.display = 'block';
            managePostsLink.classList.add('active');
        } else if (activeSection === 'topics') {
            topicsSection.style.display = 'block';
            manageTopicsLink.classList.add('active');
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
    manageTopicsLink.addEventListener('click', (e) => {
        e.preventDefault();
        switchSection('topics');
    });

    // Initialize with users section active
    switchSection('users');
});
