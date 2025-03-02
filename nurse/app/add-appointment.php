<?php
$host = 'localhost';
$dbname = 'smf_db';
$user = 'root';
$pass = '';

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all doctors to populate the dropdown
    $doctors = $pdo->query("SELECT doctor_id, name FROM doctors")->fetchAll(PDO::FETCH_ASSOC);
  
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Gather form data
        $appointment_id = uniqid();
        $patient_id = $_POST['patient_id'] ?? null;
        $patient_name = $_POST['patient_name'];
        $doctor_id = $_POST['doctor_id']; // Selected doctor
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time']; // Added appointment time
        $status = $_POST['status'];
        $reason = $_POST['reason'];

        // Insert new appointment into the database
        $sql = "INSERT INTO appointments 
                (appointment_id, patient_id, patient_name, appointment_date, appointment_time, doctor_id, status, reason)
                VALUES 
                (:appointment_id, :patient_id, :patient_name, :appointment_date, :appointment_time, :doctor_id, :status, :reason)";
        
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            'appointment_id' => $appointment_id,
            'patient_id' => $patient_id,
            'patient_name' => $patient_name,
            'appointment_date' => $appointment_date,
            'appointment_time' => $appointment_time, // Added this line
            'doctor_id' => $doctor_id,
            'status' => $status,
            'reason' => $reason
        ]);

        if ($success) {
            echo "Appointment added successfully.";
        } else {
            echo "Error adding appointment.";
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Appointment</title>
    <link rel="stylesheet" href="styles.css"> <!-- Your CSS file for styling -->
</head>
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 20px;
    }

    .container {
        max-width: 800px;
        margin: 0 auto;
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #2a5d67;
        font-size: 1.8rem;
    }

    form {
        margin-top: 20px;
    }

    label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
        color: #34495e;
    }

    input[type="text"],
    input[type="date"],
    input[type="time"],
    select,
    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #cfd8dc;
        border-radius: 4px;
        font-size: 14px;
        box-sizing: border-box;
        background-color: #f9f9f9;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #1abc9c;
        box-shadow: 0 0 5px rgba(26, 188, 156, 0.3);
    }

    textarea {
        height: 120px;
    }

    button[type="submit"] {
        margin-top: 20px;
        padding: 12px;
        background: #003366;
        color: white;
        border: none;
        border-radius: 30px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: background 0.3s ease;
        width: 100%;
    }

    button[type="submit"]:hover {
        background: linear-gradient(135deg, #16a085 0%, #1abc9c 100%);
    }

    .dashboard-btn {
        display: inline-block;
        color: white;
        background-color: #003366;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 0.9rem;
        cursor: pointer;
        transition: background-color 0.3s;
        margin-top: 20px;
        text-align: center;
    }

    .dashboard-btn:hover {
        background-color: blue;
    }
</style>
<body>


<div class="container">
    <h1>Add New Appointment</h1>

    <a href="doc_app_index.php" class="dashboard-btn">Go to Dashboard</a>

    <form method="POST" action="add-appointment.php">
    <label for="patient_id">Patient ID:</label>
    <input type="text" id="patient_id" name="patient_id" required>

    <label for="patient_name">Patient Name:</label>
    <input type="text" id="patient_name" name="patient_name" required>

    <label for="appointment_date">Appointment Date:</label>
    <input type="date" id="appointment_date" name="appointment_date" required>

    <label for="appointment_time">Appointment Time:</label>
    <input type="time" id="appointment_time" name="appointment_time" required>

    <label for="doctor_id">Select Doctor:</label>
    <select id="doctor_id" name="doctor_id" required>
        <option value="">-- Select Doctor --</option>
        <?php foreach ($doctors as $doctor): ?>
            <option value="<?= $doctor['doctor_id']; ?>"><?= $doctor['name']; ?></option>
        <?php endforeach; ?>
    </select>

    <label for="status">Status:</label>
    <select id="status" name="status" required>
        <option value="Pending">Pending</option>
        <option value="Confirmed">Confirmed</option>
        <option value="Cancelled">Cancelled</option>
    </select>

    <label for="reason">Reason:</label>
    <textarea id="reason" name="reason"></textarea>

    <button type="submit">Add Appointment</button>
</form>

</div>


</body>
</html>