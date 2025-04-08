<?php
require '../frontend/php/DBConnect.php';

// Check if the request is to fetch posts
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get query parameter (or default to empty)
    $query = $_GET['query'] ?? '';
    $likeQuery = "%$query%";

    // Search posts
    if ($query) {
        $sql = "SELECT posts.post_id, posts.title, posts.created_at, users.name AS author, posts.is_deleted 
                FROM posts 
                JOIN users ON posts.user_id = users.user_id 
                WHERE posts.title LIKE ? OR posts.content LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $likeQuery, $likeQuery);
    } else {
        $sql = "SELECT posts.post_id, posts.title, posts.created_at, users.name AS author, posts.is_deleted 
                FROM posts 
                JOIN users ON posts.user_id = users.user_id";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    echo json_encode($posts);
    $conn->close();
    exit;
}

// Handle post deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['postId'];
    $action = $_POST['action']; // 'edit' or 'delete'

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        echo "Post deleted.";
    } else if ($action === 'edit') {
        $newContent = $_POST['content'];
        $stmt = $conn->prepare("UPDATE posts SET content = ? WHERE post_id = ?");
        $stmt->bind_param("si", $newContent, $postId);
        $newContent = htmlspecialchars($newContent);
        $stmt->execute();
        echo "Post updated successfully";
    }
}
?>
