<?php
session_start();
include_once 'DBConnect.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user_id = $_SESSION['user_id'];
    $title = trim($_POST["title"]);
    $category = trim($_POST["category"]);
    $content = trim($_POST["content"]);

    // Validate inputs
    if (empty($title) || empty($category) || empty($content)) {
        echo "All fields are required.";
        exit();
    }

    // Handle image upload
    $imageData = NULL;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedFormats = ["jpg", "jpeg", "png", "gif"];
        $imageFileType = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

        if (!in_array($imageFileType, $allowedFormats)) {
            echo "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
            exit();
        }

        // Read image as binary
        $imageData = file_get_contents($_FILES['image']['tmp_name']); 

        // Debugging: Log if file reading fails
        if ($imageData === false) {
            error_log("Error reading uploaded image file.");
            echo "Error reading image file.";
            exit();
        }
    }

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO posts (user_id, title, category, content, banner_image) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("SQL Prepare Failed: " . $conn->error);
        die("SQL Prepare Failed: " . $conn->error);
    }

    if ($imageData === NULL) {
        $stmt->bind_param("isss", $user_id, $title, $category, $content);
    } else {
        // Bind parameters without image first
        $stmt->bind_param("isssb", $user_id, $title, $category, $content, $null);

        // Send image data separately
        $stmt->send_long_data(4, $imageData);
    }

    if ($stmt->execute()) {
        header("Location: ../blogPost.php?post_id=" . $stmt->insert_id);

        exit();
    } else {
        error_log("Execute Failed: " . $stmt->error);
        echo "Error: Unable to create post.";
    }

    $stmt->close();
}
$conn->close();
?>