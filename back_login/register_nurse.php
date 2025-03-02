<?php
// Database connection
include('smf_db_conn.php');

// Get the form data
$nurse_id = $_POST['nurse_id'];
$name = $_POST['name'];
$contact_information = $_POST['contact_information'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO nurses (nurse_id, name, contact_information, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $nurse_id, $name, $contact_information, $password);

// Execute the statement
if ($stmt->execute()) {
    echo "New nurse registered successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close the connection
$stmt->close();
$conn->close();
?>
