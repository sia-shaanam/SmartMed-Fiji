<?php
session_start();
include('smf_db_conn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['doctor_image'])) {
    $doctor_id = $_POST['doctor_id'];
    $imageFile = $_FILES['doctor_image'];
    
    // Check if the file is an image and handle the upload
    if ($imageFile['error'] === UPLOAD_ERR_OK) {
        $targetDir = 'doctor_images/'; // Ensure this path is correct
        $targetFile = $targetDir . basename($imageFile['name']);
        
        // Ensure the directory exists
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true); // Create directory if it doesn't exist
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($imageFile['tmp_name'], $targetFile)) {
            // Store the path in the database
            $sql = "UPDATE doctors SET image_url = ? WHERE doctor_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $targetFile, $doctor_id);
            if ($stmt->execute()) {
                // Redirect to the doc_list.php after success
                header('Location: doc_list.php');
                exit(); // Ensure no further code is executed
            } else {
                echo "Error saving image path: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error uploading the image.";
        }
    } else {
        echo "Error with the file upload.";
    }
}

$conn->close();
?>
