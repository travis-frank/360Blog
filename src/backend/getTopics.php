<?php
require '../frontend/php/DBConnect.php';

// Get query parameter (or default to empty)
$query = $_GET['query'] ?? '';
$likeQuery = "%$query%";

// Search topics
if ($query) {
    $sql = "SELECT topic FROM topics WHERE topic LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $likeQuery);
} else {
    $sql = "SELECT topic FROM topics";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$topics = [];
while ($row = $result->fetch_assoc()) {
    $topics[] = $row;
}

echo json_encode($topics);
$conn->close();
?>