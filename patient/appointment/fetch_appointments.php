<?php
session_start();
include('smf_db_conn.php');

// Check if the user is logged in
if (!isset($_SESSION['login_id'])) {
    echo 'No login_id found in the session.';
    header('Location: login.php');
    exit(); // Ensure the script stops after redirection
}

// Regenerate session ID to prevent session fixation attacks
session_regenerate_id(true);

// Fetch patient's information based on login_id from session
$login_id = $_SESSION['login_id'];
$sql = "SELECT firstName, patient_id FROM patient WHERE login_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    // Handle statement preparation error
    error_log("Query preparation failed: " . $conn->error);
    header('Location: error.php'); // Redirect to a friendly error page
    exit();
}

$stmt->bind_param("s", $login_id);
$stmt->execute();
$stmt->bind_result($firstName, $patient_id);

// Check if query returns any results
if ($stmt->fetch()) {
    // Store patient_id in the session
    $_SESSION['patient_id'] = $patient_id; // Set patient_id in the session
} else {
    echo 'No data found for this login_id: ' . htmlspecialchars($login_id); // Debugging line
    exit(); // Stop execution if no patient data is found
}

$stmt->close(); // Close the statement after fetching the first result

// Now retrieve the patient details using patient_id from the session
$patient_id = $_SESSION['patient_id']; // Use patient_id from session
$sql = "SELECT * FROM patient WHERE patient_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("Query preparation failed: " . $conn->error);
    header('Location: error.php'); // Redirect to a friendly error page
    exit();
}

$stmt->bind_param("i", $patient_id); // Bind as an integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $patient = $result->fetch_assoc();
} else {
    // Handle case where patient is not found
    echo "No patient found.";
    exit(); // Stop execution if no patient is found
}

$stmt->close(); // Close statement after fetching the patient

// Query to fetch appointments for the logged-in patient
$sql = "SELECT title, start_date, end_date FROM appointments WHERE patient_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("Query preparation failed: " . $conn->error);
    header('Location: error.php');
    exit();
}

$stmt->bind_param("i", $patient_id); // Bind patient_id as integer
$stmt->execute();
$result = $stmt->get_result();

// Create an array to store appointments
$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = [
        'title' => $row['title'],
        'start' => $row['start_date'],
        'end' => $row['end_date']
    ];
}

$stmt->close(); // Close statement after fetching the appointments

// Return appointments as JSON
header('Content-Type: application/json');
echo json_encode($appointments);
?>
