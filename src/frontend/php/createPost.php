<?php
include('DBConnect.php');
$user_id = 1;
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $title = $conn ->real_escape_string($_POST["title"]);
    $category = $conn ->real_escape_string($_POST["category"]);
    $content = $conn ->real_escape_string($_POST["content"]);

    $imagePath = null;
    // Handle image upload
    $image_id = NULL; // Default NULL if no image is uploaded
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/"; // Ensure this directory exists
        $image_name = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name; // Unique name

        // Check if image is valid
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $valid_formats = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($imageFileType, $valid_formats)) {
            die("Error: Only JPG, JPEG, PNG & GIF files allowed.");
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Insert image reference in `images` table
            $stmt = $conn->prepare("INSERT INTO images (image_path) VALUES (?)");
            $stmt->bind_param("s", $target_file);
            if ($stmt->execute()) {
                $image_id = $stmt->insert_id; // Get inserted image ID
            }
            $stmt->close();
        }
    }


    $stmt = $conn -> prepare("INSERT INTO posts (user_id, title, content) VALUES(?,?,?)");
    $stmt->bind_param("iss", $user_id,  $title, $content);
    if ($stmt->execute()) {
        header("Location: ../blogPost.php?postid=" . $stmt-> insert_id);
        exit();
    } else {
        echo "Error: Unable to create post.";
    }
    $stmt->close();
}
$conn->close();


?>