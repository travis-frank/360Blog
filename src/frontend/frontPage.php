<?php
session_start();
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Front Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/nav.css">
    <link rel="stylesheet" href="styles/frontPage.css">
</head>

<body>
    <!-- Navbar -->
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


<main>
<?php
include_once("php/DBConnect.php");

// Fetch the 4 most recent posts
$query = "SELECT * FROM posts WHERE is_deleted = 0 ORDER BY created_at DESC LIMIT 4";
$result = $conn->query($query);

$posts = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}
?>

<!-- Featured Post -->
<?php if (!empty($posts)): ?>
    <?php $featured = $posts[0]; ?>
    <section class="featured">
        <h2><strong>Featured Post</strong></h2>
        <div class="profile-container">
            <div class="profile-picture">
                <img src="data:image/jpeg;base64,<?= base64_encode($featured['banner_image']) ?>" alt="Banner Image">
            </div>
            <div class="featured-info">
                <h3><?= htmlspecialchars($featured['title']) ?></h3>
                <p><?= htmlspecialchars(substr($featured['content'], 0, 100)) ?>...</p>
                <a href="blogPost.php?post_id=<?= htmlspecialchars($featured['post_id']) ?>">Read more</a>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Popular Posts -->
<?php if (count($posts) > 1): ?>
    <section class="popular">
        <h2><strong>Popular Posts</strong></h2>
        <div class="post-list">
            <?php for ($i = 1; $i < count($posts); $i++): ?>
                <div class="post-item">
                    <div class="profile-picture">
                        <img src="data:image/jpeg;base64,<?= base64_encode($posts[$i]['banner_image']) ?>" alt="Post <?= $i ?>">
                    </div>
                    <h3><?= htmlspecialchars($posts[$i]['title']) ?></h3>
                    <p><?= htmlspecialchars(substr($posts[$i]['content'], 0, 100)) ?>...</p>
                    <a href="blogPost.php?post_id=<?= htmlspecialchars($posts[$i]['post_id']) ?>">Read More</a>
                </div>
            <?php endfor; ?>
        </div>
    </section>
<?php endif; ?>

<!-- Categories (unchanged) -->
<section class="categories">
    <h2><strong>Categories</strong></h2>
    <ul>
        <li><a href="#">Technology</a></li>
        <li><a href="#">Lifestyle</a></li>
        <li><a href="#">Travel</a></li>
        <li><a href="#">Food</a></li>
        <li><a href="#">Health</a></li>
        <li><a href="#">Sports</a></li>
        <li><a href="#">Music</a></li>
        <li><a href="#">Film</a></li>
        <li><a href='#'>Gaming</a></li>
        <li><a href='#'>Fashion</a></li>
        <li><a href='#'>Literature</a></li>
        <li><a href='#'>Manga/Anime</a></li>
    </ul>
</section>

</main>





</body>



</html>