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
$serversJsonFile = "data/servers.json";

// Function to delete a channel
function deleteChannel($serverId, $channelName) {
    global $serversDir;
    $channelFilePath = $serversDir . $serverId . "/" . $channelName . ".txt";
    if (file_exists($channelFilePath)) {
        unlink($channelFilePath);
        return true;
    }
    return false;
}

// Function to create a new channel
function createChannel($serverId, $channelName) {
    global $serversDir;
    $channelFilePath = $serversDir . $serverId . "/" . $channelName . ".txt";
    if (!file_exists($channelFilePath)) {
        // Create an empty file for the channel
        $created = file_put_contents($channelFilePath, "");
        return $created !== false;
    }
    return false;
}

// Function to delete a server
function deleteServer($serverId) {
    global $serversDir, $serversJsonFile;
    $serverPath = $serversDir . $serverId . "/";
    if (is_dir($serverPath)) {
        // Delete all channel files
        $files = glob($serverPath . "*.txt");
        foreach ($files as $file) {
            unlink($file);
        }
        // Delete server folder
        rmdir($serverPath);

        // Update servers.json to remove the server
        $servers = json_decode(file_get_contents($serversJsonFile), true);
        if (isset($servers[$serverId])) {
            unset($servers[$serverId]);
            file_put_contents($serversJsonFile, json_encode($servers, JSON_PRETTY_PRINT));
        }

        return true;
    }
    return false;
}

// Handle delete channel request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_channel') {
    if (isset($_POST['server_id']) && isset($_POST['channel_name'])) {
        $serverId = $_POST['server_id'];
        $channelName = $_POST['channel_name'];
        if (deleteChannel($serverId, $channelName)) {
            echo json_encode(array('status' => 'success'));
            exit();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to delete channel.'));
            exit();
        }
    }
}

// Handle create channel request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'create_channel') {
    if (isset($_POST['server_id']) && isset($_POST['new_channel'])) {
        $serverId = $_POST['server_id'];
        $newChannel = $_POST['new_channel'];
        if (createChannel($serverId, $newChannel)) {
            echo json_encode(array('status' => 'success'));
            exit();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to create channel.'));
            exit();
        }
    }
}

// Handle delete server request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_server') {
    if (isset($_POST['server_id'])) {
        $serverId = $_POST['server_id'];
        if (deleteServer($serverId)) {
            echo json_encode(array('status' => 'success'));
            exit();
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to delete server.'));
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Server Settings</title>
    <style>
        .container {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input[type="text"], .form-group button {
            padding: 8px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 3px;
        }
        .form-group button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-group button:hover {
            background-color: #45a049;
        }
        .channel-list {
            list-style-type: none;
            padding: 0;
        }
        .channel-list li {
            margin-bottom: 10px;
        }
        .delete-channel-btn, .delete-server-btn {
            padding: 5px 10px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s;
        }
        .delete-channel-btn:hover, .delete-server-btn:hover {
            background-color: #da190b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Server Settings</h2>
        <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>

        <h3>Create New Channel</h3>
        <form id="create-channel-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="action" value="create_channel">
            <input type="hidden" name="server_id" value="<?php echo isset($_GET['server_id']) ? htmlspecialchars($_GET['server_id']) : ''; ?>">
            <div class="form-group">
                <label for="new_channel">Channel Name:</label>
                <input type="text" id="new_channel" name="new_channel" required>
            </div>
            <div class="form-group">
                <button type="submit">Create Channel</button>
            </div>
        </form>

        <h3>Channels</h3>
        <ul class="channel-list">
            <?php
            if (isset($_GET['server_id'])) {
                $serverId = $_GET['server_id'];
                $serverPath = $serversDir . $serverId . "/";
                if (is_dir($serverPath)) {
                    $files = glob($serverPath . "*.txt");
                    foreach ($files as $file) {
                        $channelName = pathinfo($file, PATHINFO_FILENAME);
                        echo '<li>';
                        echo htmlspecialchars($channelName);
                        echo ' <form class="delete-channel-form" action="' . htmlspecialchars($_SERVER["PHP_SELF"]) . '" method="post">';
                        echo '<input type="hidden" name="action" value="delete_channel">';
                        echo '<input type="hidden" name="server_id" value="' . htmlspecialchars($serverId) . '">';
                        echo '<input type="hidden" name="channel_name" value="' . htmlspecialchars($channelName) . '">';
                        echo '<button type="submit" class="delete-channel-btn">Delete</button>';
                        echo '</form>';
                        echo '</li>';
                    }
                }
            }
            ?>
        </ul>

        <br>
        <!-- Delete Server Form -->
<form id="delete-server-form" method="post" action="delete-server.php">
    <input type="hidden" name="action" value="delete_server">
    <input type="hidden" name="server_id" value="server_id_here">
    <button type="button" class="delete-server-btn">Delete Server</button>
</form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Delete Server Button Click Handler
    $(document).on('click', '.delete-server-btn', function(e) {
        e.preventDefault();
        var form = $('#delete-server-form');
        var formData = form.serialize();
        
        if (confirm("Are you sure you want to delete this server? This action cannot be undone.")) {
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        alert("Server deleted successfully.");
                        window.location.href = 'index.php#servers'; // Redirect to home after deletion
                    } else {
                        alert("Failed to delete server: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Failed to delete server: " + error);
                }
            });
        }
    });
});
</script>


        <br>
        <a href="server.php?server_id=<?php echo $serverId; ?>">Back to Server</a>
    </div>

    <script>
    $(document).ready(function() {
        // Delete channel form submission handler
        $(document).on('submit', '.delete-channel-form', function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        alert("Channel deleted successfully.");
                        location.reload(); // Refresh page after deletion
                    } else {
                        alert("Failed to delete channel: " + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert("Failed to delete channel: " + error);
                }
            });
        });

</script>
</body>
</html>
