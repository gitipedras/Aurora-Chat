<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$auroraversion = "v0.2.5";

$username = $_SESSION['username'];
$friendsFile = "data/friends-$username.json";
$usersFile = "users.json";
$chatsDir = "chats/";
$serversFile = "data/servers.json";
$chatsFile = "data/chats.json";

function getFriends($file) {
    if (!file_exists($file)) {
        file_put_contents($file, json_encode([]));
    }
    $friends = json_decode(file_get_contents($file), true);
    return is_array($friends) ? $friends : [];
}

function saveFriends($file, $friends) {
    file_put_contents($file, json_encode($friends, JSON_PRETTY_PRINT));
}

function getUsers() {
    global $usersFile;
    if (!file_exists($usersFile)) {
        file_put_contents($usersFile, json_encode([]));
    }
    $users = json_decode(file_get_contents($usersFile), true);
    return is_array($users) ? $users : [];
}

function generateRandomChatId() {
    return uniqid();
}

function saveChat($chatId, $creator, $participants) {
    global $chatsDir, $chatsFile;
    $chatFile = $chatsDir . "$chatId.json";
    $timestamp = date('Y-m-d H:i:s');
    $content = [
        'timestamp' => $timestamp,
        'creator' => $creator,
        'participants' => $participants,
    ];
    file_put_contents($chatFile, json_encode($content, JSON_PRETTY_PRINT));
    
    $chats = getChats();
    $chats[$chatId] = [
        'owner' => $creator,
        'participants' => $participants,
    ];
    file_put_contents($chatsFile, json_encode($chats, JSON_PRETTY_PRINT));
    
    foreach ($participants as $participant) {
        if ($participant !== $creator) {
            $friendFile = "data/friends-$participant.json";
            $friends = getFriends($friendFile);
            if (!in_array($creator, $friends)) {
                $friends[] = $creator;
                saveFriends($friendFile, $friends);
            }
        }
    }
}

function getChats() {
    global $chatsFile;
    if (!file_exists($chatsFile)) {
        file_put_contents($chatsFile, json_encode([]));
    }
    $chats = json_decode(file_get_contents($chatsFile), true);
    return is_array($chats) ? $chats : [];
}

function isChatCreatedByUser($chatId, $username) {
    $chats = getChats();
    return isset($chats[$chatId]['owner']) && $chats[$chatId]['owner'] === $username;
}

function isUserParticipant($chatId, $username) {
    $chats = getChats();
    return isset($chats[$chatId]['participants']) && in_array($username, $chats[$chatId]['participants']);
}


$username = $_SESSION['username'];
$serversDir = "servers/";

// Function to create a server
function createServer($serverName, $owner) {
    global $serversDir;

    // Generate server ID based on server name
    $serverId = strtolower(str_replace(' ', '_', $serverName));

    // Create server directory
    $serverPath = $serversDir . $serverId;
    if (!is_dir($serverPath)) {
        mkdir($serverPath, 0777, true);
    }

    // Create "info" channel file
    $infoFilePath = $serverPath . "/info.txt";
    file_put_contents($infoFilePath, "Welcome to the Info Channel");

    // Setup pem.json
    $pemFile = $serverPath . "/pem.json";
    $permissions = [
        'owner' => $owner,
        'admins' => []
    ];
    file_put_contents($pemFile, json_encode($permissions, JSON_PRETTY_PRINT));

    // Add server to data/servers.json
    $servers = getServers();
    $servers[$serverId] = [
        'id' => $serverId,
        'name' => $serverName,
        'owner' => $owner,
        'admins' => []
    ];
    file_put_contents("data/servers.json", json_encode($servers, JSON_PRETTY_PRINT));
}

// Handle form submission to create server
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_server'])) {
    $serverName = $_POST['server_name'];

    // Create server with current user as owner
    createServer($serverName, $username);

    // Redirect to server.php with server name or ID
    header("Location: server.php?server_id=" . urlencode($serverName));
    exit();
}

