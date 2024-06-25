<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$auroraversion = "v0.1.8";
$usersFile = "users.json";

function getUsers() {
    global $usersFile;
    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, json_encode([]));
    }
    $users = json_decode(file_get_contents($usersFile), true);
    return is_array($users) ? $users : [];
}

function saveUsers($users) {
    global $usersFile;
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

$username = $_SESSION['username'];
$users = getUsers();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['new_username'])) {
        $newUsername = $_POST['new_username'];

        if (empty($newUsername)) {
            $error = "New username cannot be empty.";
        } elseif (isset($users[$newUsername])) {
            $error = "New username already exists.";
        } else {
            // Update username in users.json
            $users[$newUsername] = $users[$username];
            unset($users[$username]);
            saveUsers($users);

            // Update the session username
            $_SESSION['username'] = $newUsername;

            // Rename the user's friends file
            $oldFriendsFile = "data/friends-$username.json";
            $newFriendsFile = "data/friends-$newUsername.json";
            if (file_exists($oldFriendsFile)) {
                rename($oldFriendsFile, $newFriendsFile);
            }

            // Update the username in other related files or databases if necessary

            // Redirect to the homepage or another page
            header("Location: index.php");
            exit();
        }
    } else {
        $error = "New username is required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Change Username</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
  
 

 

 <div class="container">
        <h2>Change Username</h2>
<a href="index.php">Back to home</a>
<p class="warning"> This feature has been tested and only changes your username temporarily, it will not be permanent do to bugs </p>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="change-username.php">
            <label for="new_username">New Username:</label>
            <input type="text" id="new_username" name="new_username" required>
            <br><br>
            <button type="submit">Change Username</button>
        </form>
    </div>
</body>
</html>
