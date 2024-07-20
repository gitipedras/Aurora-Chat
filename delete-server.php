<?php
header('Content-Type: application/json');

// Function to delete the server
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

// Handle delete server request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete_server') {
    if (isset($_POST['server_id'])) {
        $serverId = $_POST['server_id'];
        if (deleteServer($serverId)) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Failed to delete server.'));
        }
        exit();
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Server ID not provided.'));
        exit();
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid request.'));
    exit();
}
?>
