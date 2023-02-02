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
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) > 0) {
        echo "<p style='text-align:center;color: #ffa500;'>This is the start of all messages</p>";
        foreach ($rows as $row) {
            $pfpsrc = './assets/images/default-user-round.png';
            $user = "<a href='./profile.php?user={$row['account']}' class='account rainbow_text_animated'>" . $row['account'] . "</a>";

            $stmta = $pdo->prepare("SELECT pfp FROM account WHERE name=?");
            $stmta->execute([$row['account']]);
            $pfptemp = $stmta->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pfptemp as $test) {
                if ($test['pfp'] != null) {
                    $pfpsrc = $test['pfp'];
                }
            }
            $pfp = "<a class='pfp-link' href='./profile.php?user={$row['account']}'><img class='profile-image' src='$pfpsrc'></a>";


            $message = htmlentities($row["message"]);
            if (isset($_COOKIE['timezone'])) {

                //might break the chat 
                $timezone_offset_minutes = $_COOKIE['timezone'];
                $time = new DateTime($row["message_date"]);
                $minutes_to_add = ($timezone_offset_minutes);
                $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
                $stamp = $time->format('D, d M Y H:i:s');
                // here ^

            } else {
                $stamp = $row["message_date"];
            }
            $msg_parent_id = $row['message_id'] . "parent";
            $info = "<p class='stats'>{$user} ({$stamp})</p>";
            $editBtn = "<button class='btn' onclick='handleEdit({$row['message_id']})'>Edit {$row['message_id']}</button>";
            $msg = "<p class='msg' id='{$msg_parent_id}'><span id='{$row['message_id']}'>{$message}</span> {$editBtn}</p>";
            echo $pfp;
            echo "<div style='margin-left: 10px;margin-top: 18px;'>{$info}{$msg}</div>";
        }
    }
};
loadChat($pdo);
