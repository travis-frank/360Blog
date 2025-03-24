<?php
session_start();
include('DBConnect.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id'])) {
    header("Location: ../frontPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$post_id = (int)$_POST['post_id'];

// Check if the like already exists
$check = $conn->prepare("SELECT like_id FROM likes WHERE user_id = ? AND post_id = ?");
$check->bind_param("ii", $user_id, $post_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Unlike: delete it
    $delete = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $delete->bind_param("ii", $user_id, $post_id);
    $delete->execute();
    $delete->close();
} else {
    // Like: insert it
    $insert = $conn->prepare("INSERT INTO likes (user_id, post_id, created_at) VALUES (?, ?, NOW())");
    $insert->bind_param("ii", $user_id, $post_id);
    $insert->execute();
    $insert->close();
}

$check->close();
$conn->close();

header("Location: ../blogPost.php?post_id=" . $post_id);
exit();
