<?php
session_start();
require_once "pdo.php";
require_once "head.php";
date_default_timezone_set('UTC');

if (isset($_SESSION["email"])) {
    header('Location: index.php');
}

if (isset($_POST["submit"])) {
    $statement = $pdo->prepare("SELECT * FROM account where email = :em");
    $statement->execute(array(':em' => $_POST['email']));
    $response = $statement->fetch();

    if ($response == "") {
        $name = $_POST['name'];
        $email = $_POST['email'];

        $salt = getenv('SALT');
        $check = hash("md5", $salt . $_POST['password']);
        $password = $check;

        $stmt = $pdo->prepare('INSERT INTO account
            (name, email, password) VALUES ( :nm, :em, :pw)');
        $stmt->execute(
            array(
                ':nm' => str_replace('<', ' ¯\_(ツ)_/¯ ', $name),
                ':em' => str_replace('<', ' ¯\_(ツ)_/¯ ', $email),
                ':pw' => str_replace('<', ' ¯\_(ツ)_/¯ ', $password)
            )
        );
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $_SESSION['success'] = "Account Created. Please login." . " ip: " . $ip;
        header('Location:login.php');
    } else {
        $_SESSION['error'] = "Email taken.";
        header('Location:signup.php');
    }
    return;
}
?>

<head>
    <title>Create Accnount</title>
    <link rel="stylesheet" href="./css/signup.css?v=<?php echo time(); ?>">
    <style>
        body {
            overflow-x: hidden;
        }
    </style>
</head>
<?php

if (isset($_SESSION["error"])) {
    echo ('<p class="error popup-msg popup-msg-long">' . htmlentities($_SESSION["error"]) . "</p>");
    unset($_SESSION["error"]);
    echo "";
}
?>
<div id="particles-js"></div>
<div class="center">
    <h1>Signup</h1>
    <form id="form" action="./signup.php" method="post" enctype="multipart/form-data">
        <div class="input-field">
            <input required type="text" name="name">
            <span></span>
            <label>Name</label>
        </div>
        <div class="input-field">
            <input required type="email" name="email" id="id_email">
            <span></span>
            <label>Email</label>
        </div>
        <div class="input-field">
            <input required size='21' type="password" name="password" id="id_1723">
            <span></span>
            <label>Password</label>
        </div>
        <input type="checkbox" name="spamemail" style="float:left;">
        <p style="float:left;margin-left: 10px;">Spam my email</p>
        <div style="text-align:center">
            <input type="submit" value="Create account" name="submit" onclick="return doValidate();">
        </div>
        <div class="cancel">
            By registering, you agree to our <a href="./terms-of-service.php" target="_blank">Terms</a>, <a href="./privacy-policy.php" target="_blank">Privacy Policy</a> and <a href="./cookie-policy.php" target="_blank">Cookie Policy</a>.<br />
            Already have an account? please <a href="./login.php">log in</a>
            <!-- <a href="./index.php">Cancel</a> -->
        </div>
    </form>
</div>
<script src="./particles/particles.js"></script>
<script>
    function doValidate() {
        console.log("Validating...");
        try {
            email = document.getElementById("id_email").value;
            pw = document.getElementById("id_1723").value;
            console.log("Validating email=" + email);
            console.log("Validating pw=" + pw);
            if (pw == null || pw == "" || email == null || email == "") {
                alert("Both fields must be filled out");
                return false;
            }
            if (email.search("@") === -1) {
                alert("Email address must contain @");
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
        return false;
    }
    particlesJS.load('particles-js', './particles/particles.json', function() {
        console.log('callback - particles.js config loaded');
    });
    setTimeout(function() {
        document.querySelector('.popup-msg').style.display = "none";
        document.querySelector('.error').style.display = "none";
    }, 2200);
</script>