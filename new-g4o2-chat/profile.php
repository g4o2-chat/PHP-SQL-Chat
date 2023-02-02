<?php
require_once "pdo.php";
require_once "head.php";
date_default_timezone_set('Aisa/Taipei');

if (!isset($_GET['id'])) {
    echo "<p align='center' class='text-danger'>Missing user parameter</p>";
    die();
}
$pfpsrc = './img/default-pfp.png';
$stmt = $pdo->prepare("SELECT * FROM account WHERE user_id=?");
$stmt->execute([$_GET['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/profile.css">
</head>

<body>
    <?php
    if (count($rows) > 0) {
        foreach ($rows as $test) {
            if ($test['pfp'] != null) {
                $pfpsrc = $test['pfp'];
            }
            $show_email = $test['show_email'];
            $user = "<div id='user-name'><p>{$test['name']}</p></div>";
            $pfp = "<img id='profile-image' src='$pfpsrc'>";
            $about = "<div id='user-about'><p>{$test['about']}</p></div>";
            if ($show_email === "True") {
                $email = "<div id='user-email'><p>{$test['email']}</p></div>";
            } else {
                $email = "<div id='user-email'><p>Hidden</p></div>";
            }
        }
        echo $pfp;
        echo $user;
        echo $about;
        echo $email;
    } else {
        echo "<p style='font-size: 22px;font-family: Arial;text-align:center;color:red;'>User not found</p>";
    }
    ?>
</body>

</html>