<?php
session_start(); 
include('php/DBConnect.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$post_id = $_GET['post_id'] ?? null;

if (!$post_id) {
    echo "<h2>Invalid request: No post ID provided.</h2>";
    exit();
}

$stmt = $conn->prepare("SELECT posts.title, posts.content, posts.created_at, users.name AS author, posts.banner_image 
                        FROM posts 
                        JOIN users ON posts.user_id = users.user_id 
                        WHERE posts.post_id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Execute failed: " . $stmt->error);
}
$post = $result->fetch_assoc();
$stmt->close();

$isLiked = false;
if (isset($_SESSION['user_id'])) {
    $likeCheck = $conn->prepare("SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?");
    $likeCheck->bind_param("ii", $_SESSION['user_id'], $post_id);
    $likeCheck->execute();
    $likeCheck->store_result();
    $isLiked = $likeCheck->num_rows > 0;
    $likeCheck->close();
}


if (!$post) {
    echo "<h2>Post not found.</h2>";
    exit();
}

// Fetch comments
$comments_stmt = $conn->prepare("SELECT comments.content, comments.created_at, users.name AS author 
                                 FROM comments 
                                 JOIN users ON comments.user_id = users.user_id 
                                 WHERE comments.post_id = ? AND comments.is_deleted = 0 
                                 ORDER BY comments.created_at DESC");
if (!$comments_stmt) {
    die("Prepare failed: " . $conn->error);
}
$comments_stmt->bind_param("i", $post_id);
$comments_stmt->execute();
$comments_result = $comments_stmt->get_result();
$comments = $comments_result->fetch_all(MYSQLI_ASSOC);
$comments_stmt->close();
$conn->close();
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

            <form method="POST" action="php/handleLike.php" style="display:inline;">
                <input type="hidden" name="post_id" value="<?= $post_id ?>">
                <button type="submit" class="btn btn-sm <?= $isLiked ? 'btn-danger' : 'btn-outline-danger' ?> ms-2">
                    ❤️ <?= $isLiked ? 'Unlike' : 'Like' ?>
                </button>
            </form>
        </p>
        <p class="blog-meta">Published on <strong><?php echo date("F j, Y", strtotime($post['created_at'])); ?></strong></p>

        <?php if (!empty($post['banner_image'])): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($post['banner_image']); ?>" class="img-fluid my-4" alt="Blog Banner">
        <?php endif; ?>

        <hr>
        <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
    </div>

    <div class="comment-container">
        <h3>Comments</h3>
        <div class="comment-section">
            <form class="mb-4" method="post" action="php/comment.php">
                <div class="mb-3">
                    <textarea class="form-control" id="commentText" name="content" rows="3" placeholder="Your comment"></textarea>
                </div>
                <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
            <div class="comments-list">
                <?php foreach ($comments as $comment): ?>
                    <div class="comment mb-3">
                        <div class="d-flex">
                            <strong class="me-2"><?php echo htmlspecialchars($comment['author']); ?></strong>
                            <span class="text-muted"><?php echo date("F j, Y", strtotime($comment['created_at'])); ?></span>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>