<?php
session_start(); // Start the session at the beginning

// Include database connection
include('smf_db_conn.php');

// Ensure the session variable `user_id` is set before using it
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    echo "<script>
            alert('User not logged in.');
            window.location.href = 'login_page.php'; // Redirect to login if user_id not set
          </script>";
    exit();
}

try {
    // Define database connection details (if not set in smf_db_conn.php)
    include('smf_db_conn.php');
    
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Insert query with named placeholders
    $stmt = $conn->prepare("INSERT INTO telemedicine_appointments (
        appointment_id, doctor_id, doctor_name, patient_id, 
        patient_age, diagnosis, appointment_date, appointment_time, 
        status, video_call_link, reminder_datetime, reminder_note) 
        VALUES (:appointment_id, :doctor_id, :doctor_name, :patient_id, 
        :patient_age, :diagnosis, :appointment_date, :appointment_time, 
        :status, :video_call_link, :reminder_datetime, :reminder_note)");

    // Bind values from form submission
    $stmt->bindParam(':appointment_id', $_POST['appointment_id']);
    $stmt->bindParam(':doctor_id', $_POST['doctor_id']);
    $stmt->bindParam(':doctor_name', $_POST['doctor_name']);
    $stmt->bindParam(':patient_id', $_POST['patient_id']);
    $stmt->bindParam(':patient_age', $_POST['patient_age']);
    $stmt->bindParam(':diagnosis', $_POST['diagnosis']);
    $stmt->bindParam(':appointment_date', $_POST['appointment_date']);
    $stmt->bindParam(':appointment_time', $_POST['appointment_time']);
    $stmt->bindParam(':status', $_POST['status']);
    $stmt->bindParam(':video_call_link', $_POST['video_call_link']);
    $stmt->bindParam(':reminder_datetime', $_POST['reminder_datetime']);
    $stmt->bindParam(':reminder_note', $_POST['reminder_note']);

    // Execute the query
    $stmt->execute();

    // Show success message and redirect
    echo "<script>
            alert('Telemedicine appointment saved successfully!');
            window.location.href = '../mess/mass_index.php'; // Change to your dashboard page
          </script>";
} catch(PDOException $e) {
    echo "<script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href = '../mess/mass_index.php'; // Redirect back to form page on error
          </script>";
}

// Close the database connection
$conn = null;
?>
