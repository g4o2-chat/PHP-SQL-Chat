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

    // $stmt = $pdo->prepare("SELECT * FROM friendship_status where requester_id = :usr");
    // $stmt->execute(array(':usr' => $_SESSION['user_id']));
    // $friend_req = $stmt->fetch();
    $pfpsrc_default = './assets/images/default-user-square.png';

    if ($user[0]['pfp'] != null) {
        $userpfp = $user[0]['pfp'];
    } else {
        $userpfp = $pfpsrc_default;
    }
    /*
    if ($friend_req != null) {
        $stmt = $pdo->prepare("UPDATE user_status_log SET account=?, last_active_date_time=? WHERE user_id=?");
        $stmt->execute([$_SESSION['username'], date(DATE_RFC2822), $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO user_status_log (user_id, account, last_active_date_time) VALUES (:usr, :acc, :date)');
        $stmt->execute(
            array(
                ':usr' => $_SESSION['user_id'],
                ':acc' => $_SESSION['username'],
                ':date' => date(DATE_RFC2822)
            )
        );
    }*/

    if (isset($_POST['friend_req'])) {
        echo $_POST['friend_req'];
        $addressee_id = $_POST['friend_req'];
        $stmt = $pdo->prepare('INSERT INTO friendship_status (requester_id, addressee_id, specified_date_time, status_code, specifier_id) 
    VALUES ( :reqid, :addid, :spdt, :stat, :specid)');
        $stmt->execute(
            array(
                ':reqid' => $_SESSION['user_id'],
                ':addid' => $addressee_id,
                ':spdt' => date(DATE_RFC2822),
                ':stat' => '0',
                ':specid' => $_SESSION['user_id']
            )
        );
        $_SESSION['success'] = "Friend request sent";
        header('Location:add-friend.php');
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>G4o2 Chat</title>
    <style>
        table {
            font-size: 12px;
            /* width: 100%; */
        }
    </style>
</head>

<body>
    <main>
        <script>
            window.addEventListener("load", function() {
                $('body').fadeIn(1000);
            });
        </script>
        <?php
        require_once "navbar.php";
        echo 'User ID >>' . $_SESSION['user_id'];
        echo '
    <table class="table table-light table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th scope="col" style="background-color: #eee;">#</th>
                <th scope="col" style="background-color: #eee;">Username</th>
                <th scope="col" style="background-color: #eee;">Name</th>
                <th scope="col" style="background-color: #eee;">Email</th>
                <th scope="col" style="background-color: #eee;">Last active</th>
                <th scope="col" style="background-color: #eee;">Action</th>
            </tr>
        </thead>
        <tbody>';
        foreach ($accounts as $account) {
            if ($account['pfp'] != null) {
                $pfpsrc = $account['pfp'];
            } else {
                $pfpsrc = $pfpsrc_default;
            }

            $pfp = "<a class='pfp-link' href='./profile.php?id={$account['user_id']}'><img style='border-radius: 100px; margin-left: 10px; ' height='20px' width='20px' src='$pfpsrc'></a>";

            $statement = $pdo->prepare("SELECT * FROM user_status_log where user_Id = :usr");
            $statement->execute(array(':usr' => $account['user_id']));
            $user_status_log = $statement->fetch();
            $userStatus = ($user_status_log != null) ? $user_status_log['last_active_date_time'] : "Undefined";;

            if ($userStatus === "Undefined") {
                $diff = "<p class='text-danger'>Undefined</p>";
            } else {
                $last_online = $userStatus;
                $current_date_time = date(DATE_RFC2822);
                $last_online = new DateTime($last_online);
                $current_date_time = new DateTime($current_date_time);

                $diff = $current_date_time->diff($last_online)->format("last online %a days %h hours and %i minutes ago");
                $exploded = explode(" ", $diff);

                if ($exploded[2] == "1") {
                    $diff = "<p class='text-warning'>$exploded[2]&nbsp;day&nbsp;ago</p>";
                } elseif ($exploded[4] == "1") {
                    $diff = "<p class='text-warning''>$exploded[4]&nbsp;hour&nbsp;ago</p>";
                } elseif ($exploded[7] == "1") {
                    $diff = "<p class=' text-warning''>$exploded[7]&nbsp;minute&nbsp;ago</p>";
                } elseif ($exploded[2] !== "0") {
                    $diff = "<p class='text-warning''>$exploded[2]&nbsp;days&nbsp;ago</p>";
                } elseif ($exploded[4] !== "0") {
                    $diff = "<p class=' text-warning''>$exploded[4]&nbsp;hours&nbsp;ago</p>";
                } elseif ($exploded[7] !== "0") {
                    $diff = "<p class='text-warning''>$exploded[7]&nbsp;minutes&nbsp;ago</p>";
                } else {
                    $diff = "<p class=' text-success'>Online</p>";
                }
            }
            echo ($_SESSION['user_id'] == $account['user_id']) ? "<tr class='table-success'>" : "<tr>";
            echo "<th scope='row'>";
            echo ($account['user_id']);
            echo $pfp;
            echo ("</th><td>");
            echo "<a href='./profile.php?id={$account['user_id']}'>" . htmlentities($account['username']) . "</a>";
            echo "</td><td>";
            echo "<p>" . htmlentities($account['name']) . "</p>";
            echo "</td><td>";
            echo ($account['show_email'] === "True") ? "<p class=''>" . htmlentities($account['email']) . "</p>" : "<p class='text-warning'>Hidden</p>";
            echo ("</td><td>");
            echo $diff;
            echo ("</td><td>");
            if ($account['user_id'] != $_SESSION['user_id']) {
                echo "<form action='add-friend.php' method='post'>";
                echo "<input name='friend_req' value='{$account['user_id']}' style='display: none;'/>";
                // echo "<input type='submit' value='Send friend request'/>";
                echo "none";
                echo "</form>";
            } else {
                echo "";
            }
            echo ("</td></tr>\n");
            echo ("</td></tr>\n");
        }
        echo "
        <tbody>
    </table>";
        ?>
    </main>
</body>

</html>