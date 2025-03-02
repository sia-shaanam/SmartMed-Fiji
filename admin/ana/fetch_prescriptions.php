<?php
require 'smf_db_conn.php';

$query = "SELECT reason AS medication, COUNT(*) AS count 
          FROM appointments 
          WHERE status = 'Confirmed' 
          GROUP BY reason";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>
