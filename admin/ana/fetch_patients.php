<?php
require 'smf_db_conn.php';



$query = "
    SELECT status AS category, COUNT(*) AS count 
    FROM appointments 
    GROUP BY status";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>
