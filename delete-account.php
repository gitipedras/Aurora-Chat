<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$usersFile = "users.json";
$username = $_SESSION['username'];

// Function to get users from JSON file
function getUsers() {
    global $usersFile;
    if (!file_exists($usersFile)) {
        return []; // Return empty array if file doesn't exist
    }
    $usersJson = file_get_contents($usersFile);
    return json_decode($usersJson, true); // Decode JSON into associative array
}

// Function to save users to JSON file
function saveUsers($users) {
    global $usersFile;
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
}

// Get current users
$users = getUsers();

// Check if the form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    // Optionally verify password or other security checks here

    // Remove user from array
    if (isset($users[$username])) {
        unset($users[$username]);
        saveUsers($users);

        // Redirect or show confirmation message
        header("Location: account-deleted.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
<link rel="stylesheet" type="text/css" href="styles.css">
    <title>Delete Account</title>
</head>
<body>
<div class="container">
    <h2>Delete Your Account</h2>
    <p>Are you sure you want to delete your account, <?php echo htmlspecialchars($username); ?>?</p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="confirm_delete" value="true">
        <button type="submit">Yes, Delete My Account</button>
    </form>
    <p><a href="index.php">Cancel and Go Back</a></p>
</div>
</body>
</html>
