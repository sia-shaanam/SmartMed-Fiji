<?php
$host = 'localhost';
$dbname = 'smf_db';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

// Fetch all appointments
$sql = "SELECT * FROM appointments ORDER BY appointment_date, appointment_time";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointments</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

<h1>Appointments</h1>

<table>
    <thead>
        <tr>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Doctor ID</th>
            <th>Status</th>
            <th>Reason</th>
            <th>Remark</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($appointments as $appointment): ?>
            <tr>
                <td><?= htmlspecialchars($appointment['appointment_id']); ?></td>
                <td><?= htmlspecialchars($appointment['patient_name']); ?></td>
                <td><?= htmlspecialchars($appointment['appointment_date']); ?></td>
                <td><?= htmlspecialchars($appointment['appointment_time']); ?></td>
                <td><?= htmlspecialchars($appointment['doctor_id']); ?></td>
                <td><?= htmlspecialchars($appointment['status']); ?></td>
                <td><?= htmlspecialchars($appointment['reason']); ?></td>
                <td><?= htmlspecialchars($appointment['remark']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
