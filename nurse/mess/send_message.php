<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

// Include database connection
include 'smf_db_conn.php';  // Assuming you have a connection script

// Get the message and patient ID from POST
$message = $_POST['message'];
$patient_id = $_POST['patient_id'];
$user_id = $_SESSION['user_id'];

// Insert the message into the database
$sql = "INSERT INTO messages (user_id, patient_id, message, sent_at) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $user_id, $patient_id, $message);
$stmt->execute();

// Close the connection
$stmt->close();
$conn->close();
?>
