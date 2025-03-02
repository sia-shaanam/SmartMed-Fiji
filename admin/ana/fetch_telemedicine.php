<?php
require 'smf_db_conn.php';


$query = "SELECT appointment_date AS date, COUNT(*) AS sessions 
          FROM appointments 
          WHERE reason LIKE '%telemedicine%'
          GROUP BY appointment_date";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>
