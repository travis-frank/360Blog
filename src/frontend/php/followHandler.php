<?php
session_start();
include('DBConnect.php');

header('Content-Type: text/plain');

if (!isset($_SESSION['user_id']) || !isset($_POST['followed_id'])) {
    http_response_code(400);
    echo "Invalid request.";
    exit();
}

$follower_id = intval($_SESSION['user_id']);
$followed_id = intval($_POST['followed_id']);

// Prevent following yourself
if ($follower_id === $followed_id) {
    echo "You can't follow yourself.";
    exit();
}

// Check if already following
$check = $conn->prepare("SELECT 1 FROM follows WHERE follower_id = ? AND followed_id = ?");
$check->bind_param("ii", $follower_id, $followed_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Already following — unfollow
    $unfollow = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
    $unfollow->bind_param("ii", $follower_id, $followed_id);
    if ($unfollow->execute()) {
        echo "unfollowed";
    } else {
        echo "error";
    }
    $unfollow->close();
} else {
    // Not following — follow
    $follow = $conn->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
    $follow->bind_param("ii", $follower_id, $followed_id);
    if ($follow->execute()) {
        echo "followed";
    } else {
        echo "error";
    }
    $follow->close();
}

$check->close();
$conn->close();
?>
