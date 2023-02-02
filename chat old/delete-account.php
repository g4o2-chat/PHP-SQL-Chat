<?php
session_start();
require_once "pdo.php";
require_once "head.php";
date_default_timezone_set('Asia/Taipei');

if (!isset($_SESSION["email"])) {
    echo "<p class='die-msg'>PLEASE LOGIN</p>";
    echo '<link rel="stylesheet" href="./style.css?v=<?php echo time(); ?>">';
    echo "<br />";
    echo "<p class='die-msg'>Redirecting in 3 seconds</p>";
    header("refresh:3;url=index.php");
    die();
}
if ($_SESSION['email'] == 'guest@guest.com') {
    echo "<p class='die-msg'>LOGGED IN AS GUEST ACCOUNT</p>";
    echo "<p class='die-msg'>EDIT ACCOUNT DETAILS NOT ALLOWED</p>";
    echo '<link rel="stylesheet" href="./style.css?v=<?php echo time(); ?>">';
    echo "<br />";
    echo "<p class='die-msg'>Redirecting in 3 seconds</p>";
    header("refresh:3;url=index.php");
    die();
}
if (isset($_SESSION["email"])) {
    $statement = $pdo->prepare("SELECT * FROM account where user_Id = :usr");
    $statement->execute(array(':usr' => $_SESSION['user_id']));
    $response = $statement->fetch();
    $pfpsrc_default = './img/default-pfp.png';

    if ($response['pfp'] != null) {
        $userpfp = $response['pfp'];
    } else {
        $userpfp = $pfpsrc_default;
    }
    $pfp = "<a class='pfp-link' href='./profile.php?user={$_SESSION['user_id']}'><img class='profile-img-large' src='$userpfp'></a>";
    $main = "<p id='profile-name'>{$_SESSION['name']}</p><p id='profile-email'>{$_SESSION['email']}</p>";
    $profileLink = "<a href='./profile.php?user={$_SESSION['user_id']}'>Your public profile</a>";
    $actions = '<a href="edit-account.php">Edit Account</a> | <a href="logout.php">Logout</a>';
    echo "<div id='profile'><button id='close-btn' onclick='closeProfile()'>&times;</button>{$pfp}{$main}{$actions}<br />{$profileLink}</div>";
    echo "<button id='close-btn-two' onclick='openProfile()'><img class='user-pfp' alt='user-logo' src='{$userpfp}'></button>";
}

if(isset($_POST['delete'])) {
    $sql = "DELETE FROM account WHERE user_id = :uid";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':uid' => $_SESSION['user_id']));
    $_SESSION['success'] = 'Account deleted';
    session_destroy();
    header('Location: ./login.php');
    return;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Account</title>
    <link rel="stylesheet" href="./css/edit-account.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="./css/login.css?v=<?php echo time(); ?>">
</head>

<body>
    <div id="mySidenav" style="z-index: 999999;" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <p class="rainbow_text_animated">g4o2</p>
        <a href="./chat.php">chat</a>
        <a href="https://github.com/g4o2" target="_blank">github</a>
        <a href="./login.php">Login</a>
        <p class='rainbow_text_animated'>Dev</p>
        <a href="https://maxhu787.github.io" target="_blank">Hu Kaixiang</a>
        <a href="https://github.com/maxhu787" target="_blank">maxhu787</a>
    </div>
    <header class="navbar-header">
        <div class="navbar-container">
            <div>
                <a href="./index.php">
                    <img class="logo" alt="logo" src="./favicon.ico">
                </a>
            </div>
            <nav>
                <ul>
                    <li><a href="./chat.php">Chat</a></li>
                    <li><a href="https://maxhu787.github.io" target="_blank">G4o2</a></li>
                    <li><a href="https://github.com/g4o2" target="_blank">Github</a></li>
                    <button id="sideNav-button" onclick="openNav()">☰</button>
                </ul>
            </nav>
        </div>
    </header>

    <div id="particles-js"></div>
    <div class="center" style='margin-top: 60px;'>
        <form action="delete-account.php" method="post" enctype="multipart/form-data">
            <p>Delete Account?</p>
            <input type="submit" value="Delete Account" name="delete" class="btn">
            <br />
            <div style="float:left;">
                <a href="./index.php">Cancel</a><br />
            </div>
        </form>
    </div>
</body>

</html>