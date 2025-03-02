<?php
// Database connection
include('smf_db_conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capture form data
    $login_id = $_POST['login_id'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Insert data into the login table
    $sql = "INSERT INTO login (login_id, email, password) VALUES (?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sss', $login_id, $email, $password);

    if ($stmt->execute()) {
        echo "Patient registered successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

