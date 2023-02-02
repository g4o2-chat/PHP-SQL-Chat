<?php
session_start();
date_default_timezone_set('Asia/Taipei');

if ($_POST["email"] == 'g4o2@protonmail.com' || $_POST["email"] == 'g4o3@protonmail.com' || $_POST["email"] == 'maxhu787@gmail.com') {
    // error_log("Logout success admin account " . $_SESSION['email'] . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
} else {
    error_log("Logout success " . $_SESSION['email'] . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
}
unset($_SESSION['name']);
unset($_SESSION['user_id']);
unset($_SESSION['email']);
session_start();
session_destroy();

header('Location: index.php');
?>
