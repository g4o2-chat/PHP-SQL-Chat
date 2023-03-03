<?php
require_once "pdo.php";
require_once "head.php";
date_default_timezone_set('Asia/Taipei');

if (!isset($_SESSION["email"])) {
    include 'head.php';
    echo "<p align='center'>PLEASE LOGIN</p>";
    echo "<br />";
    echo "<p align='center'>Redirecting in 3 seconds</p>";
    header("refresh:3;url=login.php");
    die();
}

if (isset($_SESSION['email'])) {
    $stmt = $pdo->query("SELECT * FROM account");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM account WHERE user_id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pfpsrc_default = './assets/images/default-user-square.png';

    if ($user[0]['pfp'] != null) {
        $userpfp = $user[0]['pfp'];
    } else {
        $userpfp = $pfpsrc_default;
    }
    $stmt = $pdo->prepare("SELECT * FROM friendship_status where addressee_id =?");
    $stmt->execute([$_SESSION['user_id']]);
    $friendship_requests = $stmt->fetchAll();
    // print_r($friendship_requests);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>G4o2 Chat</title>
</head>

<body>
    <main>
        <?php
        require_once "navbar.php";
        echo "<h1>Friend requests</h1>";
        if (!$friendship_requests) {
            echo "<p>no friend requests</p>";
            echo "<span>Send one <a href='./users.php'>here</a></span>";
        } else {
            foreach ($friendship_requests as $friendship_request) {
                $stmt = $pdo->prepare("SELECT * FROM account WHERE user_id=?");
                $stmt->execute([$friendship_request['requester_id']]);
                $friendship_requester = $stmt->fetch(PDO::FETCH_ASSOC);

                $last_online = $friendship_request['specified_date_time'];
                $current_date_time = date(DATE_RFC2822);
                $last_online = new DateTime($last_online);
                $current_date_time = new DateTime($current_date_time);
                $diff = $current_date_time->diff($last_online)->format("last online %a days %h hours and %i minutes ago");
                $exploded = explode(" ", $diff);
                
                if ($exploded[2] == "1") {
                    $diff = "<p class='text-primary'>$exploded[2]&nbsp;day&nbsp;ago</p>";
                } elseif ($exploded[4] == "1") {
                    $diff = "<p class='text-primary'>$exploded[4]&nbsp;hour&nbsp;ago</p>";
                } elseif ($exploded[7] == "1") {
                    $diff = "<p class='text-primary'>$exploded[7]&nbsp;minute&nbsp;ago</p>";
                } elseif ($exploded[2] !== "0") {
                    $diff = "<p class='text-primary'>$exploded[2]&nbsp;days&nbsp;ago</p>";
                } elseif ($exploded[4] !== "0") {
                    $diff = "<p class='text-primary'>$exploded[4]&nbsp;hours&nbsp;ago</p>";
                } elseif ($exploded[7] !== "0") {
                    $diff = "<p class='text-primary'>$exploded[7]&nbsp;minutes&nbsp;ago</p>";
                } else {
                    $diff = "<p class='text-primary'>Just now</p>";
                }
                
                echo "<h6>New friend request from <a href='profile.php?id=".$friendship_requester['user_id']."'>{$friendship_requester['username']}</a></h6>";
                echo $diff;
            }
        }
        ?>
    </main>
</body>

</html>