function getServers() {
    global $serversDir;
    $servers = [];
    
    if (is_dir($serversDir)) {
        $serverFolders = array_diff(scandir($serversDir), ['.', '..']);
        foreach ($serverFolders as $serverFolder) {
            if (is_dir($serversDir . $serverFolder)) {
                // Assuming the server name is the same as the folder name
                $servers[$serverFolder] = $serverFolder;
            }
        }
    }
    
    return $servers;
}

$users = getUsers();
$friends = getFriends($friendsFile);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_friend'])) {
    $friendName = $_POST['friend_name'];

    if (empty($friendName)) {
        $error = "Friend name cannot be empty.";
    } elseif ($friendName == $username) {
        $error = "You cannot add yourself as a friend.";
    } elseif (!in_array($friendName, array_column($users, 'username'))) {
        $error = "User does not exist.";
    } elseif (in_array($friendName, $friends)) {
        $error = "User is already your friend.";
    } else {
        $friends[] = $friendName;
        saveFriends($friendsFile, $friends);
        header("Location: index.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_chat'])) {
    $selectedFriends = isset($_POST['friends']) ? $_POST['friends'] : [];
    $selectedFriends[] = $username;

    $chatId = generateRandomChatId();
    saveChat($chatId, $username, $selectedFriends);

    header("Location: chat.php?chat_id=" . urlencode($chatId));
    exit();
}


$chats = getChats();
$createdChats = [];
$participatingChats = [];
foreach ($chats as $chatId => $chatInfo) {
    if ($chatInfo['owner'] === $username) {
        $createdChats[] = $chatId;
    }
    if (in_array($username, $chatInfo['participants'])) {
        $participatingChats[] = $chatId;
    }
}

$servers = getServers(); // Fetch servers here to avoid undefined variable

?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aurora Chat - Home</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
   <style>
.accordion {
  background-color: #a25fcc;
  color: #fff;
  cursor: pointer;
  padding: 18px;
  width: 50%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
    font-family: Poppins, Segoe UI, sans-serif;
  background-color: #a25fcc; 
}

.panel {
  padding: 0 18px;
  display: none;
  background-color: #fff;
  overflow: hidden;
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
    <br>
 <div class="ctl2">
      <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        <a href="logout.php">Logout</a>
        <br><br>
        <div class="tabs">
            <button class="tablink" onclick="openTab('Home', this)">Home</button>
            <button class="tablink" onclick="openTab('Friends', this)">Friends</button>
            <button class="tablink" onclick="openTab('Chats', this)">Chats</button>
            <!--<button class="tablink" onclick="openTab('Servers', this)">Servers</button>-->
	    <button class="tablink" onclick="openTab('Settings', this)">Settings <!--<span class="new">!</span>--></button>
        </div>


 <div id="Settings" class="tabcontent">
	    <h3>Settings</h3>
<div class="error-box">
<p> <b style="font-family:'Bungee Spice';">W</b>arning: features in red are beta and do not work</p>
  	    <p> Username: <b><?php echo $username; ?> </b> <button class="settings-edit"><a class="settings-edit" href="change-username.php">&#9998;</a></button></p>
	    <p> Password: <button class="settings-edit"><a class="settings-edit" href="change-password.php">&#9998;</a></button></p>
	    <p> Delete Account:  <button class="settings-edit"><a class="settings-edit" href="delete-account.php">&#128465;</a></button></p>
   </div>   
        </div>



        <div id="Home" class="tabcontent">

           <h3>Home</h3>
  	    <p> Logged in as: <b><?php echo $username; ?> </b></p>
            <p> Aurora Version: <b style="color:green;"><?php echo $auroraversion; ?></b></p>
            <p> Chats directory: <?php echo $chatsDir; ?></p>
            <p> servers file: <?php echo $serversFile; ?></p>
	    <p> Github: <a href="https://github.com/gitipedras/Aurora-Chat">here</a></p>
	    <p> Discord: <a href="https://github.com/gitipedras/Aurora-Chat/wiki/Discord-(Updated-Link)">here</a></p>
           
        </div>

        <div id="Friends" class="tabcontent">
            <h3>Friends</h3>
            <form method="post" action="index.php">
                <label for="friend_name">Add Friend:</label>
                <input type="text" id="friend_name" name="friend_name" required>
                <button type="submit" name="add_friend">Add Friend</button>
            </form>
            <?php if (isset($error)): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <h4>Your Friends:</h4>
            <ul>
                <?php foreach ($friends as $friend) : ?>
                    <li><?php echo htmlspecialchars($friend); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div id="CreateChat" class="tabcontent">
            <h3>Create New Chat</h3>
            <form method="post" action="index.php">
                <label for="friends">Select Friends:</label><br>
                <select id="friends" name="friends[]" multiple required>
                    <?php foreach ($friends as $friend) : ?>
                        <option value="<?php echo htmlspecialchars($friend); ?>"><?php echo htmlspecialchars($friend); ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <button type="submit" name="create_chat">Create Chat</button>
            </form>
        </div>



        <div id="Chats" class="tabcontent">

<button class="accordion">Chats</button>
<div class="panel">
    <h3>Chats</h3>
            <h4>Chats Created by You:</h4>
            <ul>
                <?php foreach ($createdChats as $chatId) : ?>
                    <li>
                        <a href="chat.php?chat_id=<?php echo urlencode($chatId); ?>">
                            <?php echo htmlspecialchars($chatId); ?>
                        </a>
                        <form method="post" action="delete_chat.php">
                            <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars($chatId); ?>">
                            <button type="submit" name="delete_chat">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h4>Chats You Participate In:</h4>
            <ul>
                <?php foreach ($participatingChats as $chatId) : ?>
                    <li>
                        <a href="chat.php?chat_id=<?php echo urlencode($chatId); ?>">
                            <?php echo htmlspecialchars($chatId); ?>
                        </a>
                        <form method="post" action="delete_chat.php">
                            <input type="hidden" name="chat_id" value="<?php echo htmlspecialchars($chatId); ?>">
                            <button type="submit" name="delete_chat">Delete</button>
                        </form>
                    </li>
                <?php endforeach; ?>
            </ul>
</div>

<button class="accordion">Open Chat by ID</button>
<div class="panel">
  <h3>Open Chat by ID</h3>
            <form method="get" action="chat.php">
                <label for="chat_id">Enter Chat ID:</label><br>
                <input type="text" id="chat_id" name="chat_id" required><br><br>
                <button type="submit">Open Chat</button>
            </form>
</div>

<button class="accordion">Create Chat</button>
<div class="panel">
   <h3>Create New Chat</h3>
            <form method="post" action="index.php">
                <label for="friends">Select Friends:</label><br>
                <select id="friends" name="friends[]" multiple required>
                    <?php foreach ($friends as $friend) : ?>
                        <option value="<?php echo htmlspecialchars($friend); ?>"><?php echo htmlspecialchars($friend); ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <button type="submit" name="create_chat">Create Chat</button>
            </form>
</div>
         
 
        </div>

        <div id="Servers" class="tabcontent">
        <h3>Servers</h3>
        <form method="post" action="index.php">
            <label for="server_name">Create New Server:</label><br>
            <input type="text" id="server_name" name="server_name" required><br><br>
            <button type="submit" name="create_server">Create Server</button>
        <h4>All Servers:</h4>
    <ul>
        <?php foreach ($servers as $serverName => $serverFolder) : ?>
            <li>
                <a href="server.php?server_id=<?php echo urlencode($serverName); ?>">
                    <?php echo htmlspecialchars($serverName); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    </div>
</div>
</form>
</div>

<script>
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}


</script>
    <script>
        function openTab(tabName, elmnt) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablink");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(tabName).style.display = "block";
            elmnt.className += " active";
        }

        document.getElementsByClassName("tablink")[0].click(); // Click the first tab by default on page load
    </script>
</body>
</html>
