<?php
function getUsers() {
    $usersFile = 'users.json';
    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, json_encode([]));
    }
    return json_decode(file_get_contents($usersFile), true);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $users = getUsers();

    foreach ($users as $user) {
        if ($user['username'] == $username && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        }
    }
    echo "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Aurora Chat - Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post" action="login.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
<br>
<br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
<br>
<br>
            <button type="submit">Login</button>
<br>
<br>
	    <a href="signup.php"> Create an account </a>
        </form>
    </div>
</body>
</html>
