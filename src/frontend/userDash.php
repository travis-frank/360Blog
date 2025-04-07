<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once 'php/DBConnect.php';

$user_id = $_SESSION['user_id'];
$query = $conn->prepare("SELECT name, email, bio, password, profile_image FROM users WHERE user_id = ?");
$query->bind_param("i", $user_id);
$query->execute();
$result = $query->get_result();
$user = $result->fetch_assoc();

$postQuery = $conn->prepare("SELECT post_id, user_id, title, description, created_at FROM posts WHERE user_id = ?");
$postQuery->bind_param("i", $user_id);
$postQuery->execute();
$posts = $postQuery->get_result();

$likeQuery = $conn->prepare("SELECT p.post_id, p.title, p.description, p.created_at FROM likes l JOIN posts p ON l.post_id = p.post_id WHERE l.user_id = ?");
$likeQuery->bind_param("i", $user_id);
$likeQuery->execute();
$likedPosts = $likeQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="styles/nav.css"/>
  <link rel="stylesheet" href="styles/userDash.css"/>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <img src="../../Images/logo.png" alt="Logo" class="navbar-brand">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                <li class="nav-item"><a class="nav-link fw-bold text-danger" href="php/logout.php">Logout</a></li>
            <?php else: ?>
                <li class="nav-item"><a class="nav-link fw-bold text-success" href="login.php">Login</a></li>
            <?php endif; ?>
        </ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span class="navbar-text text-white me-3">Welcome, <?= htmlspecialchars($_SESSION['name']) ?></span>
        <?php endif; ?>
        <form class="d-flex" action="searchResults.php" method="GET">
            <input type="text" class="form-control search-bar" name="query" placeholder="Search..." required>
        </form>
    </div>
</nav>

<div class="container py-5">
    <div class="card-profile">
        <h2 class="dashboard-heading">Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="profile-picture">
                    <img src="data:image/jpeg;base64,<?= base64_encode($user['profile_image']) ?>" alt="Profile Picture">
                </div>
                <form action="php/updatePicture.php" method="post" enctype="multipart/form-data" class="mt-2">
                    <input type="file" name="profile_picture" class="form-control mb-2">
                    <button type="submit" class="btn btn-outline-primary w-100">Update Picture</button>
                </form>
            </div>
            <div class="col-md-8">
                <form action="php/updateProfile.php" method="post">
                    <div class="mb-3">
                        <label>Name:</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label>Email:</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
                    </div>
                    <div class="mb-3">
                        <label>Password:</label>
                        <input type="password" name="password" class="form-control" value="<?= htmlspecialchars($user['password']) ?>">
                    </div>
                    <div class="mb-3">
                        <label>Bio:</label>
                        <textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($user['bio']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-dark">Save Changes</button>
                </form>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mt-5" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">My Blogs</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="likes-tab" data-bs-toggle="tab" data-bs-target="#likes" type="button" role="tab">My Likes</button>
        </li>
    </ul>

    <div class="tab-content pt-3" id="dashboardTabsContent">
        <div class="tab-pane fade show active" id="posts" role="tabpanel">
            <div class="row">
                <?php if ($posts->num_rows > 0): ?>
                    <?php while ($post = $posts->fetch_assoc()): ?>
                        <div class="col-md-6 mb-3">
                            <div class="blog-post">
                                <h5><?= htmlspecialchars($post['title'] ?? 'Untitled') ?></h5>
                                <p><?= htmlspecialchars($post['description'] ?? '') ?></p>
                                <small class="text-muted">Posted on <?= date("F j, Y, g:i a", strtotime($post['created_at'])) ?></small><br>
                                <a href="blogPost.php?post_id=<?= (int)$post['post_id'] ?>" class="btn btn-sm btn-outline-primary mt-2">Read More</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No blog posts yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="tab-pane fade" id="likes" role="tabpanel">
            <div class="row">
                <?php if ($likedPosts->num_rows > 0): ?>
                    <?php while ($like = $likedPosts->fetch_assoc()): ?>
                        <div class="col-md-6 mb-3">
                            <div class="liked-post">
                                <h5><?= htmlspecialchars($like['title'] ?? 'Untitled') ?></h5>
                                <p><?= htmlspecialchars($like['description'] ?? '') ?></p>
                                <small class="text-muted">Posted on <?= date("F j, Y, g:i a", strtotime($like['created_at'])) ?></small><br>
                                <a href="blogPost.php?post_id=<?= (int)$like['post_id'] ?>" class="btn btn-sm btn-outline-primary mt-2">Read More</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>You haven't liked any posts yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/navbarAuth.js"></script>
</body>
</html>

<?php $conn->close(); ?>
