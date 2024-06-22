<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$chatsDir = "chats/";
$chatsFile = "data/chats.json";

function getChatHistory($chatId) {
    global $chatsDir;
    $chatFile = $chatsDir . "$chatId.txt";
    if (file_exists($chatFile)) {
        return file_get_contents($chatFile);
    } else {
        return "Chat history not found.";
    }
}

// Handle sending messages
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['chat_id']) && isset($_POST['message'])) {
        $chatId = $_POST['chat_id'];
        $message = $_POST['message'];
        $chatFile = $chatsDir . "$chatId.txt";
        
        // Format message with timestamp
        $timestamp = date('Y-m-d H:i:s');
        $newMessage = "[$timestamp] $username: $message\n";
        
        // Append message to chat file
        file_put_contents($chatFile, $newMessage, FILE_APPEND | LOCK_EX);
        
        // Send response back to indicate success
        echo json_encode(array('status' => 'success'));
        exit();
    }
}

// Check if chat_id is provided in URL
if (!isset($_GET['chat_id'])) {
    die("Chat ID not specified.");
}

$chatId = $_GET['chat_id'];
$chatHistory = getChatHistory($chatId);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aurora Chat - Chat</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Function to refresh chat messages
        function refreshChat() {
            $.ajax({
                url: window.location.href,
                type: 'GET',
                cache: false,
                success: function(data) {
                    var newMessages = $(data).find('#chat-history').html();
                    $('#chat-history').html(newMessages);
                },
                complete: function() {
                    setTimeout(refreshChat, 2000); // Refresh every 2 seconds
                }
            });
        }
        
        // Initial call to refresh chat messages
        refreshChat();
        
        // Handle sending messages
        $('#message-form').submit(function(e) {
            e.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Clear and focus input field after sending message
                        $('#message').val('').focus();
                    }
                }
            });
        });
    });
    </script>
</head>
<body>
    <div class="container">
        <h2>Chat ID: <?php echo htmlspecialchars($chatId); ?></h2>
        <a href="index.php">Back to Home</a>
        <div id="chat-history">
            <h3>Chat History:</h3>
            <div style="border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: scroll;">
                <?php echo nl2br(htmlspecialchars($chatHistory)); ?>
            </div>
        </div>
        <br>
        <div>
            <h3>Send Message:</h3>
            <form id="message-form" method="post" action="chat.php">
                <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars($chatId); ?>">
                <input type="text" id="message" name="message" placeholder="Type your message here..." required>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>
</body>
</html>
