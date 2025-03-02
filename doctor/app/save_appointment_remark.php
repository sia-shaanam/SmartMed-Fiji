<?php
include('smf_db_conn.php');

// Get form data
$appointment_id = $_POST['appointment_id'];
$remark = $_POST['remark'];

// Update the remark for the specified appointment_id
$sql = "UPDATE appointments SET remark = ? WHERE appointment_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $remark, $appointment_id);

if ($stmt->execute()) {
  echo "<script>
          alert('Remark updated successfully for Appointment ID: $appointment_id.');
          window.location.href = 'doc_app_index.php';
        </script>";
} else {
  echo "<script>
          alert('Error updating remark. Please try again.');
          window.location.href = 'doc_app_index.php';
        </script>";
}

// Close connection
$stmt->close();
$conn->close();
?>
