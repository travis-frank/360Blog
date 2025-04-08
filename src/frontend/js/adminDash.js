document.addEventListener('DOMContentLoaded', function() {
  console.log('Chart.js loaded:', typeof Chart);
  console.log('Chart.js version:', Chart ? Chart.version : 'Not loaded');

  const manageUsersLink = document.querySelector('[data-section="users"]');
  const managePostsLink = document.querySelector('[data-section="posts"]');
  const manageTopicsLink = document.querySelector('[data-section="topics"]');
  const manageAnalyticsLink = document.querySelector('[data-section="analytics"]');

  const usersSection = document.getElementById('users-section');
  const postsSection = document.getElementById('posts-section');
  const topicsSection = document.getElementById('topics-section');
  const analyticsSection = document.getElementById('analytics-section');

  function switchSection(activeSection) {
    console.log(`Switching to section: ${activeSection}`);
    usersSection.style.display = 'none';
    postsSection.style.display = 'none';
    topicsSection.style.display = 'none';
    analyticsSection.style.display = 'none';

    manageUsersLink.classList.remove('active');
    managePostsLink.classList.remove('active');
    manageTopicsLink.classList.remove('active');
    manageAnalyticsLink.classList.remove('active'); 

    if (activeSection === 'users') {
      usersSection.style.display = 'block';
      manageUsersLink.classList.add('active');
      loadUsers();
    } else if (activeSection === 'posts') {
      postsSection.style.display = 'block';
      managePostsLink.classList.add('active');
      loadPosts();
    } else if (activeSection === 'topics') {
      topicsSection.style.display = 'block';
      manageTopicsLink.classList.add('active');
      loadTopics();
    } else if (activeSection === 'analytics') {
      analyticsSection.style.display = 'block';
      manageAnalyticsLink.classList.add('active');
      renderAnalytics();
    }
  }

  manageUsersLink.addEventListener('click', e => {
    e.preventDefault();
    switchSection('users');
  });

  managePostsLink.addEventListener('click', e => {
    e.preventDefault();
    switchSection('posts');
  });

  manageTopicsLink.addEventListener('click', e => {
    e.preventDefault();
    switchSection('topics');
  });

  manageAnalyticsLink.addEventListener('click', e => { 
    e.preventDefault();
    switchSection('analytics');
  });

  switchSection('users');

  function loadUsers(query = '') {
    console.log(`Loading users with query: ${query}`);
    fetch(`../../src/backend/searchUser.php?query=${encodeURIComponent(query)}`)
      .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(users => {
        console.log('Users:', users);
        const userResults = document.getElementById('user-results');
        let html = '';
        users.forEach(user => {
          html += `
            <tr>
              <td>${user.name}</td>
              <td>${user.email}</td>
              <td>${user.role}</td>
              <td>${user.is_active == 1 ? 'Active' : 'Disabled'}</td>
              <td>
                <button class="btn btn-sm btn-danger btn-toggle-status" data-user-id="${user.user_id}">
                  ${user.is_active == 1 ? 'Disable' : 'Enable'}
                </button>
              </td>
              <td>
                <form action="../../src/backend/updateUserRole.php" method="post" class="update-role-form" data-user-id="${user.user_id}">
                  <input type="hidden" name="user_id" value="${user.user_id}" />
                  <select name="new_role">
                    <option value="user" ${user.role.toLowerCase() === 'user' ? 'selected' : ''}>User</option>
                    <option value="admin" ${user.role.toLowerCase() === 'admin' ? 'selected' : ''}>Admin</option>
                  </select>
                  <button type="button" class="btn btn-sm btn-secondary update-role-btn">Update Role</button>
                </form>
              </td>
            </tr>
          `;
        });
        userResults.innerHTML = html;

        document.querySelectorAll('.btn-toggle-status').forEach(btn => {
          btn.addEventListener('click', () => {
            const userId = btn.getAttribute('data-user-id');
            toggleUserStatus(userId);
          });
        });
      })
      .catch(error => console.error('Error fetching users:', error));
  }

  function loadPosts(query = '') {
    console.log(`Loading posts with query: ${query}`);
    fetch(`../../src/backend/managePosts.php?query=${encodeURIComponent(query)}`)
      .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(posts => {
        console.log('Posts:', posts);
        const postsTableBody = document.getElementById('posts-table-body');
        let html = '';
        posts.forEach(post => {
          html += `
            <tr>
              <td>${post.title}</td>
              <td>${post.author}</td>
              <td>${new Date(post.created_at).toLocaleDateString()}</td>
              <td>${post.is_deleted == 1 ? 'Deleted' : 'Active'}</td>
              <td>
                <button class="btn btn-sm btn-danger btn-delete-post" data-post-id="${post.post_id}">Delete</button>
              </td>
            </tr>
          `;
        });
        postsTableBody.innerHTML = html;

        document.querySelectorAll('.btn-delete-post').forEach(btn => {
          btn.addEventListener('click', () => {
            const postId = btn.getAttribute('data-post-id');
            if (confirm(`Are you sure you want to delete the post with ID: ${postId}?`)) {
              deletePost(postId);
            }
          });
        });
      })
      .catch(error => console.error('Error fetching posts:', error));
  }

  function deletePost(postId) {
    console.log(`Deleting post with ID: ${postId}`);
    fetch('../../src/backend/managePosts.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ postId: postId, action: 'delete' })
    })
      .then(response => response.text())
      .then(() => {
        loadPosts();
      })
      .catch(error => console.error('Error deleting post:', error));
  }

  function loadTopics(query = '') {
    console.log(`Loading topics with query: ${query}`);
    fetch(`../../src/backend/getTopics.php?query=${encodeURIComponent(query)}`)
      .then(response => response.json())
      .then(topics => {
        console.log('Topics:', topics);
        const topicsTableBody = document.getElementById('topics-table-body');
        let html = '';
        topics.forEach(topic => {
          html += `
            <tr>
              <td>${topic.topic}</td>
              <td>
                <button class="btn btn-sm btn-danger btn-remove-topic" data-topic="${topic.topic}">Remove</button>
              </td>
            </tr>
          `;
        });
        topicsTableBody.innerHTML = html;

        document.querySelectorAll('.btn-remove-topic').forEach(btn => {
          btn.addEventListener('click', function() {
            const topic = btn.getAttribute('data-topic');
            if (confirm(`Are you sure you want to delete the topic: "${topic}"?`)) {
              removeTopic(topic);
            }
          });
        });
      })
      .catch(error => console.error('Error loading topics:', error));
  }

  function removeTopic(topic) {
    console.log(`Removing topic: ${topic}`);
    fetch('../../src/backend/removeTopic.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ topic: topic })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          loadTopics();
        } else {
          console.error(data.error);
        }
      })
      .catch(error => console.error('Error removing topic:', error));
  }

  const userSearchInput = document.querySelector('.user-search-input');
  if (userSearchInput) {
    userSearchInput.addEventListener('input', function() {
      loadUsers(this.value);
    });
  }

  const postSearchInput = document.querySelector('.post-search-input');
  if (postSearchInput) {
    postSearchInput.addEventListener('input', function() {
      loadPosts(this.value);
    });
  }

  const addTopicForm = document.getElementById('add-topic-form');
  if (addTopicForm) {
    addTopicForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(addTopicForm);
      fetch('../../src/backend/addTopic.php', {
        method: 'POST',
        body: formData
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            addTopicForm.reset();
            loadTopics();
          } else {
            console.error(data.error);
          }
        })
        .catch(error => console.error('Error adding topic:', error));
    });
  }

  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('update-role-btn')) {
      const form = e.target.closest('.update-role-form');
      const userId = form.getAttribute('data-user-id');
      const newRole = form.querySelector('select[name="new_role"]').value;

      fetch('../../src/backend/toggleUserStatus.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ user_id: userId, new_role: newRole }),
      })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert(`User role updated to: ${data.new_role}`);
          } else {
            alert(`Failed to update role: ${data.error}`);
          }
        })
        .catch(error => console.error('Error updating role:', error));
    }
  });

  function toggleUserStatus(userId) {
    console.log(`Toggling status for user ID: ${userId}`);
    fetch('../../src/backend/toggleUserStatus.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ user_id: userId }),
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('User status updated successfully.');
          loadUsers(); 
        } else {
          alert(`Failed to update user status: ${data.error}`);
        }
      })
      .catch(error => console.error('Error toggling user status:', error));
  }

  function renderAnalytics() {
    console.log('Fetching analytics data');
    fetch('../../src/backend/getAnalyticsData.php')
      .then(res => {
        console.log('Response received:', res);
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
      })
      .then(data => {
        console.log('Analytics data:', data);
        setTimeout(() => { 
          renderPostsTrendChart(data.postDates);
          renderCategoryPieChart(data.categories);
          renderSiteUsageChart(data.siteUsage);
        }, 100);
      })
      .catch(err => console.error('Error loading analytics:', err));
  }

  function renderPostsTrendChart(postDates) {
    console.log('Rendering posts trend chart with data:', postDates);
    const canvas = document.getElementById('postsTrendChart');
    if (!canvas) {
      console.error('Canvas element for postsTrendChart not found');
      return;
    }
    const ctx = canvas.getContext('2d');
    if (!ctx) {
      console.error('Canvas context for postsTrendChart not found');
      return;
    }
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: postDates.labels,
        datasets: [{
          label: 'Posts Over Time',
          data: postDates.counts,
          borderColor: '#007bff',
          backgroundColor: 'rgba(0,123,255,0.1)',
          fill: true,
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

  function renderCategoryPieChart(categories) {
    console.log('Rendering category pie chart with data:', categories);
    const canvas = document.getElementById('categoryPieChart');
    if (!canvas) {
      console.error('Canvas element for categoryPieChart not found');
      return;
    }
    const ctx = canvas.getContext('2d');
    if (!ctx) {
      console.error('Canvas context for categoryPieChart not found');
      return;
    }
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: categories.labels,
        datasets: [{
          data: categories.counts,
          backgroundColor: [
            '#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d'
          ]
        }]
      },
      options: {
        responsive: true
      }
    });
  }
  function renderSiteUsageChart(siteUsage) {
    const canvas = document.getElementById('siteUsageChart');
    if (!canvas) return;
  
    const ctx = canvas.getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: siteUsage.labels,
        datasets: [
          {
            label: 'New Users',
            data: siteUsage.users,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Posts',
            data: siteUsage.posts,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40,167,69,0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Topics',
            data: siteUsage.topics,
            borderColor: '#ffc107',
            backgroundColor: 'rgba(255,193,7,0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Comments',
            data: siteUsage.comments,
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220,53,69,0.1)',
            fill: true,
            tension: 0.3
          },
          {
            label: 'Likes',
            data: siteUsage.likes,
            borderColor: '#6f42c1',
            backgroundColor: 'rgba(111,66,193,0.1)',
            fill: true,
            tension: 0.3
          }
        ]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Weekly Site Activity'
          },
          legend: {
            position: 'top'
          },
          tooltip: {
            mode: 'index',
            intersect: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Count'
            }
          },
          x: {
            title: {
              display: true,
              text: 'Week'
            }
          }
        },
        interaction: {
          mode: 'nearest',
          axis: 'x',
          intersect: false
        }
      }
    });
  }  
});