<?php
include('php/DBConnect.php');

$post_id = 3;

if ($post_id) {
    // Fetch post details including the image path
    $stmt = $conn->prepare("SELECT posts.title, posts.content, posts.created_at, users.name AS author
                            FROM posts 
                            JOIN users ON posts.user_id = users.user_id 
                            WHERE posts.post_id = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    $stmt->close();

    // Check if post exists
    if (!$post) {
        echo "<h2>Post not found.</h2>";
        exit();
    }
} else {
    echo "<h2>Invalid request: No post ID provided.</h2>";
    exit();
}

$conn->close();include('php/DBConnect.php');

$post_id = $_GET['post_id'] ?? null;

if (!$post_id) {
    echo "<h2>Invalid request: No post ID provided.</h2>";
    exit();
}

$stmt = $conn->prepare("SELECT posts.title, posts.content, posts.created_at, users.name AS author 
                        FROM posts 
                        JOIN users ON posts.user_id = users.user_id 
                        WHERE posts.post_id = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$post) {
    echo "<h2>Post not found.</h2>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/blogPost.css">
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
                <li class="nav-item">
                    <a class="nav-link" href="feed.php">Feed</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="frontPage.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="userDash.html">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="createPost.html">Create Post</a>
                </li>
            </ul>
            <form class="d-flex">
                <input type="text" class="form-control search-bar" placeholder="Search...">
            </form>
        </div>
    </nav>

    <div class="blog-container">
        <h1 class="blog-title"><?php echo htmlspecialchars($post['title']); ?></h1>
        <p class="blog-meta">
            By <strong><?php echo htmlspecialchars($post['author']); ?></strong>
            <img src="../../Images/person.svg" class="rounded-circle ms-2" alt="User Profile Picture" width="30" height="30">
            <button class="btn btn-sm btn-primary ms-2">Follow</button>
        </p>
        <p class="blog-meta">Published on <strong><?php echo date("F j, Y", strtotime($post['created_at'])); ?></strong></p>

        <!-- Display Image if Exists -->
        <?php if (!empty($post['image_path'])): ?>
            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" class="img-fluid my-4" alt="Blog Banner">
        <?php endif; ?>

        <hr>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    </div>

    <div class="comment-container">
        <h3>Comments</h3>
        <div class="comment-section">
            <form class="mb-4">
                <div class="mb-3">
                    <textarea class="form-control" id="commentText" rows="3" placeholder="Your comment"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div class="comments-list">
                <div class="comment mb-3">
                    <div class="d-flex">
                        <strong class="me-2">John Doe</strong>
                        <span class="text-muted">March 1, 2025</span>
                    </div>
                    <p>This is a sample comment. Great post!</p>
                </div>
                <div class="comment mb-3">
                    <div class="d-flex">
                        <strong class="me-2">Jane Smith</strong>
                        <span class="text-muted">March 2, 2025</span>
                    </div>
                    <p>Very informative. Thanks for sharing!</p>
                </div>
            </div>
        </div>
    </div>

    <div class="related-articles mt-4">
        <h3>Related Articles</h3>
        <ul class="list-group">
            <li class="list-group-item">
                <a href="#">Article #1</a>
            </li>
            <li class="list-group-item">
                <a href="#">Article #2</a>
            </li>
            <li class="list-group-item">
                <a href="#">Article #3</a>
            </li>
            <li class="list-group-item">
                <a href="#">Article #4</a>
            </li>
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
