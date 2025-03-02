<?php
// update_doctor.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    include '../smf_db_conn.php';
    
    // Collect form data
    $doctor_id = $_POST['doctor_id'];
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $contact_information = $_POST['contact_information'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $job_position = $_POST['job_position'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];

    // SQL to update doctor record
    $sql = "UPDATE doctors 
            SET name=?, specialization=?, contact_information=?, email=?, department=?, job_position=?, gender=?, age=? 
            WHERE doctor_id=?";

    $stmt = $conn->prepare($sql);
    
    // Note: Removed the extra $user_id, and correctly matched the placeholders
    $stmt->bind_param("sssssssii", $name, $specialization, $contact_information, $email, $department, $job_position, $gender, $age, $doctor_id);

    if ($stmt->execute()) {
        // Redirect to dashboard immediately after successful update
        echo "<script>
                alert('Record updated successfully!');
                window.location.href = '../doc_dash.php'; // Redirect to the dashboard immediately
              </script>";
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
