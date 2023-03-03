<?php
require_once "pdo.php";
if (!isset($_SESSION["email"])) {
    echo "<p align='center'>PLEASE LOGIN</p>";
    echo "<br />";
    echo "<p align='center'>Redirecting in 3 seconds</p>";
    header("refresh:3;url=login.php");
    die();
}
require_once "pdo.php";
function loadChat($pdo)
{
    $stmt = $pdo->query(
        "SELECT * FROM chatlog"
    );
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($messages) > 0) {
        foreach ($messages as $row) {
            $pfpsrc = './assets/images/default-user-round.png';

            $stmt = $pdo->prepare("SELECT pfp FROM account WHERE user_id=?");
            $stmt->execute([$row['user_id']]);
            $pfptemp = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pfptemp as $test) {
                if ($test['pfp'] != null) {
                    $pfpsrc = $test['pfp'];
                }
            }

            if (isset($_COOKIE['timezone'])) {
                $timezone_offset_minutes = $_COOKIE['timezone'];
                $time = new DateTime($row["message_date"]);
                $minutes_to_add = ($timezone_offset_minutes);
                $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                $stamp = $time->format('D, d M Y H:i:s');
            } else {
                $stamp = $row["message_date"];
            }

            $pfp = "<a class='pfp-link' href='./profile.php?id={$row['user_id']}'><img class='profile-image' src='$pfpsrc'></a>";
            $user = "<a href='./profile.php?id={$row['user_id']}' class='account rainbow_text_animated'>" . $row['account'] . "</a>";
            $message = htmlentities($row["message"]);
            $msg_parent_id = $row['message_id'] . "parent";
            $info = "<p class='stats'>{$user} ({$stamp})</p>";
            if ($row['user_id'] == $_SESSION['user_id']) {
                $editBtn = "<button class='btn chat-btn' onclick='handleEdit({$row['message_id']})'>Edit</button>";
            } else {
                $editBtn = "";
            }
            // $msg = "<p class='msg' id='{$msg_parent_id}'><span id='{$row['message_id']}'>{$message}</span> " . $editBtn . "</p>";
            // echo "<div style='margin-left: 10px;margin-top: 18px;'>{$info}{$msg}</div>";
            echo $pfp;
            echo '<div style="margin-left: 10px;margin-top: 18px;">  
                    <p class="stats"><a href="./profile.php?id='.$row['user_id'].'" class="account rainbow_text_animated">'.$row['account'].'</a> '.$stamp.'</p>
                    <p class="msg" id="'. $msg_parent_id.'"><span id="'.$row['message_id'].'">'.$message.'</span> '.$editBtn.'</p>
                </div>';
        }
    }
};
loadChat($pdo);
