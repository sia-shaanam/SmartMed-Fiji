<?php
session_start();
include('smf_db_conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize login credentials here
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check user in database
    $sql = "SELECT user_id, password_hash FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id, $password_hash);
    $stmt->fetch();

    if (password_verify($password, $password_hash)) {
        // If password is correct, store user_id in session
        $_SESSION['user_id'] = $user_id;
        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();
    } else {
        echo 'Invalid username or password';
    }
    $stmt->close();
}
?>
