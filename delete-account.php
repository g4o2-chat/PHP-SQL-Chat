<?php
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

if (isset($_POST['delete'])) {
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
    <title>Delete Account</title>
    <style>
        form {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <form class="form-signin" action="delete-account.php" method="post" enctype="multipart/form-data">
        <h1 class="h3 mb-3 font-weight-normal">Delete Account</h1>
        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Delete Account" name="delete">
        <br />
        <a href="./index.php">Cancel</a>
    </form>
</body>

</html>