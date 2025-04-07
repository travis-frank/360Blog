<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect if not logged in
        exit();
    }

include 'php/DBConnect.php';
$topics = [];
$query = "SELECT topic FROM topics ORDER BY topic ASC";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $topics[] = $row['topic'];
    }
}
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/createPost.css">
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

    <div class="page-container">
        <h2>Create a New Blog Post</h2>
        <form action="./php/createPost.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Post Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-control" id="category" name="category" required>
                <?php if (count($topics) > 0): ?>
                <?php foreach ($topics as $topic): ?>
                <option value="<?= htmlspecialchars($topic) ?>"><?= htmlspecialchars($topic) ?></option>
                <?php endforeach; ?>
                <?php else: ?>
                 <option disabled>No topics found</option>
                <?php endif; ?>
            </select>
            </div>
            <div class="mb-3">
                <label for="content" class="form-label">Post Content</label>
                <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Upload Image</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Publish Post</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
