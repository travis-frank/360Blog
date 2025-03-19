<?php
session_start();
include_once 'DBConnect.php';

// Enable debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password)) {
        $_SESSION['error'] = "All fields are required.";
        header("Location: ../register.html");
        exit();
    }

    // Check if profile image is uploaded
    $imageData = NULL;
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowedFormats = ["jpg", "jpeg", "png", "webp"];
        $imageFileType = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));

        if (!in_array($imageFileType, $allowedFormats)) {
            $_SESSION['error'] = "Invalid image format. Only JPG, PNG, and WEBP are allowed.";
            header("Location: ../register.html");
            exit();
        }

        // Read image as binary
        $imageData = file_get_contents($_FILES['profile_image']['tmp_name']); 

        // Debugging: Log if file reading fails
        if ($imageData === false) {
            error_log("Error reading uploaded image file.");
            $_SESSION['error'] = "Error reading image file.";
            header("Location: ../register.html");
            exit();
        }
    } else {
        error_log("No valid file uploaded. Error Code: " . ($_FILES['profile_image']['error'] ?? 'No file uploaded.'));
    }

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, profile_image, role, is_active, created_at) 
    VALUES (?, ?, ?, ?, 'user', 1, NOW())");

    if (!$stmt) {
    die("SQL Prepare Failed: " . $conn->error);
    }

    if ($imageData === NULL) {
    $stmt->bind_param("sss", $name, $email, $password);
    } else {
    // Bind parameters without image first
    $stmt->bind_param("sssb", $name, $email, $password, $null);

    // Send image data separately
    $stmt->send_long_data(3, $imageData);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Registration successful! Please log in.";
        header("Location: ../userDash.php");
        exit();
    } else {
        $_SESSION['error'] = "Database error: " . $stmt->error;
        header("Location: ../register.html");
        exit();
    }

    $stmt->close();
    $conn->close();

}
?>
