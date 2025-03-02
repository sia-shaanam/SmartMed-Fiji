<?php
include('smf_db_conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $user_id = trim($_POST['user_id']);
    $password = $_POST['password'];

    // Check if user_id and password are not empty
    if (empty($user_id) || empty($password)) {
        echo "<script>alert('Please enter both User ID and Password.'); window.history.back();</script>";
        exit();
    }

    // Prepared statement to fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    if (!$stmt) {
        die('Error: Failed to prepare statement: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Start a session and regenerate session ID for security
            session_start();
            session_regenerate_id(true);

            // Store user data in session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === "doctor") {
                header("Location: ../doctor/doc_dash.php");
            } elseif ($user['role'] === "admin") {
                header("Location: ../admin/admin_dash.php");
            } elseif ($user['role'] === "nurse") { // Added condition for nurses
                header("Location: ../nurse/doc_dash.php"); // Redirect nurse to their dashboard
            } else {
                // Handle other roles if necessary
                header("Location: ../default/dashboard.php"); // Default behavior
            }
            exit();
        } else {
            // Invalid password
            echo "<script>alert('Invalid password. Please try again.'); window.history.back();</script>";
        }
    } else {
        // User ID not found
        echo "<script>alert('User ID not found. Please try again.'); window.history.back();</script>";
    }

    // Close the statement
    $stmt->close();
}
?>
