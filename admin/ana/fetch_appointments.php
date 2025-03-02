<?php
include 'smf_db_conn.php';

$sql = "SELECT status, COUNT(*) as total FROM appointments GROUP BY status";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
