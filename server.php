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

// Function to get server channels
function getServerChannels($server_id) {
    global $serversDir;
    $serverPath = $serversDir . $server_id;
    $channels = [];

    if (is_dir($serverPath)) {
        $files = scandir($serverPath);
        foreach ($files as $file) {
            // Check if the file is a .txt file (assuming all channel files end with .txt)
            if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
                $channels[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
    }

    return $channels;
}


// Check if server_id is provided in URL parameter
if (!isset($_GET['server_id'])) {
    die("Server ID not specified.");
}

$server_id = $_GET['server_id'];
$channels = getServerChannels($server_id);

function isOwnerOrAdmin($server_id, $username) {
    global $serversDir;
    
    $pemFile = $serversDir . $server_id . "/pem.json";
    if (file_exists($pemFile)) {
        $permissions = json_decode(file_get_contents($pemFile), true);
        if ($permissions['owner'] === $username || in_array($username, $permissions['admins'])) {
            return true;
        }
    }
    return false;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Aurora Chat - Server</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
     <div class="container">
        <h2 class="server-name">Welcome to Server: <?php echo htmlspecialchars($server_id); ?></h2>
        <a href="index.php">Back to Home</a>
        <h3 class="server-name">Server Channels:</h3>
        <ul>
            <?php foreach ($channels as $channel) : ?>
                <li>
                    <a href="server-message.php?server_id=<?php echo urlencode($server_id); ?>&channel=<?php echo urlencode($channel); ?>">
                        <?php echo htmlspecialchars($channel); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <?php if (isOwnerOrAdmin($server_id, $username)) : ?>
            <a href="server_settings.php?server_id=<?php echo urlencode($server_id); ?>">Settings</a>
        <?php endif; ?>
    </div>
</body>
</html>
