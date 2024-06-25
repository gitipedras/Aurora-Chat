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
    <title>404 - Not Found</title>
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
        <h2>Error 404</h2>
<p>Error code: <code>404</code> | <button onclick="learnmore()"> Learn More </button></p>
<p> The requested location was not found on this server</p>
    

<script>
function learnmore(message) {
    alert("You will be redirected to wikipedia.org");
    window.location.href = ' https://en.wikipedia.org/wiki/HTTP_404';
}
</script>
    </div>
</body>
</html>
