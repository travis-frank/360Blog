<?php
session_start();
include('php/DBConnect.php');

if (!isset($_GET['user_id'])) {
    echo "<h2>No user specified.</h2>";
    exit();
}

$user_id = intval($_GET['user_id']);

// Fetch user info
$stmt = $conn->prepare("SELECT user_id, name, profile_image, bio FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "<h2>User not found.</h2>";
    exit();
}

// Fetch user's posts
$post_stmt = $conn->prepare("SELECT post_id, title, content FROM posts WHERE user_id = ? ORDER BY created_at DESC");
$post_stmt->bind_param("i", $user_id);
$post_stmt->execute();
$post_result = $post_stmt->get_result();
$posts = $post_result->fetch_all(MYSQLI_ASSOC);
$post_stmt->close();

// Get followers count
$followersCount = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM follows WHERE followed_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($followersCount);
$stmt->fetch();
$stmt->close();

// Get following count
$followingCount = 0;
$stmt = $conn->prepare("SELECT COUNT(*) FROM follows WHERE follower_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($followingCount);
$stmt->fetch();
$stmt->close();

// Check if viewer is following this user
$isFollowing = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id) {
    $follow_stmt = $conn->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ?");
    $follow_stmt->bind_param("ii", $_SESSION['user_id'], $user_id);
    $follow_stmt->execute();
    $follow_stmt->store_result();
    $isFollowing = $follow_stmt->num_rows > 0;
    $follow_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['name']) ?>'s Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/userProfile.css">
    <link rel="stylesheet" href="styles/nav.css">
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

    <div class="profile-container text-center">
        <div class="profile-picture">
            <img src="data:image/jpeg;base64,<?= base64_encode($user['profile_image']) ?>" alt="Profile Picture">
        </div>
        <h2><?= htmlspecialchars($user['name']) ?></h2>
        <?php if (!empty($user['bio'])): ?>
            <p class="text-muted mt-2"><?= nl2br(htmlspecialchars($user['bio'])); ?></p>
        <?php endif; ?>
        <div class="profile-info d-flex justify-content-center gap-4 mt-3">
            <div class="profile-stats">
                <span><?= count($posts) ?></span>
                Posts
            </div>
            <div class="profile-stats">
                <span><?= $followersCount ?></span>
                Followers
            </div>
            <div class="profile-stats">
                <span><?= $followingCount ?></span>
                Following
            </div>
        </div>

        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != $user_id): ?>
            <form method="POST" action="php/followHandler.php" id="followForm" class="mt-3">
                <input type="hidden" name="followed_id" value="<?= $user_id ?>">
                <button type="submit" class="btn btn-<?= $isFollowing ? 'secondary' : 'primary' ?> follow-btn">
                    <?= $isFollowing ? 'Unfollow' : 'Follow' ?>
                </button>
            </form>
        <?php endif; ?>
    </div>

    <div class="container mt-5">
        <h4 class="mb-4"><?= htmlspecialchars($user['name']) ?>'s Posts</h4>
        <div class="row">
            <?php if (empty($posts)): ?>
                <p>This user hasn’t posted anything yet.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars(mb_strimwidth($post['content'], 0, 100, '...')) ?></p>
                                <a href="blogPost.php?post_id=<?= $post['post_id'] ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('followForm')?.addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = e.target;
        const formData = new FormData(form);

        const res = await fetch(form.action, {
            method: 'POST',
            body: formData
        });

        const result = await res.text();
        const btn = form.querySelector('button');

        if (result === "followed") {
            btn.classList.remove("btn-primary");
            btn.classList.add("btn-secondary");
            btn.innerText = "Unfollow";
        } else if (result === "unfollowed") {
            btn.classList.remove("btn-secondary");
            btn.classList.add("btn-primary");
            btn.innerText = "Follow";
        }
    });
    </script>
</body>
</html>
