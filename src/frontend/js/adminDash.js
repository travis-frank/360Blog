document.addEventListener('DOMContentLoaded', function() {
    const manageUsersLink = document.querySelector('[data-section="users"]');
    const managePostsLink = document.querySelector('[data-section="posts"]');
    const manageTopicsLink = document.querySelector('[data-section="topics"]');
  
    const usersSection = document.getElementById('users-section');
    const postsSection = document.getElementById('posts-section');
    const topicsSection = document.getElementById('topics-section');
  
    // Switching sections and loading data
    function switchSection(activeSection) {
      console.log(`Switching to section: ${activeSection}`);
      usersSection.style.display = 'none';
      postsSection.style.display = 'none';
      topicsSection.style.display = 'none';
  
      manageUsersLink.classList.remove('active');
      managePostsLink.classList.remove('active');
      manageTopicsLink.classList.remove('active');
  
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
  
    switchSection('users');
  
    // Fetching users from backend
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
                    <button class="btn btn-sm btn-toggle-status" data-user-id="${user.user_id}">
                      ${user.is_active == 1 ? 'Disable' : 'Enable'}
                    </button>
                  </td>
                  <td>
                    <form action="../../src/backend/updateUserRole.php" method="post">
                      <input type="hidden" name="user_id" value="${user.user_id}" />
                      <select name="new_role">
                        <option value="user"  ${user.role.toLowerCase() === 'user'  ? 'selected' : ''}>User</option>
                        <option value="admin" ${user.role.toLowerCase() === 'admin' ? 'selected' : ''}>Admin</option>
                      </select>
                      <button type="submit" class="btn btn-sm btn-secondary">Update Role</button>
                    </form>
                  </td>
                </tr>
              `;
            });
            userResults.innerHTML = html;
      
            // Status event
            document.querySelectorAll('.btn-toggle-status').forEach(btn => {
              btn.addEventListener('click', () => {
                const userId = btn.getAttribute('data-user-id');
                toggleUserStatus(userId);
              });
            });
          })
          .catch(error => console.error('Error fetching users:', error));
      }
  
    // Fetching posts from backend
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
                    <button class="btn btn-sm btn-delete-post" data-post-id="${post.post_id}">Delete</button>
                  </td>
                </tr>
              `;
            });
            postsTableBody.innerHTML = html;
      
            // Delete event
            document.querySelectorAll('.btn-delete-post').forEach(btn => {
              btn.addEventListener('click', () => {
                const postId = btn.getAttribute('data-post-id');
                deletePost(postId);
              });
            });
          })
          .catch(error => console.error('Error fetching posts:', error));
      }
  
    // Delete post
    function deletePost(postId) {
      console.log(`Deleting post with ID: ${postId}`);
      fetch(`../../src/backend/managePosts.php`, {
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
  
    // Fetching topics from backend
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

          // Attach event listeners to the remove buttons
          document.querySelectorAll('.btn-remove-topic').forEach(btn => {
            btn.addEventListener('click', function() {
              const topic = btn.getAttribute('data-topic');
              removeTopic(topic);
            });
          });
        })
        .catch(error => console.error('Error loading topics:', error));
    }

    // Remove topic
    function removeTopic(topic) {
      console.log(`Removing topic: ${topic}`);
      fetch(`../../src/backend/removeTopic.php`, {
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
  
    // Searching users/posts/topics
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
  
    // Adding a new topic
    const addTopicForm = document.getElementById('add-topic-form');
    if (addTopicForm) {
      addTopicForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(addTopicForm);
        fetch(`../../src/backend/addTopic.php`, {
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
  });