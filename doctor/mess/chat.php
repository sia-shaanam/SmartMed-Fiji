
<?php
// Start session and check if user is logged in
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the patient ID from the request
$patient_id = $_GET['patient_id'];

// Include database connection
include 'smf_db_conn.php';  // Assuming you have a connection script

// Retrieve patient details
$sql = "SELECT firstName FROM patient WHERE patient_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($patient['firstName']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #b1ccfe;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        .chat-box {
            height: 400px;
            overflow-y: scroll;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .message {
            margin-bottom: 10px;
            max-width: 70%;
            padding: 10px;
            border-radius: 10px;
            position: relative;
        }
        .message.sent {
            background-color: #007bff;
            color: white;
            margin-left: auto; /* Align sent messages to the right */
            text-align: right;
        }
        .message.received {
            background-color: #f1f1f1;
            color: black;
            text-align: left;
        }
        .message p {
            margin: 0;
            font-size: 16px;
        }
        .message span {
            font-size: 12px;
            color: #bbb;
            display: block;
            margin-top: 5px;
        }
        .input-group {
            display: flex;
        }
        .input-group input {
            flex-grow: 1;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .input-group button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            margin-left: 5px;
            border-radius: 5px;
            cursor: pointer;
        }
        .input-group button:hover {
            background-color: #0056b3;
        }
        .back-button{
            background-color: cornsilk;
        }
    </style>
</head>
<body>
    <div class="container">
         <!-- Back Button -->
         <button class="back-button" onclick="window.history.back();">Back</button>

        <h2>Chat with <?php echo htmlspecialchars($patient['firstName']); ?></h2>

        <div class="chat-box" id="chat-box">
            <!-- Messages will be loaded here -->
        </div>

        <div class="input-group">
            <input type="text" id="message-input" placeholder="Type a message">
            <button onclick="sendMessage()">Send</button>
        </div>
    </div>

    <script>
        // Fetch messages every 2 seconds
        setInterval(fetchMessages, 2000);

        function fetchMessages() {
            const chatBox = document.getElementById('chat-box');
            const patientId = <?php echo json_encode($patient_id); ?>;
            
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_messages.php?patient_id=' + patientId, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    chatBox.innerHTML = this.responseText;
                    chatBox.scrollTop = chatBox.scrollHeight; // Auto-scroll to the bottom
                }
            };
            xhr.send();
        }

        function sendMessage() {
            const messageInput = document.getElementById('message-input');
            const message = messageInput.value;
            const patientId = <?php echo json_encode($patient_id); ?>;

            if (message.trim() === '') return;

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'send_message.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (this.status === 200) {
                    messageInput.value = ''; // Clear the input
                    fetchMessages(); // Refresh chat
                }
            };
            xhr.send('message=' + message + '&patient_id=' + patientId);
        }
    </script>
</body>
</html>
