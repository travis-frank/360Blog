<?php
require '../frontend/php/DBConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $topicName = $_POST['topic_name'] ?? '';

    if ($topicName) {
        $stmt = $conn->prepare("INSERT INTO topics (topic, created_at) VALUES (?, 0)");
        $stmt->bind_param("s", $topicName);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to add topic"]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Topic name is required"]);
    }
    $conn->close();
}
?>