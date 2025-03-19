<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "360BlogDB";

// Create a connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

$conn->select_db($database);

$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        user_id       INT AUTO_INCREMENT PRIMARY KEY,
        name          VARCHAR(100) NOT NULL,
        email         VARCHAR(255) UNIQUE NOT NULL,
        password      VARCHAR(255) NOT NULL,
        profile_image LONGBLOB DEFAULT NULL,
        bio           TEXT NOT NULL DEFAULT 'No bio',
        role          ENUM('user', 'admin') DEFAULT 'user',
        is_active     BOOLEAN DEFAULT TRUE,
        created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS posts (
        post_id      INT AUTO_INCREMENT PRIMARY KEY,
        user_id      INT NOT NULL,
        banner_image LONGBLOB DEFAULT NULL,
        title        VARCHAR(255) NOT NULL,
        description  TEXT DEFAULT NULL,
        content      TEXT NOT NULL,
        created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        is_deleted   BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS comments (
        comment_id   INT AUTO_INCREMENT PRIMARY KEY,
        post_id      INT NOT NULL,
        user_id      INT NOT NULL,
        content      TEXT NOT NULL,
        created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_deleted   BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS sessions (
        session_id    VARCHAR(255) PRIMARY KEY,
        user_id       INT NOT NULL,
        created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS admin_actions (
        action_id    INT AUTO_INCREMENT PRIMARY KEY,
        admin_id     INT NOT NULL,
        action_type  ENUM('delete_post', 'delete_comment', 'ban_user') NOT NULL,
        target_id    INT NOT NULL,
        target_type  ENUM('post', 'comment', 'user') NOT NULL,
        created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS likes (
        like_id    INT AUTO_INCREMENT PRIMARY KEY,
        user_id    INT NOT NULL,
        post_id    INT NULL,
        comment_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
        FOREIGN KEY (comment_id) REFERENCES comments(comment_id) ON DELETE CASCADE,
        UNIQUE (user_id, post_id, comment_id)
    )"
];

// Execute each table creation query
foreach ($tables as $query) {
    if ($conn->query($query) === TRUE) {
        echo "Table created successfully.<br>";
    } else {
        echo "Error creating table: " . $conn->error . "<br>";
    }
}
// Close the connection
$conn->close();

?>