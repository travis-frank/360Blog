<?php
session_start();
include_once 'DBConnect.php'; // Ensure database connection

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: ../login.php");
        exit();
    }

    // Debugging: Log user input
    error_log("Login Attempt: Email = " . $email);

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT user_id, name, email, password, role, is_active FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        error_log("Query preparation failed: " . $conn->error);
        die("Query failed.");
    }

    $stmt = $conn->prepare("SELECT user_id, name, email, password, role, is_active FROM users WHERE email = ? LIMIT 1");
    if (!$stmt) {
        error_log("Query preparation failed: " . $conn->error);
        die("Query failed.");
    } 
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        error_log("User found: " . json_encode($user));

        if (!$user['is_active']) {
            error_log("User is deactivated.");
            $_SESSION['error'] = "Your account has been deactivated.";
            header("Location: ../login.php");
            exit();
        }

        // Check password (Ensure it's hashed in DB)
        if ($password === $user['password']) {
            error_log("Password matched. Logging in user: " . $user['email']);

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;

            error_log("Session Data: " . json_encode($_SESSION));
            header("Location: ../userDash.php");
            exit();
        } else {
            error_log("Password did NOT match.");
            $_SESSION['error'] = "Invalid email or password.";
            header("Location: ../login.php");
            exit();
        }
    } else {
        error_log("User not found in database.");
        $_SESSION['error'] = "Invalid email or password. User not found.";
        header("Location: ../login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
