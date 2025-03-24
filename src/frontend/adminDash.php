<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Paths to your CSS in /styles -->
    <link rel="stylesheet" href="styles/adminDash.css" />
    <link rel="stylesheet" href="styles/nav.css" />
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
      <img src="../../Images/logo.png" alt="Logo" class="navbar-brand" />
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link" href="feed.php">Feed</a></li>
          <li class="nav-item"><a class="nav-link" href="frontPage.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="userDash.php">Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="createPost.php">Create Post</a></li>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-item"><a class="nav-link" href="adminDash.php">Admin Dashboard</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link fw-bold text-danger" href="php/logout.php">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link fw-bold text-success" href="login.php">Login</a>
                </li>
            <?php endif; ?>

            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <span class="navbar-text text-white me-3">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
            <?php endif; ?>
        </ul>
        <form class="d-flex">
          <input type="text" class="form-control search-bar" placeholder="Search users..." />
        </form>
      </div>
    </nav>

    <div class="admin-container">
      <div class="sidebar">
        <div class="sidebar-header">
          <h4>Admin Panel</h4>
        </div>
        <ul class="nav flex-column">
          <li class="nav-item">
            <a href="#" class="nav-link active" data-section="users">Manage Users</a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link" data-section="posts">Manage Posts</a>
          </li>
          <li class="nav-item">
            <a href="#" class="nav-link" data-section="topics">Manage Topics</a>
          </li>
        </ul>
      </div>

      <div class="main-content">
        <div id="users-section">
          <div class="content-header">
            <h2>Manage Users</h2>
            <div class="header-actions">
              <input
                type="text"
                class="form-control user-search-input"
                placeholder="Search users..."
              />
            </div>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Username</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th>Status</th>
                  <th>Actions</th>
                  <th>Change Role</th>
                </tr>
              </thead>
              <tbody id="user-results"></tbody>
            </table>
          </div>
        </div>

        <div id="posts-section" style="display: none;">
          <div class="content-header">
            <h2>Manage Posts</h2>
            <div class="header-actions">
              <input
                type="text"
                class="form-control post-search-input"
                placeholder="Search posts by title..."
              />
            </div>
          </div>
          <table class="table">
            <thead>
              <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="posts-table-body"></tbody>
          </table>
        </div>

        <div id="topics-section" style="display: none;">
          <div class="content-header">
            <h2>Manage Topics</h2>
          </div>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Topic Name</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="topics-table-body"></tbody>
            </table>
          </div>
          <div class="add-topic-form mt-4">
            <h3>Add New Topic</h3>
            <form id="add-topic-form">
              <div class="mb-3">
                <label for="topicName" class="form-label">Topic Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="topicName"
                  name="topic_name"
                  placeholder="Enter new topic"
                  required
                />
              </div>
              <button type="submit" class="btn btn-dark">Add Topic</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/adminDash.js"></script>
  </body>
</html>