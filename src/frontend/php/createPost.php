<?php
session_start();
include 'DBConnect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if image was uploaded
if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
    $_SESSION['error'] = "You must upload an image to create a post.";
    header("Location: ../createPost.php");
    exit();
}

// Validate and get fields
$title = $_POST['title'] ?? '';
$category = $_POST['category'] ?? '';
$content = $_POST['content'] ?? '';
$user_id = $_SESSION['user_id'];

// Read image binary
$imageData = file_get_contents($_FILES['image']['tmp_name']);

// Prepare insert
$stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, category, banner_image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isssb", $user_id, $title, $content, $category, $null);
$stmt->send_long_data(4, $imageData);

if ($stmt->execute()) {
    header("Location: ../feed.php");
} else {
    $_SESSION['error'] = "Failed to create post. Please try again.";
    header("Location: ../createPost.php");
}

$stmt->close();
$conn->close();
