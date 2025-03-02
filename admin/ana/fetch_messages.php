<?php
include 'smf_db_conn.php';

$sql = "SELECT DATE(sent_at) as date, COUNT(*) as message_count
        FROM messages
        GROUP BY date
        ORDER BY date";
$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
