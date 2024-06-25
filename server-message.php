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

function getChannelHistory($serverName, $channelName) {
    global $serversDir;
    $channelFilePath = $serversDir . $serverName . "/" . $channelName . ".txt";
    if (file_exists($channelFilePath)) {
        return file_get_contents($channelFilePath);
    } else {
        return "Channel history not found.";
    }
}

// Function to check if a channel is locked
function isChannelLocked($serverName, $channelName) {
    global $serversDir;
    $channelPemFile = $serversDir . $serverName . "/pem.json";
    if (file_exists($channelPemFile)) {
        $pem = json_decode(file_get_contents($channelPemFile), true);
        if (isset($pem['locked_channels']) && in_array($channelName, $pem['locked_channels'])) {
            return true;
        }
    }
    return false;
}

// Function to check if a user is admin or owner
function isUserAdminOrOwner($serverName, $username) {
    global $serversDir;
    $channelPemFile = $serversDir . $serverName . "/pem.json";
    if (file_exists($channelPemFile)) {
        $pem = json_decode(file_get_contents($channelPemFile), true);
        if (isset($pem['owner']) && $pem['owner'] === $username) {
            return true;
        }
        if (isset($pem['admins']) && in_array($username, $pem['admins'])) {
            return true;
        }
    }
    return false;
}

// Function to clear channel messages
function clearChannelMessages($serverName, $channelName) {
    global $serversDir;
    $channelFilePath = $serversDir . $serverName . "/" . $channelName . ".txt";
    if (file_exists($channelFilePath)) {
        file_put_contents($channelFilePath, ""); // Clear the file
        return true;
    }
    return false;
}

// Check if server_id and channel are provided in URL
if (!isset($_GET['server_id']) || !isset($_GET['channel'])) {
    echo json_encode(['status' => 'error', 'message' => 'Server or channel not specified.']);
    exit();
}

$serverName = $_GET['server_id'];
$channelName = $_GET['channel'];
$channelHistory = getChannelHistory($serverName, $channelName);

// Check if channel is locked
$channelLocked = isChannelLocked($serverName, $channelName);

// Check if user is admin or owner
$isAdminOrOwner = isUserAdminOrOwner($serverName, $username);

// Handle clear messages request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clear_messages') {
    if ($isAdminOrOwner) {
        clearChannelMessages($serverName, $channelName);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    }
    exit();
}
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
    </style>
    <script>
        $(document).ready(function() {
            // Function to clear all messages
            $('#clear-messages-btn').click(function() {
                if (confirm('Are you sure you want to clear all messages?')) {
                    $.ajax({
                        url: 'server-message.php?server_id=<?php echo urlencode($serverName); ?>&channel=<?php echo urlencode($channelName); ?>',
                        type: 'POST',
                        data: {
                            action: 'clear_messages',
                            server_id: '<?php echo htmlspecialchars($serverName); ?>',
                            channel: '<?php echo htmlspecialchars($channelName); ?>'
                        },
                        success: function(response) {
                            try {
                                var data = JSON.parse(response);
                                if (data.status === 'success') {
                                    $('#channel-history').html('');
                                    alert('Messages cleared successfully.');
                                } else {
                                    alert('Failed to clear messages: ' + data.message);
                                }
                            } catch (e) {
                                alert('Failed to parse response: ' + response);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('AJAX request failed: ' + error);
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Server: <?php echo htmlspecialchars($serverName); ?></h2>
        <a href="server.php?server_id=<?php echo urlencode($serverName); ?>">Back to Server</a>
        <h3>Channel: <?php echo htmlspecialchars($channelName); ?></h3>
        
        <div id="channel-history">
            <h3>Messages:</h3>
            <div style="border: 1px solid #ccc; padding: 10px; max-height: 300px; overflow-y: scroll;">
                <?php echo nl2br(htmlspecialchars($channelHistory)); ?>
            </div>
        </div>
        
        <br>
        
        <?php if ($channelLocked && !$isAdminOrOwner): ?>
            <p>This channel is locked. Only admins can send messages.</p>
        <?php else: ?>
            <div>
                <h3>Send Message:</h3>
                <form id="message-form" method="post" action="server-message.php">
                    <input type="hidden" name="server_id" value="<?php echo htmlspecialchars($serverName); ?>">
                    <input type="hidden" name="channel" value="<?php echo htmlspecialchars($channelName); ?>">
                    <input type="text" id="message" name="message" placeholder="Type your message here..." required>
                    <button type="submit">Send Message</button>
                </form>
            </div>
        <?php endif; ?>
        
        <?php if ($isAdminOrOwner): ?>
<br>
            <button id="clear-messages-btn">Clear All Messages</button>
        <?php endif; ?>
    </div>
</body>
</html>
