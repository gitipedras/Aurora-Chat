<?php
function getUsers() {
    $usersFile = 'users.json';
    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, json_encode([]));
    }
    return json_decode(file_get_contents($usersFile), true);
}

function saveUsers($users) {
    file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $users = getUsers();

    foreach ($users as $user) {
        if ($user['username'] == $username) {
            echo "Username already exists.";
            exit();
        }
    }

    $users[] = ['username' => $username, 'password' => $password];
    saveUsers($users);

    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aurora Chat - Signup</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Signup</h2>
        <form method="post" action="signup.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
<br>
<br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
<br>
<br>
            <button type="submit">Signup</button>
<br>
<br>
	    <a href="login.php"> Login </a>
        </form>
    </div>
</body>
</html>
