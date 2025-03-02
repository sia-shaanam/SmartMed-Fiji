<?php
include('smf_db_conn.php'); // Include your database connection file

// Check if the necessary data is received via POST
if (isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];  // Get the appointment ID from the AJAX request
    $new_status = $_POST['status'];  // Get the new status from the AJAX request

    // Debug: Check if the values are received properly
    if (empty($appointment_id) || empty($new_status)) {
        die('Error: Appointment ID or Status is empty.');
    }

    // Ensure appointment_id is an integer (to prevent SQL injection or malformed input)
    $appointment_id = intval($appointment_id);

    // Check if appointment_id is valid (greater than 0)
    if ($appointment_id <= 0) {
        die('Error: Invalid Appointment ID.');
    }

    // Prepare the SQL query to update the status only for the selected appointment_id
    $sql = "UPDATE appointments SET status = ? WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);  // Prepare the statement

    if ($stmt) {
        // Bind the parameters: 's' for string (status), 'i' for integer (appointment_id)
        $stmt->bind_param("si", $new_status, $appointment_id);

        // Execute the query
        if ($stmt->execute()) {
            // Check if the row was updated
            if ($stmt->affected_rows > 0) {
                echo 'Status updated successfully for appointment ID: ' . $appointment_id;
            } else {
                echo 'Error: No appointment found with the given ID or status is already set to the same value.';
            }
        } else {
            // Debug: Show the exact error from the database
            echo 'Error: ' . $stmt->error;
        }

      }}
?>