<?php
include('php/DBConnect.php');

$sql = "SELECT post_id, title, content, created_at, user_id FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/frontPage.css">
    <link rel="stylesheet" href="styles/feed.css">
    <link rel="stylesheet" href="styles/nav.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
        <img src="../../Images/logo.png" alt="Logo" class="navbar-brand">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="feed.php">Feed</a></li>
                <li class="nav-item"><a class="nav-link" href="frontPage.html">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="userDash.html">Profile</a></li>
                <li class="nav-item"><a class="nav-link" href="createPost.html">Create Post</a></li>
            </ul>
            <form class="d-flex">
                <input type="text" class="form-control search-bar" placeholder="Search...">
            </form>
        </div>
    </nav>

    <main>
        <div class="post-list">
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="post-item">
                    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars(substr($post['content'], 0, 150))) . '...'; ?></p>
                    <a href="blogPost.php?post_id=<?php echo $post['post_id']; ?>">Read more</a>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <script src="js/feed.js"></script>
</body>
</html>

<?php $conn->close(); ?>
