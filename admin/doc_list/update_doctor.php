<?php
// Database connection
$host = 'localhost'; // Your database host
$db = 'smf_db'; // Your database name
$user = 'root'; // Your database username
$pass = ''; // Your database password

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Update doctor info if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $contact_information = $_POST['contact_information'];
    $email = $_POST['email'];
    $department = $_POST['department'];
    $job_position = $_POST['job_position'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];

    // Update the doctor's data
    $sql = "UPDATE doctors SET name=?, specialization=?, contact_information=?, email=?, department=?, job_position=?, gender=?, age=? WHERE doctor_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssis", $name, $specialization, $contact_information, $email, $department, $job_position, $gender, $age, $doctor_id);

    if ($stmt->execute()) {
        // Success message and redirect to ke.php
        echo "<script>
                alert('Doctor information updated successfully.');
                window.location.href = 'doc_list.php';
              </script>";
    } else {
        // Error message and redirect back to the form
        echo "<script>
                alert('Error updating doctor information: " . addslashes($stmt->error) . "');
                window.history.back();
              </script>";
    }

    $stmt->close();
}

$conn->close();
?>
