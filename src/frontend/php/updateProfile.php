<?php
session_start();
include_once 'DBConnect.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $bio = trim($_POST['bio']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "Name, email, and password are required.";
        header("Location: ../userDash.php");
        exit();
    }

    // Prepare SQL query
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password_hash = ?, bio = ? WHERE user_id = ?");
    if (!$stmt) {
        die("SQL Prepare Failed: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $name, $email, $password, $bio, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Profile updated successfully!";
        header("Location: ../userDash.php");
        exit();
    } else {
        $_SESSION['error'] = "Database error: " . $stmt->error;
        header("Location: ../userDash.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
