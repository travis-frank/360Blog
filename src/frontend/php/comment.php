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
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $content = isset($_POST["content"]) ? trim($_POST["content"]) : '';

    if (empty($content)) {
        die("Comment content cannot be empty.");
    }

    $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, content, created_at, is_deleted) VALUES (?, ?, ?, NOW(), 0)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("iis", $post_id, $user_id, $content);
    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }
    $stmt->close();
}
$conn->close();
header("Location: ../blogPost.php?post_id=" . $post_id);
exit();
?>