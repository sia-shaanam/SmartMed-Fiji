<?php
$host = 'localhost';
$dbname = 'smf_db';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);


// Fetch all confirmed appointments
$sql = "SELECT * FROM appointments WHERE status = 'Confirmed'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare appointments for FullCalendar
$events = [];

foreach ($appointments as $appointment) {
    $events[] = [
        'title' => $appointment['patient_name'], // Title displayed on the event
        'start' => $appointment['appointment_date'] . 'T' . $appointment['appointment_time'], // Combine date and time
        'extendedProps' => [
            'doctor_id' => $appointment['doctor_id'],
            'reason' => $appointment['reason'],
            'remark' => $appointment['remark'],
        ]
    ];
}

// Return the events as JSON
header('Content-Type: application/json');
echo json_encode($events);
?>
