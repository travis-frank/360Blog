<?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php"); // Redirect if not logged in
        exit();
    }

    // Enable error reporting
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    include_once 'php/DBConnect.php';

    $user_id = $_SESSION['user_id'];
    $query = $conn->prepare("SELECT name, email, bio, password_hash, profile_image FROM users WHERE user_id = ?");
    $query->bind_param("i", $user_id);
    $query->execute();
    $result = $query->get_result();
    $user = $result->fetch_assoc();

    // Fetch user blog posts
    $postQuery = $conn->prepare("SELECT user_id, title, description FROM posts WHERE user_id = ?");
    $postQuery->bind_param("i", $user_id);
    $postQuery->execute();
    $posts = $postQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/userDash.css">
    <link rel="stylesheet" href="styles/nav.css">
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
            </ul>
            <a class="btn btn-danger" href="logout.php">Logout</a>
            <form class="d-flex">
                <input type="text" class="form-control search-bar" placeholder="Search...">
            </form>
        </div>
    </nav>

    <!-- Dashboard Container -->
    <div class="page-container">
        <div class="dashboard-container mt-4">
            <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
            <p><strong>User Dashboard</strong></p>
            
            <!-- Profile Section -->
            <div class="d-flex align-items-start">
                <div class="position-relative">
                    <div class="profile-picture">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($user['profile_image']); ?>" alt="Profile Picture">
                    </div>
                    <!-- Form to upload new profile picture -->
                    <form action="php/updatePicture.php" method="post" enctype="multipart/form-data" class="mt-2 profile-picture-form">
                        <div class="mb-2">
                            <label for="profile_picture">Upload New Profile Picture:</label>
                            <input type="file" name="profile_picture" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Picture</button>
                    </form>
                </div>
                <div class="ms-4">
                    <form action="php/updateProfile.php" method="post" enctype="multipart/form-data">
                        <div class="mb-2">
                            <label>Name:</label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>">
                        </div>
                        <div class="mb-2">
                            <label>Email:</label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="mb-2">
                            <label>Password:</label>
                            <input type="password" name="password" class="form-control" value="<?php echo htmlspecialchars($user['password_hash']); ?>">
                        </div>
                        <div class="mb-2">
                            <label>Bio:</label>
                            <textarea name="bio" class="form-control"><?php echo htmlspecialchars($user['bio']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary">Save Changes</button>
                    </form>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <h4>My Blogs:</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="blog-post">
                                <img src="../../Images/pencil-square.svg" alt="Blog Post">
                                <h5>BLOG POST TITLE</h5>
                                <p>Short description of the blog post...</p>
                                <a href="#">Read More</a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="blog-post">
                                <img src="../../Images/pencil-square.svg" alt="Blog Post">
                                <h5>BLOG POST TITLE</h5>
                                <p>Short description of the blog post...</p>
                                <a href="#">Read More</a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="blog-post">
                                <img src="../../Images/pencil-square.svg" alt="Blog Post">
                                <h5>BLOG POST TITLE</h5>
                                <p>Short description of the blog post...</p>
                                <a href="#">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h4>My Likes:</h4>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="liked-post">
                                <img src="../../Images/heart.png" alt="Liked Post">
                                <h5>BLOG POST TITLE</h5>
                                <p>Short description of the blog post...</p>
                                <a href="#">Read More</a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="liked-post">
                                <img src="../../Images/heart.png" alt="Liked Post">
                                <h5>BLOG POST TITLE</h5>
                                <p>Short description of the blog post...</p>
                                <a href="#">Read More</a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="liked-post">
                                <img src="../../Images/heart.png" alt="Liked Post">
                                <h5>BLOG POST TITLE</h5>
                                <p>Short description of the blog post...</p>
                                <a href="#">Read More</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/navbarAuth.js"></script>
</body>
</html>

<?php
$conn->close();
?>