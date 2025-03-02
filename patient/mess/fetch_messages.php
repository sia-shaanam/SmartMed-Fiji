<?php
session_start();

// Include database connection
include 'smf_db_conn.php';

// Check if the user is logged in
if (!isset($_SESSION['patient_id'])) {
    exit('User not logged in.');
}

// Get the doctor ID from the request
$doctor_id = $_GET['doctor_id'];
$patient_id = $_SESSION['patient_id'];

// Retrieve messages for this user and patient
$sql = "SELECT message, sent_at, user_id FROM messages WHERE (user_id = ? AND patient_id = ?) OR (user_id = ? AND patient_id = ?) ORDER BY sent_at ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $doctor_id, $patient_id, $patient_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

// Display messages
$messages = '';
while ($row = $result->fetch_assoc()) {
    // Check if the message was sent by the patient
    $messageClass = ($row['user_id'] == $patient_id) ? 'sent' : 'received'; // Adjusting logic based on user_id
    $messages .= '<div class="message ' . $messageClass . '">';
    $messages .= '<p>' . htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8') . '</p>';
    $messages .= '<span>' . htmlspecialchars($row['sent_at'], ENT_QUOTES, 'UTF-8') . '</span>';
    $messages .= '</div>';
}

echo $messages; // Output the messages
?>
