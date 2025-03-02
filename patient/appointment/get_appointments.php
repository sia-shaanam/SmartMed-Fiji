<?php
// Database connection
$host = 'localhost';
$dbname = 'smf_db';
$user = 'root';
$pass = '';
$conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Get patient_id from query parameters
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

if ($patient_id) {
    // Fetch appointments for the given patient_id
    $query = $conn->prepare("SELECT * FROM appointments WHERE patient_id = ?");
    $query->execute([$patient_id]);
    $appointments = $query->fetchAll(PDO::FETCH_ASSOC);

    // Format the appointments for FullCalendar
    $events = array_map(function ($appointment) {
        return [
            'id' => $appointment['id'],
            'title' => "Appointment with Doctor " . $appointment['doctor_id'],
            'start' => $appointment['appointment_date'],
        ];
    }, $appointments);

    echo json_encode($events);
} else {
    echo json_encode([]);
}
?>
