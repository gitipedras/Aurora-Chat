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
      <meta name="viewport" content="width=device-width, initial-scale=1">
       <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
.container {
    font-family: Poppins, Segoe UI, sans-serif;;
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


@media screen and (max-width:600px) {
    .container {
        padding: 10px;
        max-width: 100%;
        background-image: none;
        background-color: transparent;
        border:0px solid transparent;
        border-style: none;
        box-shadow: none;
        color: black;
    }
    .container a {
        color: blue;
    }
    .tabcontent {
        background-color: transparent;
    }
    button[type="submit"], button {
        font-family: Poppins, Segoe UI, sans-serif;
        padding: 5px 10px;
        background-color: #a25fcc;
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s ease;
        margin: 3px;
        font-size: 20px;
    }

    button[type="submit"]:hover, button:hover {
        background-color: #0056b3;
    }

    a {
        font-size: 20px;
    }
}
</style>
</head>
<body>
    <div class="container">
         <noscript>
        <style type="text/css">
            .ctl2 {
                display: none;
            }
        </style>
        <h2>Javascript Error</h2>
        <p>It seems like your web browser does not have/support javascript. Please enable it or update your browser to the <b>latest</b> version.</p>
        <a href="https://support.google.com/adsense/answer/12654?hl=en">Activate for Chrome</a><br>
         <a href="https://support.microsoft.com/en-us/microsoft-edge">Activate for Microsoft Edge</a><br>
          <a href="https://support.mozilla.org/en-US/kb/javascript-settings-for-interactive-web-pages">Activate for Firefox</a><br>
           <a href="https://support.apple.com/safari">Activate for Safari</a><br>

            <a href="">Ok, It's enabled</a><br>
    </noscript>

    <div class="ctl2">
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

</div>
</body>
</html>
