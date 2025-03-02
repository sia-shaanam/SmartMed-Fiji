<!-- login.php -->
<?php
include('smf_db_conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    // Prepared statement to fetch user data
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Fetch user data
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Start a session and store user data
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on user role
            if ($user['role'] === "doctor") {
                header("Location: ../doctor/doc_dash.php");
            } elseif ($user['role'] === "nurse") {
                header("Location: nurse-dashboard.html");
            } elseif ($user['role'] === "admin") {
                header("Location:../admin/admin_dash.php");
            }
            exit();
        } else {
            echo "<script>alert('Invalid password. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('User ID not found. Please try again.'); window.history.back();</script>";
    }
    $stmt->close();
}
?>