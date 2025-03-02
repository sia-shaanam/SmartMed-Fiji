<?php
// Connect to the database
$connection = new mysqli("localhost", "root", "", "smf_db");

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the telemedicine ID and new status from the form
    $telemedicine_id = $_POST['telemedicine_id'];
    $new_status = $_POST['new_status'];

    // Prepare and bind the statement
    $stmt = $connection->prepare("UPDATE telemedicine_appointments SET status = ? WHERE telemedicine_id = ?");
    $stmt->bind_param("ss", $new_status, $telemedicine_id);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Status updated successfully!";
    } else {
        echo "Error updating status: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$connection->close();

// Redirect back to the previous page (optional)
header("Location: mass_index.php");
exit();
?>
