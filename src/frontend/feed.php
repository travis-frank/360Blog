<?php
include('php/DBConnect.php');
session_start(); 

$filter = isset($_GET['topic']) ? $_GET['topic'] : null;

if ($filter) {
    $stmt = $conn->prepare("
        SELECT p.post_id, p.title, p.content, p.created_at, p.user_id, p.banner_image, u.name
        FROM posts p
        JOIN users u ON p.user_id = u.user_id
        WHERE p.category = ? AND p.is_deleted = 0
        ORDER BY p.created_at DESC
    ");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT p.post_id, p.title, p.content, p.created_at, p.user_id, p.banner_image, u.name 
            FROM posts p 
            JOIN users u ON p.user_id = u.user_id 
            WHERE p.is_deleted = 0 
            ORDER BY p.created_at DESC";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Feed</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="styles/frontPage.css" />
  <link rel="stylesheet" href="styles/feed.css" />
  <link rel="stylesheet" href="styles/nav.css" />
</head>
<body>

<!-- Navbar -->
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

<!-- Filter Dropdown -->
<div class="container mt-4">
  <div class="d-flex justify-content-end mb-3">
    <form method="GET" class="d-flex align-items-center">
      <select name="topic" id="topic" onchange="this.form.submit()" class="form-select w-auto">
        <option value="">All</option>
        <?php
        $topicQuery = "SELECT DISTINCT topic FROM topics";
        $topicResult = $conn->query($topicQuery);
        while ($row = $topicResult->fetch_assoc()):
          $t = htmlspecialchars($row['topic']);
          $selected = ($t === $filter) ? 'selected' : '';
        ?>
          <option value="<?= $t ?>" <?= $selected ?>><?= $t ?></option>
        <?php endwhile; ?>
      </select>
    </form>
  </div>

  <!-- Post List -->
  <?php if ($result && $result->num_rows > 0): ?>
    <div class="row row-cols-1 g-4">
      <?php while ($post = $result->fetch_assoc()): ?>
        <div class="col">
          <div class="card shadow-sm h-100 d-flex flex-row">
            <?php if (!empty($post['banner_image'])): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($post['banner_image']); ?>" class="post-image" alt="Blog Banner">
            <?php else: ?>
                <img src="images/default-banner.jpg" class="post-image" alt="Default Banner">
            <?php endif; ?>
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
              <h6 class="card-subtitle mb-2 text-muted">
                Posted on <?= date('F j, Y', strtotime($post['created_at'])) ?> 
                by <?= htmlspecialchars($post['name']) ?>
              </h6>
              <p class="card-text"><?= nl2br(htmlspecialchars(substr($post['content'], 0, 150))) ?>...</p>
              <a href="blogPost.php?post_id=<?= $post['post_id'] ?>" class="btn btn-outline-primary">Read more</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info bg-white text-center w-100 mt-5 p-4 rounded border border-dark shadow-sm" role="alert">
      <h4 class="mb-2">No posts yet in this category</h4>
      <p>Be the first to <a href="createPost.php" class="alert-link">create a post</a> in this topic!</p>
    </div>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/feed.js"></script>
</body>
</html>

<?php $conn->close(); ?>
