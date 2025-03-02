<?php
// Include your database connection file
include 'smf_db_conn.php'; // Update with your actual database connection file

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve appointment ID and remark from the POST request
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;
    $remark = isset($_POST['remark']) ? trim($_POST['remark']) : '';

    // Validate input
    if ($appointment_id > 0 && !empty($remark)) {
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("UPDATE appointments SET remark = ? WHERE appointment_id = ?");
        
        if ($stmt) {
            // Bind parameters ("si" means string for remark and integer for appointment_id)
            $stmt->bind_param("si", $remark, $appointment_id);
            
            // Execute the query
            if ($stmt->execute()) {
                // Redirect to the previous page with a success message
                header("Location: previous_page.php?message=Remark updated successfully."); // Change to your page
                exit();
            } else {
                // Display error message if the update fails
                echo "Error updating remark: " . htmlspecialchars($stmt->error);
            }

            // Close the prepared statement
            $stmt->close();
        } else {
            // Handle statement preparation error
            echo "Error preparing statement: " . htmlspecialchars($conn->error);
        }
    } else {
        // Invalid input message
        echo "Invalid appointment ID or remark.";
    }
}

// Close the database connection
$conn->close();
?>
