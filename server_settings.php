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

// Check if server_id is provided in URL
if (!isset($_GET['server_id'])) {
    die("Server ID not specified.");
}

$serverId = $_GET['server_id'];

// Function to delete server
function deleteServer($serverId) {
    global $serversDir, $serversJsonFile;
    $serverPath = $serversDir . $serverId;

    // Remove server directory recursively
    if (is_dir($serverPath)) {
        array_map('unlink', glob("$serverPath/*.*"));
        rmdir($serverPath);

        // Remove server from servers.json
        $servers = getServers();
        if (isset($servers[$serverId])) {
            unset($servers[$serverId]);
            saveServers($servers);
        }
        
        return true;
    }
    return false;
}

// Function to get servers from JSON file
function getServers() {
    global $serversJsonFile;
    if (!file_exists($serversJsonFile)) {
        file_put_contents($serversJsonFile, json_encode([]));
    }
    $servers = json_decode(file_get_contents($serversJsonFile), true);
    return is_array($servers) ? $servers : [];
}

// Function to save servers to JSON file
function saveServers($servers) {
    global $serversJsonFile;
    file_put_contents($serversJsonFile, json_encode($servers, JSON_PRETTY_PRINT));
}

// Handle delete server request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_server') {
    if (deleteServer($serverId)) {
        echo json_encode(array('status' => 'success'));
        exit();
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Failed to delete server.'));
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Server Settings</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <script>
    $(document).ready(function() {
        // Handle delete server button click
        $('#delete-server-btn').click(function(e) {
            e.preventDefault();
            if (confirm("Are you sure you want to delete this server?")) {
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'delete_server'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            alert("Server deleted successfully.");
                            window.location.href = "index.php"; // Redirect to main page or appropriate location
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
</head>
<body>
    <div class="container">
        <h2>Server Settings</h2>
        <h3>Server ID: <?php echo htmlspecialchars($serverId); ?></h3>
        
        <div>
            <h3>Delete Server</h3>
            <button id="delete-server-btn">Delete Server</button>
        </div>
        
        <br>
        <a href="server.php?server_id=<?php echo urlencode($serverId); ?>">Back to Server</a>
    </div>
</body>
</html>
