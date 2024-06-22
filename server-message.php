<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$serversDir = "servers/";
$chatsDir = "chats/";

// Check if server_id and channel are provided in URL
if (!isset($_GET['server_id']) || !isset($_GET['channel'])) {
    die("Server or Channel not specified.");
}

$serverId = $_GET['server_id'];
$channelName = $_GET['channel'];

// Function to get channel history
function getChannelHistory($serverId, $channelName) {
    global $serversDir;
    $channelFilePath = $serversDir . $serverId . "/" . $channelName . ".txt";
    if (file_exists($channelFilePath)) {
        return file_get_contents($channelFilePath);
    } else {
        return "Channel history not found.";
    }
}

// Handle sending messages
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['server']) && isset($_POST['channel']) && isset($_POST['message'])) {
        $serverId = $_POST['server'];
        $channelName = $_POST['channel'];
        $message = $_POST['message'];
        $channelFilePath = $serversDir . $serverId . "/" . $channelName . ".txt";
        
        // Format message with timestamp
        $timestamp = date('Y-m-d H:i:s');
        $newMessage = "[$timestamp] $username: $message\n";
        
        // Append message to channel file
        file_put_contents($channelFilePath, $newMessage, FILE_APPEND | LOCK_EX);
        
        // Send response back to indicate success
        echo json_encode(array('status' => 'success'));
        exit();
    }
}

$channelHistory = getChannelHistory($serverId, $channelName);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aurora Chat - Server Message</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <style>
        .container {
            font-family: 'Montserrat', sans-serif;
        }
        .server-name {
            color: #333;
        }
        .message-container {
            border: 1px solid #ccc;
            padding: 10px;
            max-height: 300px;
            overflow-y: scroll;
        }
        .message {
            margin-bottom: 10px;
        }
    </style>
    <script>
    $(document).ready(function() {
        // Function to refresh channel messages
        function refreshChannel() {
            $.ajax({
                url: window.location.href,
                type: 'GET',
                cache: false,
                success: function(data) {
                    var newMessages = $(data).find('#channel-history').html();
                    $('#channel-history').html(newMessages);
                    $('#channel-history').scrollTop($('#channel-history')[0].scrollHeight);
                },
                complete: function() {
                    setTimeout(refreshChannel, 2000); // Refresh every 2 seconds
                }
            });
        }
        
        // Initial call to refresh channel messages
        refreshChannel();
        
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
        <h2 class="server-name">Server: <?php echo htmlspecialchars($serverId); ?></h2>
        <a href="server.php?server_id=<?php echo urlencode($serverId); ?>">Back to Server</a>
        <h3 class="server-name">Channel: <?php echo isset($_GET['channel']) ? htmlspecialchars($_GET['channel']) : 'Channel name not specified'; ?></h3>
        <div id="channel-history" class="message-container">
            <?php echo nl2br(htmlspecialchars($channelHistory)); ?>
        </div>
        <br>
        <div>
            <h3>Send Message:</h3>
            <form id="message-form" method="post" action="server-message.php?server_id=<?php echo urlencode($serverId); ?>&channel=<?php echo urlencode($channelName); ?>">
                <input type="hidden" name="server" value="<?php echo htmlspecialchars($serverId); ?>">
                <input type="hidden" name="channel" value="<?php echo htmlspecialchars($channelName); ?>">
                <input type="text" id="message" name="message" placeholder="Type your message here..." required>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>
</body>
</html>
