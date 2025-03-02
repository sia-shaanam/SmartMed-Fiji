<?php
// Database connection
include('smf_db_conn.php');

if (isset($_POST['bill_id'])) {
    $bill_id = $_POST['bill_id'];

    // Update bill status
    $sql = "UPDATE billing SET status = 'paid' WHERE bill_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bill_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo '<span class="paid">Paid</span>';
    } else {
        echo 'Error updating bill status.';
    }

    $stmt->close();
}

$conn->close();
?>
