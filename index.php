<?php
session_start();

require_once "pdo.php";
$stmt = $pdo->query("SELECT make, model, year, mileage, autos_id FROM autos ORDER BY make");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtuser = $pdo->query("SELECT * FROM account");
$users = $stmtuser->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['logout'])) {
    header('Location: logout.php');
    return;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Database</title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <link rel="stylesheet" href="./style.css?v=<?php echo time(); ?>">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Alumni+Sans+Pinstripe&family=Montserrat:wght@300&family=Orbitron&family=Work+Sans:wght@300&display=swap');

        .container {
            margin-left: 40px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        #profile {
            position: fixed;
            right: 10px;
            top: 10px;
            background-color: rgb(41, 41, 41);
            padding: 40px;
            text-align: center;
            transition: opacity .3s ease-in;
        }

        #close-btn:hover {
            background-color: transparent !important;
            transition: all .1s ease-in !important;
        }

        #close-btn-two:hover {
            background-color: transparent !important;
            transition: all .1s ease-in !important;
        }

        .btn {
            font-family: Arial, Helvetica, sans-serif;
            text-decoration: none;
            color: #ffa500;
            background-color: rgba(41, 41, 41, 1);
            padding: 8px;
            border: none;
            font-size: 14px;
            cursor: pointer;
            transition: all .15s ease-in;
        }

        .btn:hover {
            color: #fff;
        }

        .btn:active {
            background-color: transparent;
        }

        #announcements {
            height: 20vh;
            bottom: 0;
            width: 99vw;
            position: fixed;
            border-radius: 10px;
            padding: 20px 0px 20px 30px;
            background-color: rgba(0, 0, 0, 0.6);
            text-align: center;
            overflow: auto;
        }

        #announcements::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            background-color: #F5F5F5;
        }

        #announcements::-webkit-scrollbar {
            width: 10px;
            background-color: #F5F5F5;
        }

        #announcements::-webkit-scrollbar-thumb {
            background-color: #F90;
            background-image: -webkit-linear-gradient(45deg,
                    rgba(255, 255, 255, .2) 25%,
                    transparent 25%,
                    transparent 50%,
                    rgba(255, 255, 255, .2) 50%,
                    rgba(255, 255, 255, .2) 75%,
                    transparent 75%,
                    transparent)
        }

        table {
            border-radius: 8px;
            background-color: rgba(41, 41, 41, .7);
        }

        th {
            border: solid 2px orange;
            padding: 20px;
        }

        td {
            padding: 5px;
            color: #ffa500;
        }

        footer {
            height: 20vh;
            bottom: 0;
            width: 100vw;
            border-radius: 10px;
            padding: 20px 0px 20px 30px;
            background-color: rgba(0, 0, 0, 0.6);
            text-align: center;
        }

        #chat-btn {
            display: inline-block;
            font-family: orbitron;
            font-size: 25px;
            color: #ffa500;
            padding: 8px;
            border-radius: 8px;
            background-color: rgba(41, 41, 41, .8);
            transition: color .2s ease-in-out;
        }

        #chat-btn:hover {
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class='rainbow_text_animated'>Welcome to g4o2-chat</h1>
        <?php
        if (!isset($_SESSION['email'])) {
            echo '<p><a href="login.php">Please log in</a></p>';
            echo '<p>Attempt to <a href="add.php">add data</a> without logging in</p>';
        }
        if (isset($_SESSION["success"])) {
            echo ('<p style="color:green;background:rgba(41, 41, 41, 0.8);width: 90px;border-radius:10px;padding:10px;">' . htmlentities($_SESSION["success"]) . "</p>\n");
            unset($_SESSION["success"]);
        }
        if (isset($_SESSION["error"])) {
            echo ('<p style="color:red;  background:rgba(41, 41, 41, 0.8);width: 90px;border-radius:10px;padding:10px;">' . htmlentities($_SESSION["error"]) . "</p>\n");
            unset($_SESSION["error"]);
        }
        ?>
        <?php
        if (isset($_SESSION['email'])) {
            echo '<table border="1">
            <thead>
            <tr><th>Make</th><th>Model</th><th>Year</th><th>Mileage</th><th>Action</th></tr></thead>';
            foreach ($rows as $row) {
                echo "<tr><td>";
                echo ($row['make']);
                echo ("</td><td>");
                echo ($row['model']);
                echo "<td>";
                echo ($row['year']);
                echo ("</td><td>");
                echo ($row['mileage']);
                echo ("</td><td>");
                echo ('<a href="edit.php?autos_id=' . $row['autos_id'] . '">Edit</a> / ');
                echo ('<a href="delete.php?autos_id=' . $row['autos_id'] . '">Delete</a>');
                echo ("</td></tr>\n");
                echo ("</td></tr>\n");
            }
            echo "</table>";
        }
        if (isset($_SESSION['email'])) {
            echo '<p><a href="add.php">Add New Entry</a><br></p><a id="chat-btn" href="chat.php">CHAT</a></p>';
        }
        if (isset($_SESSION['email'])) {
            echo '<table border="1">
            <thead>
            <tr><th>user_id</th><th>Name</th><th>Email</th><th>Password</th><th>Status</th></tr></thead>';
            foreach ($users as $user) {
                $pfpsrc = './default-pfp.png';
                if ($user['pfp'] != null) {
                    $pfpsrc = $user['pfp'];
                }
                $pfp = "<img style='margin-left: 10px;' class='profile-img' src='$pfpsrc'>";



                echo "<tr><td>";
                echo ($user['user_id']);
                echo $pfp;
                echo ("</td><td>");
                echo "<a href='./profile.php?user={$user['name']}' >" . $user['name'] . "</a>";
                echo "<td>";
                echo ($user['email']);
                echo ("</td><td>");
                echo ($user['password']);
                echo ("</td><td>");
                echo "Undefined";
                echo ("</td></tr>\n");
                echo ("</td></tr>\n");
            }
            echo "</table>";
        }
        ?>
    </div>
    <?php
    if (isset($_SESSION['email'])) {
        $pfpsrc = './default-pfp.png';

        $stmta = $pdo->prepare("SELECT * FROM account WHERE user_id=?");
        $stmta->execute([$_SESSION['user_id']]);
        $pfptemp = $stmta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pfptemp as $test) {
            if ($test['pfp'] != null) {
                $pfpsrc = $test['pfp'];
            }
            $_SESSION['name'] = $test['name'];
            $_SESSION['email'] = $test['email'];
        }
        $pfp = "<img class='profile-img-large' src='$pfpsrc'>";
        $main = "<p style='margin-top: 20px;font-size: 20px;font-family: monospace;'>{$_SESSION['name']}</p><p style='font-family: monospace;'>{$_SESSION['email']}</p>";
        $profileLink = "<a href='./profile.php?user={$_SESSION['name']}'>Your public profile</a>";
        $actions = '<a href="edit-account.php">Edit Account</a> | <a href="logout.php">Logout</a>';
        echo "<div style='border-radius: 12px;' id='profile'><button id='close-btn' onclick='closeProfile()' style='background-color: rgb(71, 71, 71);border:none;position:absolute;top:0;left:0;font-size: 18px;padding:5px 12px 5px 12px;'>&times;</button>{$pfp}{$main}{$actions}<br />{$profileLink}</div>";
        echo "<button id='close-btn-two' onclick='openProfile()' style='background-color: rgb(71, 71, 71);border:none;position:absolute;top:10px;right:10px;font-size: 18px;padding:5px 12px 5px 12px;opacity: 0;'>&#9776;</button>";
    }
    ?>
    <script>
        function closeProfile() {
            document.getElementById("profile").style.opacity = '0';
            document.getElementById("close-btn").style.opacity = '0';
            document.getElementById("close-btn-two").style.opacity = '1';
        }

        function openProfile() {
            document.getElementById("profile").style.opacity = '1';
            document.getElementById("close-btn").style.opacity = '1';
            document.getElementById("close-btn-two").style.opacity = '0';
        }
    </script>
    <div id="announcements">
        <h3 style="font-family: orbitron;">Announcements</h3><br />
        <h4>Site creation! First line of code written&#127881;&#127881;&#127881; <code>2022/8/23</code></h4>
        <h4>Login system working! 8 created accounts <code>2022/8/24</code></h4>
        <h4>Chat now available&#128526; <code>2022/8/25</code></h4>
        <h4>Profile pictures are now available <code>2022/8/26</code></h4>
        <h4>Profile system working and still being worked on for new look <code>2022/8/28</code></h4>
        <h4>New website theme/style & added user 👤 profile link table on index page <code>2022/9/11</code></h4>
    </div>
    <footer></footer>
</body>