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
    <style>
.container {
background-image: url('background.png');
    background-size: 100% 100%; /* Adjusts the background image to cover the entire div */
    background-position: center; /* Centers the background image */
    background-repeat: no-repeat; /* Prevents the background image from repeating */
    height: 500px; /* Makes the div take up the full viewport height */

    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 20px auto;
color: white;

}

.container a {
color: white;
}

.container input[type=text] {
background-color: #fff;
}

.container input[type=password] {
background-color: #fff;
}
</style>
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
