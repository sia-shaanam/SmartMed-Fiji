<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

// Include database connection
include 'smf_db_conn.php';  // Assuming you have a connection script

// Get the patient ID from GET
$patient_id = $_GET['patient_id'];
$user_id = $_SESSION['user_id'];

// Retrieve messages for this user and patient
$sql = "SELECT message, sent_at, user_id FROM messages WHERE (user_id = ? AND patient_id = ?) OR (user_id = ? AND patient_id = ?) ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $user_id, $patient_id, $patient_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Display messages
while ($row = $result->fetch_assoc()) {
    // Check if the message was sent by the logged-in user
    $messageClass = ($row['user_id'] == $user_id) ? 'sent' : 'received';
    echo '<div class="message ' . $messageClass . '">';
    echo '<p>' . htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<span>' . htmlspecialchars($row['sent_at'], ENT_QUOTES, 'UTF-8') . '</span>';
    echo '</div>';
}

// Close the connection
$stmt->close();
$conn->close();
?>
