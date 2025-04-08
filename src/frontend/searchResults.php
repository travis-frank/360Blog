<?php
session_start();
include('php/DBConnect.php');

$searchQuery = trim($_GET['query'] ?? '');

if (empty($searchQuery)) {
    echo "<h2>No search term provided.</h2>";
    exit();
}

$searchTerm = '%' . $searchQuery . '%';

// Search blog posts
$post_stmt = $conn->prepare("SELECT posts.post_id, posts.title, posts.content, users.name AS author, users.user_id AS author_id
                             FROM posts 
                             JOIN users ON posts.user_id = users.user_id 
                             WHERE posts.title LIKE ? OR posts.content LIKE ?");
$post_stmt->bind_param("ss", $searchTerm, $searchTerm);
$post_stmt->execute();
$post_results = $post_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$post_stmt->close();

// Search users
$user_stmt = $conn->prepare("SELECT user_id, name FROM users WHERE name LIKE ?");
$user_stmt->bind_param("s", $searchTerm);
$user_stmt->execute();
$user_results = $user_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$user_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results for "<?= htmlspecialchars($searchQuery) ?>"</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/searchResults.css">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="icon" type="image/png" href="../../Images/logo.png" />
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <img src="../../Images/logo.png" alt="Logo" class="navbar-brand">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="frontPage.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="feed.php">Feed</a></li>
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

    <div class="search-results-page" style="background-image: url('../../Images/background.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat; min-height: 100vh;">
        <div class="container search-results-container">
            <h2 class="search-header">Search Results for "<em><?= htmlspecialchars($searchQuery) ?></em>"</h2>

            <div class="result-section">
                <h4>Blog Posts</h4>
                <?php if (empty($post_results)): ?>
                    <p class="no-results">No matching blog posts found.</p>
                <?php else: ?>
                    <?php foreach ($post_results as $post): ?>
                        <div class="result-card">
                            <h5>
                                <a href="blogPost.php?post_id=<?= $post['post_id'] ?>">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h5>
                            <p class="result-author">
                                by <a href="userProfile.php?user_id=<?= $post['author_id'] ?>"><?= htmlspecialchars($post['author']) ?></a>
                            </p>
                            <p><?= htmlspecialchars(mb_strimwidth($post['content'], 0, 150, '...')) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="result-section">
                <h4>Users</h4>
                <?php if (empty($user_results)): ?>
                    <p class="no-results">No users found.</p>
                <?php else: ?>
                    <?php foreach ($user_results as $user): ?>
                        <div class="result-card">
                            <h5>
                                <a href="userProfile.php?user_id=<?= $user['user_id'] ?>">
                                    <?= htmlspecialchars($user['name']) ?>
                                </a>
                            </h5>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
