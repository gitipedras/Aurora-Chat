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
    if (isset($_POST['username']) && isset($_POST['old_password']) && isset($_POST['new_password'])) {
        $inputUsername = $_POST['username'];
        $oldPassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];

        if (empty($inputUsername) || empty($oldPassword) || empty($newPassword)) {
            $error = "All fields are required.";
        } elseif ($inputUsername !== $username) {
            $error = "Entered username does not match the logged-in user.";
        } elseif (!isset($users[$inputUsername]) || $users[$inputUsername]['password'] !== $oldPassword) {
            $error = "Old password is incorrect.";
        } else {
            // Update the password in users.json
            $users[$inputUsername]['password'] = $newPassword;
            saveUsers($users);

            // Redirect to the homepage or another page
            header("Location: index.php");
            exit();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Change Password</h2>
<a href="index.php">Back to home</a>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" action="change-password.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <br><br>
            <label for="old_password">Old Password:</label>
            <input type="password" id="old_password" name="old_password" required>
            <br><br>
            <label for="new_password">New Password:</label>
            <input type="password" id="new_password" name="new_password" required>
            <br><br>
            <button type="submit">Change Password</button>
        </form>
    </div>
</body>
</html>
