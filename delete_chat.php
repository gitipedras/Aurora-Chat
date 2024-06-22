<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$chatsFile = "data/chats.json";
$chatsDir = "chats/";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_chat'])) {
    // Check if chat_id is set in the POST data
    if (isset($_POST['chat_id'])) {
        $chatId = $_POST['chat_id'];
        
        // Get the current chats from JSON file
        $chats = getChats();
        
        // Check if the chat exists and the user has permission to delete it
        if (isset($chats[$chatId]) && $chats[$chatId]['owner'] === $username) {
            // Delete chat file from chats/CHAT_ID.txt
            $chatFile = $chatsDir . "$chatId.txt";
            if (file_exists($chatFile)) {
                unlink($chatFile);
            } else {
                // Handle case where chat file doesn't exist
                $error = "Chat file does not exist.";
            }
            
            // Remove chat from chats.json
            unset($chats[$chatId]);
            file_put_contents($chatsFile, json_encode($chats, JSON_PRETTY_PRINT));
            
            // Redirect back to index.php or any other appropriate page
            header("Location: index.php");
            exit();
        } else {
            // Handle error if chat does not exist or user doesn't have permission
            $error = "Failed to delete chat. Please try again.";
        }
    } else {
        // Handle case where chat_id is not provided in the POST data
        $error = "Chat ID is missing. Please try again.";
    }
} else {
    // Handle invalid requests
    header("Location: index.php");
    exit();
}

function getChats() {
    global $chatsFile;
    if (!file_exists($chatsFile)) {
        file_put_contents($chatsFile, json_encode([]));
    }
    $chats = json_decode(file_get_contents($chatsFile), true);
    return is_array($chats) ? $chats : [];
}
?>
