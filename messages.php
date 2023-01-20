<?php
session_start();
require_once "pdo.php";
/*if (!isset($_SESSION["email"])) {
    echo "<p class='die-msg'>PLEASE LOGIN</p>";
    echo '<link rel="stylesheet" href="./style.css?v=<?php echo time(); ?>">';
    echo "<br />";
    echo "<p class='die-msg'>Redirecting in 3 seconds</p>";
    header("refresh:3;url=index.php");
    die();
}*/
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
            $pfpsrc = './img/default-pfp.png';
            $user = "<a href='./profile.php?user={$row['user_id']}' class='account rainbow_text_animated'>" . $row['account'] . "</a>";

            $stmta = $pdo->prepare("SELECT pfp FROM account WHERE name=?");
            $stmta->execute([$row['account']]);
            $pfptemp = $stmta->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pfptemp as $test) {
                if ($test['pfp'] != null) {
                    $pfpsrc = $test['pfp'];
                }
            }
            $pfp = "<a class='pfp-link' href='./profile.php?user={$row['user_id']}'><img class='profile-image' src='$pfpsrc'></a>";


            $pattern = "/@" . $_SESSION['name'] . "/i";
            if (preg_match($pattern, $row["message"])) {
                $message = "<span class='user-ping'>" . htmlentities($row["message"]) . "</span>";
            } else {
                $message = htmlentities($row["message"]);
            }

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
            if ($row['user_id'] == $_SESSION['user_id']) {
                $editBtn = "<button class='chat-btn' onclick='handleEdit({$row['message_id']})'>Edit</button>";
            } else {
                $editBtn = "";
            }
            $msg = "<p class='msg' id='{$msg_parent_id}'><span id='{$row['message_id']}'>{$message}</span>" . $editBtn . "</p>";
            echo $pfp;
            echo "<div style='margin-left: 10px;margin-top: 18px;'>{$info}{$msg}</div>";
        }
    }
};
loadChat($pdo);
