<?php
require_once "pdo.php";
require_once "head.php";
date_default_timezone_set('Asia/Taipei');

if (!isset($_SESSION["email"])) {
  echo "<p align='center'>PLEASE LOGIN</p>";
  echo "<br />";
  echo "<p align='center'>Redirecting in 3 seconds</p>";
  header("refresh:3;url=login.php");
  die();
}

$stmt = $pdo->query(
  "SELECT * FROM chatlog"
);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
/*
if (isset($_POST['message'])) {
  $stmta = $pdo->prepare(
    'INSERT INTO chatlog
  (message, message_date, account, user_id)
  VALUES (:msg, :msgd, :acc, :usrid)'
  );

  $stmta->execute(
    array(
      ':msg' => $_POST['message'],
      ':msgd' => date(DATE_RFC2822),
      ':acc' => $_SESSION['name'],
      ':usrid' => $_SESSION['user_id']
    )
  );
  $stmt = $pdo->query(
    "SELECT * FROM chatlog"
  );
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  foreach ($_POST as $edit_msg) {
    $key = array_search($edit_msg, $_POST);


    $sql = "UPDATE chatlog SET message = :msg
            WHERE message_id = :message_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':msg' => $edit_msg,
      ':message_id' => $key
    ));

    break;
  }
}*/
?>
<html>

<head>
  <link rel="stylesheet" href="./css/chat.css">
  <title>g4o2 chat</title>
</head>

<body>
  <?php
  include_once "navbar.php";
  foreach ($rows as $row) {
    // echo $row['message_id'].'<br/>';
    echo $row['message_id'].'';
  }
  ?>
</body>
</html>