<?php
$host = 'localhost';
$dbname = 'smf_db';
$user = 'root';
$pass = '';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['appointment_ids']) && !empty($_POST['appointment_ids'])) {
        $appointment_ids = $_POST['appointment_ids'];
        $placeholders = implode(',', array_fill(0, count($appointment_ids), '?'));

        if (isset($_POST['confirm'])) {
            // Update the status of the selected appointments to "Confirmed"
            $sql = "UPDATE appointments SET status = 'Confirmed' WHERE appointment_id IN ($placeholders)";
        } elseif (isset($_POST['cancel'])) {
            // Update the status of the selected appointments to "Canceled"
            $sql = "UPDATE appointments SET status = 'Canceled' WHERE appointment_id IN ($placeholders)";
        }

        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($appointment_ids)) {
            echo (isset($_POST['confirm'])) ? "Selected appointments have been confirmed." : "Selected appointments have been canceled.";
        } else {
            echo "Error updating appointments.";
        }
    } else {
        echo "No appointments selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm or Cancel Appointments</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:  #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #003366;
            color:white;
        }
        tr:hover {
            background-color: #003366;
            color:white;
        }
        input[type="checkbox"] {
            transform: scale(1.2);
        }
        .btn-container {
            text-align: center;
            margin-top: 20px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #20c997;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #007bff;
        }
        .dashboard-btn {
            color: white;
            background-color:#04c9f5;
            border: none;
            padding: 12px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .dashboard-btn:hover {
            color: white;
            background-color: #007bff;
        }
        @media screen and (max-width: 600px) {
            th, td {
                font-size: 14px;
                padding: 8px;
            }
            button {
                width: 100%;
                padding: 12px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>

<h1>Pending Appointments</h1>

<?php
// Fetch all pending appointments
$sql = "SELECT * FROM appointments WHERE status = 'Pending' ORDER BY appointment_date, appointment_time";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pending_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($pending_appointments) > 0): ?>
    <form method="POST" action="confirm-appointments.php">
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Appointment ID</th>
                    <th>Patient Name</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor ID</th>
                    <th>Reason</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_appointments as $appointment): ?>
                    <tr>
                        <td><input type="checkbox" name="appointment_ids[]" value="<?= htmlspecialchars($appointment['appointment_id']); ?>"></td>
                        <td><?= htmlspecialchars($appointment['appointment_id']); ?></td>
                        <td><?= htmlspecialchars($appointment['patient_name']); ?></td>
                        <td><?= htmlspecialchars($appointment['appointment_date']); ?></td>
                        <td><?= htmlspecialchars($appointment['appointment_time']); ?></td>
                        <td><?= htmlspecialchars($appointment['doctor_id']); ?></td>
                        <td><?= htmlspecialchars($appointment['reason']); ?></td>
                        <td><?= htmlspecialchars($appointment['remark']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="btn-container"> 
            <button type="submit" name="confirm">Confirm Selected Appointments</button>
            <button type="submit" name="cancel" style="background-color: #dc3545;">Cancel Selected Appointments</button>
            <a href="doc_app_index.php" class="dashboard-btn">Go to Dashboard</a>
        </div>
    </form>
<?php else: ?>
    <p>No pending appointments found.</p>
    <div class="btn-container"> 
        <a href="doc_app_index.php" class="dashboard-btn">Go to Dashboard</a> 
    </div>
<?php endif; ?>

</body>
</html>
