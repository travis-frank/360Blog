<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "360BlogDB";

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4"); // Ensure UTF-8 compatibility
} catch (Exception $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("Database connection failed.");
}
?>
