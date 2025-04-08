<?php
require '../frontend/php/DBConnect.php';

// Get query parameter (or default to empty)
$query = $_GET['query'] ?? '';
$likeQuery = "%$query%";

// Search user
if ($query) {
    $sql = "SELECT user_id, name, email, role, is_active FROM users WHERE name LIKE ? OR email LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $likeQuery, $likeQuery);
} else {
    $sql = "SELECT user_id, name, email, role, is_active FROM users";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

echo json_encode($users);
$conn->close();
?>