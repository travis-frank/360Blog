<?php
require '../frontend/php/DBConnect.php';

// Retrieve the user_id from POST
$user_id = $_POST['user_id'] ?? 0;

// Check current is_active status
$sql = "SELECT is_active FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo json_encode(["success" => false, "error" => "User not found"]);
    exit;
}

// Toggle from 1 to 0 or 0 to 1
$currentActive = $row['is_active'];
$newActive = ($currentActive == 1) ? 0 : 1;

$sql = "UPDATE users SET is_active = ? WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $newActive, $user_id);
$stmt->execute();

echo json_encode(["success" => $stmt->affected_rows > 0]);
$conn->close();
?>