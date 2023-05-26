<?php
require_once "pdo.php";
require_once "head.php";
date_default_timezone_set('Asia/Taipei');

if (isset($_SESSION["email"])) {
    header('Location: index.php');
}

if (isset($_POST["submit"])) {
    $statement = $pdo->prepare("SELECT * FROM account where email = :em");
    $statement->execute(array(':em' => $_POST['email']));
    $response = $statement->fetch();

    if ($response == "") {
        $statement = $pdo->prepare("SELECT * FROM account where username = :username");
        $statement->execute(array(':username' => $_POST['username']));
        $response = $statement->fetch();

        if ($response == "") {
            $username = $_POST['username'];
            $email = $_POST['email'];

            $salt = getenv('SALT');
            $check = hash("md5", $salt . $_POST['password']);
            $password = $check;

            $stmt = $pdo->prepare('INSERT INTO account
            (username, email, password) VALUES ( :username, :em, :pw)');
            $stmt->execute(
                array(
                    ':username' => $username,
                    ':em' => $email,
                    ':pw' => $password
                )
            );
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $_SESSION['success'] = "Account Created. Please login." . " ip: " . $ip;
            header('Location:login.php');
        } else {
            $_SESSION['error'] = "Username taken.";
            header('Location:signup.php');
        }
    } else {
        $_SESSION['error'] = "Email taken.";
        header('Location:signup.php');
    }
    return;
}
?>

<head>
    <title>Sign up</title>
    <style>
        html,
        body {
            height: 100%;
            background-color: #fff !important;
        }

        body {
            display: -ms-flexbox;
            display: -webkit-box;
            display: flex;
            -ms-flex-align: center;
            -ms-flex-pack: center;
            -webkit-box-align: center;
            align-items: center;
            -webkit-box-pack: center;
            justify-content: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: 0 auto;
            /* text-align: center; */
        }

        .form-signin .checkbox {
            font-weight: 400;
        }

        .form-signin .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;
        }

        .form-signin .form-control:focus {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>
<form class="form-signin" method="post" action="./signup.php" enctype="multipart/form-data">
    <img class="mb-4" src="./favicon.ico" alt="" width="72" height="72" style="float: left;">
    <h1 class="h3 mb-3 font-weight-normal" style="margin-top: 24px; margin-left: 90px;">Sign up</h1>
    <?php
    if (isset($_SESSION["error"])) {
        echo ('<p class="text-danger">' . htmlentities($_SESSION["error"]) . "</p>");
        unset($_SESSION["error"]);
    }
    if (isset($_SESSION["success"])) {
        echo ('<p class="text-success">' . htmlentities($_SESSION["success"]) . "</p>");
        unset($_SESSION["success"]);
    }
    ?>
    <label for="username" class="sr-only">Username</label>
    <input id="username" type="text" class="form-control" name="username" placeholder="Username" required="" autofocus="" maxlength=128>
    <label for="" class="sr-only">Email</label>
    <input type="email" id="id_email" class="form-control" name="email" placeholder="Email address" required="">
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="id_1723" class="form-control" name="password" placeholder="Password" required="">
    <div class="checkbox mb-3">
        <label>
            <input type="checkbox" name="spamemail" value="spam my email"> Spam my email
        </label>
    </div>
    <button class="btn btn-lg btn-primary btn-block" name="submit" type="submit" onclick="return doValidate();">Signup</button>
    <br />Already have an account? please <a href="./login.php">log in</a>
    <p class="mt-5 mb-3 text-muted">© <?= date("Y") ?></p>
    By registering, you agree to our <a href="./terms-of-service.php" target="_blank">Terms</a>, <a href="./privacy-policy.php" target="_blank">Privacy Policy</a> and <a href="./cookie-policy.php" target="_blank">Cookie Policy</a>.<br />
</form>
<script>
    document.getElementById('username').onkeydown = function(e) {
        var value = e.target.value;
        if (value) {
            if (!e.key.match(/[a-zA-Z0-9_]/) || (e.key == '_' && value[value.length - 1] == '_')) {
                e.preventDefault();
            }
        }
    };

    document.getElementById('username').addEventListener("paste", (event) => {
        event.preventDefault();
    })

    function doValidate() {
        console.log("Validating...");
        try {
            email = document.getElementById("id_email").value;
            pw = document.getElementById("id_1723").value;
            username = document.getElementById("username").value;

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
            if (username.match(/[^a-zA-Z0-9_]+/g)) {
                alert("Username must consist of only characters, numbers, and underscore")
                username.value = "";
                return false;
            }
            return true;
        } catch (e) {
            return false;
        }
        return false;
    }
    setTimeout(function() {
        // document.querySelector('.popup-msg').style.display = "none";
        // document.querySelector('.error').style.display = "none";
    }, 2200);
</script>