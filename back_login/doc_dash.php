<?php
// Start the session
session_start();

// Check if the user is logged in and has the role 'doctor'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'doctor') {
    header("Location: ../login.php"); // Redirect to login if not authenticated
    exit();
}

// Include the database connection file
include('smf_db_conn.php');

// Retrieve the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Fetch the doctor's details based on the user_id
$stmt = $conn->prepare("SELECT name, specialization FROM doctors WHERE user_id = ?");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $doctor = $result->fetch_assoc(); // Fetch doctor details
    $doctor_name = htmlspecialchars($doctor['name']); // Sanitize output
    $specialization = htmlspecialchars($doctor['specialization']);
} else {
    echo "<script>alert('Doctor details not found. Please contact the administrator.');</script>";
    exit();
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
</head>
<body>
    <h1>Welcome, Dr. <?php echo $doctor_name; ?>!</h1>
    <p>Specialization: <?php echo $specialization ?: 'Not provided'; ?></p>

    <p><a href="../logout.php">Logout</a></p>
</body>
</html>
