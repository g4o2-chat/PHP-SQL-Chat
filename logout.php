<?php
session_start();
date_default_timezone_set('Asia/Taipei');


error_log("Logout success " . $_SESSION['email'] . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");

unset($_SESSION['name']);
unset($_SESSION['user_id']);
unset($_SESSION['email']);
session_destroy();

if (isset($_COOKIE['email']) && !empty($_COOKIE['email'])) {
    unset($_COOKIE['email']);
    setcookie('email', null, -1, '/');
}
if (isset($_COOKIE['password']) && !empty($_COOKIE['password'])) {
    unset($_COOKIE['password']);
    setcookie('password', null, -1, '/');
}
header('Location: index.php');
exit;
?>