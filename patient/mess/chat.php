<?php
// Start session to access logged-in user's patient_id
session_start();

// Assuming patient_id is stored in session after login
$patient_id = $_SESSION['patient_id'];

// Get doctor_id from the URL parameter
$doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : null;

// Database connection (update with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smf_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for sending a new message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = $_POST['message'];

    if (!empty($message) && !empty($doctor_id)) {
        // Insert the new message into the messages table
        $sql = "INSERT INTO messages (patient_id, user_id, message, sent_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $patient_id, $doctor_id, $message);
        $stmt->execute();
    }
}

// Fetch the chat history between the logged-in patient and the selected doctor
$sql = "SELECT m.message, m.sent_at, 
        CASE 
            WHEN m.patient_id = ? THEN 'You' 
            ELSE 'Doc' 
        END AS sender
        FROM messages m
        JOIN doctors d ON m.user_id = d.user_id
        WHERE (m.patient_id = ? AND m.user_id = ?) OR (m.patient_id = ? AND m.user_id = ?)
        ORDER BY m.sent_at ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $patient_id, $patient_id, $doctor_id, $doctor_id, $patient_id);
$stmt->execute();
$chat_result = $stmt->get_result();

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with Doctor</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS for styling -->
</head>
<style>
/* General body styling */
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
    padding: 20px;
}

/* Chat container styling */
.chat-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

/* Chat box styling */
.chat-box {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    padding: 20px;
    max-width: 600px;
    width: 100%;
    display: flex;
    flex-direction: column;
}

/* Chat history styling */
.chat-history {
    max-height: 400px;
    overflow-y: auto;
    margin-bottom: 20px;
}

.message {
    padding: 10px;
    border-radius: 10px;
    margin-bottom: 10px;
}

.you {
    background-color: #d1ecf1;
    text-align: right;
}

.doctor {
    background-color: #f8d7da;
    text-align: left;
}

/* New message form styling */
.new-message-form {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.new-message-form textarea {
    width: 80%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.new-message-form button {
    padding: 10px 20px;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.new-message-form button:hover {
    background-color: #0056b3;
    transform: translateY(-2px);
}

.new-message-form button:active {
    background-color: #004085;
    transform: translateY(0);
}
.back-button {
    background-color: cornsilk;
    color: black; /* Make the text color black for better visibility */
    padding: 5px 10px; /* Smaller padding for a more compact button */
    font-size: 14px; /* Reduce the font size */
    border: none;
    border-radius: 3px; /* Smaller border-radius for less rounded corners */
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    display: inline-block; /* Inline block to keep it aligned */
}

.back-button:hover {
    background-color: #eee8aa; /* Darker shade of cornsilk for hover effect */
    transform: translateY(-1px);
}

.back-button:active {
    background-color: #ddd; /* Even darker shade for when button is clicked */
    transform: translateY(0);
}

</style>
<script>
// Scroll the chat history to the bottom when the page loads
window.onload = function() {
    var chatHistory = document.querySelector('.chat-history');
    chatHistory.scrollTop = chatHistory.scrollHeight;
}
</script>
<body>
    <div class="chat-container">
             
        <div class="chat-box">
             <!-- Back Button -->
<button class="back-button" onclick="window.location.href='view_messages.php';">Back</button>

            <h2>Chat with Doctor</h2>
      
            <div class="chat-history">
                <?php while ($row = $chat_result->fetch_assoc()): ?>
                    <div class="message <?php echo ($row['sender'] === 'You') ? 'you' : 'doctor'; ?>">
                        <p><?php echo htmlspecialchars($row['message']); ?></p>
                        <small>Sent at: <?php echo htmlspecialchars($row['sent_at']); ?></small>
                    </div>
                <?php endwhile; ?>
            </div>
            
            <!-- Form to send a new message -->
            <form class="new-message-form" action="chat.php?patient_id=<?php echo $patient_id; ?>&doctor_id=<?php echo $doctor_id; ?>" method="post">
                <textarea name="message" placeholder="Type your message here..." rows="3" required></textarea>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
</body>
</html>
