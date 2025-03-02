<?php
include 'smf_db_conn.php';

$sql = "SELECT d.name, d.specialization, COUNT(a.appointment_id) AS total_appointments
        FROM doctors d
        LEFT JOIN appointments a ON d.doctor_id = a.doctor_id
        GROUP BY d.name, d.specialization";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
