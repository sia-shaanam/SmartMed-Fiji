<?php
session_start(); // Start the session
include('smf_db_conn.php'); // Include your database connection

// Check if user ID is available in the session
if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('You must be logged in to access this page.');
            window.location.href = '../login.html'; // Redirect to login page if not logged in
          </script>";
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Create a new PDO instance for database connection
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prepare the query with named placeholders
    $stmt = $conn->prepare("INSERT INTO telemedicine_appointments (
        appointment_id, doctor_id, doctor_name, patient_id, 
        patient_age, diagnosis, appointment_date, appointment_time, 
        status, video_call_link, reminder_datetime, reminder_note) 
        VALUES (:appointment_id, :doctor_id, :doctor_name, :patient_id, 
        :patient_age, :diagnosis, :appointment_date, :appointment_time, 
        :status, :video_call_link, :reminder_datetime, :reminder_note)");

    // Bind values from the POST request
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

    // Show success popup and redirect
    echo "<script>
            alert('Telemedicine appointment saved successfully!');
            window.location.href = '../mess/mass_index.php'; // Change to your desired page
          </script>";
} catch(PDOException $e) {
    echo "<script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.location.href = '../mess/mass_index.php'; // Redirect back to form page on error
          </script>";
}

$conn = null; // Close the connection
?>
