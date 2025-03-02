<?php
// Include the database connection
include('smf_db_conn.php');

// Check if form data is submitted
if (isset($_POST['email'], $_POST['current_password'], $_POST['new_password'])) {
    // Fetch and sanitize form data
    $email = $_POST['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Check if the user exists and verify the current password
    $query = "SELECT password FROM login WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If the user exists, proceed
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($stored_password);
        $stmt->fetch();

        // Verify the current password
        if (password_verify($current_password, $stored_password)) {
            // Hash the new password
            $hashed_new_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Update the password
            $update_query = "UPDATE login SET password = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ss", $hashed_new_password, $email);

            if ($update_stmt->execute()) {
                echo "Password successfully updated!";
            } else {
                echo "Error updating password. Please try again.";
            }
        } else {
            echo "Current password is incorrect.";
        }
    } else {
        echo "User not found.";
    }

    // Close statements
    $stmt->close();
    $update_stmt->close();
} else {
    echo "Please fill in all fields.";
}

// Close the database connection
$conn->close();
?>
