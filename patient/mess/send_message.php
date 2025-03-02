<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'smf_db.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect data from POST request
$patient_id = $_POST['patient_id'];
$user_id = $_POST['user_id'];
$message = $_POST['message'];

// Insert new message into the database
$sql = "INSERT INTO messages (patient_id, user_id, message) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $patient_id, $user_id, $message);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
