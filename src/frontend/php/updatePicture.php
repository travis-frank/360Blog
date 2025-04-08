<?php
session_start();
include_once 'DBConnect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $userId = $_SESSION['user_id'];
    $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
    $fileType = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);

    $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowedTypes)) {
        // Read image as binary
        $imageData = file_get_contents($fileTmpPath);

        // Debugging: Log if file reading fails
        if ($imageData === false) {
            error_log("Error reading uploaded image file.");
            $_SESSION['error'] = "Error reading image file.";
            header('Location: ../userDash.php');
            exit();
        }

        $query = "UPDATE users SET profile_image = ? WHERE user_id = ?";
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters without image first
            $stmt->bind_param("bi", $null, $userId);

            // Send image data separately
            $stmt->send_long_data(0, $imageData);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Profile picture updated successfully.";
            } else {
                $_SESSION['error'] = "Failed to update profile picture in database.";
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Database error: Unable to prepare statement.";
        }
    } else {
        $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
    }
} else {
    $_SESSION['error'] = "No file uploaded.";
}

header('Location: ../userDash.php');
exit();
?>
