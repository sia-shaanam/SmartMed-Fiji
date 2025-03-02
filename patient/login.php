<!-- login.php -->
<?php
include('smf_db_conn.php');

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['login_id'];
    $password = $_POST['password'];

    // Debugging: Check if POST data is received
    // echo "User ID: $user_id, Password: $password"; 

    // Prepared statement to fetch user data
    $stmt = $conn->prepare("SELECT * FROM login WHERE login_id = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error); // Debugging database connection
    }
    $stmt->bind_param("s", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        // Fetch user data
        $user = $result->fetch_assoc();
        
        // Debugging: Check if user is found
        // echo "User found: " . $user['login_id']; 

        // Verify password (assuming password in DB is hashed)
        if (password_verify($password, $user['password'])) {
            // Start session and store user data
            session_start();
            $_SESSION['login_id'] = $user['login_id'];

            // Debugging: Check session value
            // echo "Session started for user: " . $_SESSION['login_id']; 

            // Redirect based on user role (check if session is set)
            if (isset($_SESSION['login_id'])) {
                header("Location: pdashboard.php");
                exit(); // Ensure the script stops after redirect
            }
        } else {
            // Password incorrect
            echo "<script>alert('Invalid password. Please try again.'); window.history.back();</script>";
        }
    } else {
        // User ID not found
        echo "<script>alert('User ID not found. Please try again.'); window.history.back();</script>";
    }
    // Close statement
    $stmt->close();
}
?>
