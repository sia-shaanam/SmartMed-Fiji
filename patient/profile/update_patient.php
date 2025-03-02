<?php
session_start();
include('smf_db_conn.php');

// Check if the user is logged in
if (!isset($_SESSION['login_id'])) {
    echo 'No login_id found in the session.';
    header('Location: login.php');
    exit();
}

// Check if the patient ID is set in the session and matches the form data
if (isset($_SESSION['patient_id'], $_POST['patient_id']) && $_SESSION['patient_id'] === $_POST['patient_id']) {
    $patient_id = $_SESSION['patient_id'];

    // Get form inputs with proper sanitization
    $firstName = htmlspecialchars(trim($_POST['firstName']));
    $lastName = htmlspecialchars(trim($_POST['lastName']));
    $preferredName = htmlspecialchars(trim($_POST['preferredName']));
    $dob = htmlspecialchars(trim($_POST['dob']));
    $gender = htmlspecialchars(trim($_POST['gender']));
    $preferredPronouns = htmlspecialchars(trim($_POST['preferredPronouns']));
    $maritalStatus = htmlspecialchars(trim($_POST['maritalStatus']));
    $address = htmlspecialchars(trim($_POST['address']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $contactPreference = htmlspecialchars(trim($_POST['contactPreference']));
    $emergencyContactName = htmlspecialchars(trim($_POST['emergencyContactName']));
    $relationship = htmlspecialchars(trim($_POST['relationship']));
    $emergencyContactNumber = htmlspecialchars(trim($_POST['emergencyContactNumber']));

    // Prepare an SQL statement for updating the patient's information
    $sql = "UPDATE patient 
            SET firstName = ?, lastName = ?, preferredName = ?, dob = ?, gender = ?, 
                preferredPronouns = ?, maritalStatus = ?, address = ?, phone = ?, 
                contactPreference = ?, emergencyContactName = ?, relationship = ?, emergencyContactNumber = ? 
            WHERE patient_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ssssssssssssss',
        $firstName,
        $lastName,
        $preferredName,
        $dob,
        $gender,
        $preferredPronouns,
        $maritalStatus,
        $address,
        $phone,
        $contactPreference,
        $emergencyContactName,
        $relationship,
        $emergencyContactNumber,
        $patient_id
    );

    // Execute the statement and provide feedback
    if ($stmt->execute()) {
        echo "<script>
                alert('Profile updated successfully.');
                window.location.href = 'index.php';
              </script>";
        exit();
    } else {
        echo "<script>
                alert('Error updating profile: " . $stmt->error . "');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Invalid patient ID or session expired.');
            window.location.href = 'login.php';
          </script>";
    exit();
}
// Close the statement and the database connection
$stmt->close();
$conn->close();
?>
