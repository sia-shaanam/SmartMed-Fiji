<?php
// Database connection
$mysqli = new mysqli('localhost', 'root', '', 'smf_db');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointmentId = $_POST['appointment_id'];
    $remark = $_POST['remark'];

    // Update remark in the database
    $stmt = $mysqli->prepare("UPDATE appointments SET remark = ? WHERE appointment_id = ?");
    $stmt->bind_param('ss', $remark, $appointmentId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "Remark updated successfully";
    } else {
        echo "Failed to update remark";
    }

    $stmt->close();
}
$mysqli->close();
?>
