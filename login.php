<?php
require_once "pdo.php";
require_once "head.php";

date_default_timezone_set('Asia/Taipei');

if (isset($_POST["cancel"])) {
    header("Location: ./index.php");
    die();
}

if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    unset($_SESSION["username"]);
    unset($_SESSION["user_id"]);
    session_destroy();
    session_start();
    $salt = getenv('SALT');
    $check = hash("md5", $salt . $_COOKIE['password']);

    $stmt = $pdo->prepare(
        'SELECT user_id, username, email, disabled
        FROM account
        WHERE
        email = :em AND
        password = :pw'
    );
    $stmt->execute(array(':em' => $_COOKIE['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    if ($row !== false) {
        if ($row['disabled'] === "True") {
            $_SESSION["error"] = "Account disabled";
            error_log("Login fail disabled account " . $_COOKIE['email'] . " " . $ip . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
            header("Location: ./login.php");
            die();
        }
        if ($_COOKIE['email'] == 'g4o2@protonmail.com' || $_COOKIE['email'] == 'g4o3@protonmail.com' || $_POST["email"] == 'maxhu787@gmail.com') {
            // error_log("Login success admin account (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        } else {
            error_log("Login success " . $_COOKIE['email'] . " " . $ip . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        }
        $_SESSION["user_id"] = $row["user_id"];
        $_SESSION["username"] = $row["username"];
        $_SESSION['email'] = $row['email'];
        $_SESSION["success"] = "Logged in.";
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'chat') {
                header("Location: ./chat/chat.php");
            }
        } else {
            header("Location: ./index.php");
        }
        die();
    } else {
        $_SESSION["error"] = "Incorrect email or password";
        error_log("Login fail wrong password " . $_COOKIE['email'] . " " . $check . " " . $ip . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        header("Location: ./login.php");
        die();
    }
}

if (isset($_POST["email"]) && isset($_POST["pass"])) {
    unset($_SESSION["username"]);
    unset($_SESSION["user_id"]);
    session_destroy();
    session_start();
    $salt = getenv('SALT');
    $check = hash("md5", $salt . $_POST["pass"]);

    $stmt = $pdo->prepare(
        'SELECT user_id, username, email, disabled
        FROM account
        WHERE
        email = :em AND
        password = :pw'
    );
    $stmt->execute(array(':em' => $_POST['email'], ':pw' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

    if ($row !== false) {
        if ($row['disabled'] === "True") {
            $_SESSION["error"] = "Account disabled";
            error_log("Login fail disabled account " . $_POST['email'] . " " . $ip . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
            header("Location: ./login.php");
            die();
        }
        if ($_POST["email"] == 'g4o2@protonmail.com' || $_POST["email"] == 'g4o3@protonmail.com' || $_POST["email"] == 'maxhu787@gmail.com') {
            // error_log("Login success admin account (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        } else {
            error_log("Login success " . $_POST['email'] . " " . $ip . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        }
        $_SESSION["user_id"] = $row["user_id"];
        $_SESSION["username"] = $row["username"];
        $_SESSION['email'] = $row['email'];
        $_SESSION["success"] = "Logged in.";
        if ($_POST['remember'] == 'remember') {
            setcookie("email", $row["email"], time() + (86400 * 30), "/");
            setcookie("password", $_POST["pass"], time() + (86400 * 30), "/");
            // 86400 = 1 day
        }
        if (isset($_GET['page'])) {
            if ($_GET['page'] == 'chat') {
                header("Location: ./chat/chat.php");
            }
        } else {
            header("Location: ./index.php");
        }
        die();
    } else {
        $_SESSION["error"] = "Incorrect email or password";
        error_log("Login fail wrong password " . $_POST['email'] . " " . $check . " " . $ip . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        header("Location: ./login.php");
        die();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Login</title>
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

<body class="text-center">
    <form class="form-signin" method="post" onsubmit="return doValidate();">
        <img class="mb-4" src="./favicon.ico" alt="" width="72" height="72">
        <h1 class="h3 mb-3 font-weight-normal">Please sign in</h1>
        <p>
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
        </p>
        <label for="id_email" class="sr-only">Email address</label>
        <input type="email" id="id_email" class="form-control" name="email" placeholder="Email address" required="" autofocus="">
        <label for="id_pass" class="sr-only">Password</label>
        <input type="password" id="id_pass" class="form-control" name="pass" placeholder="Password" required="">
        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" name="remember" value="remember" checked> Remember me
            </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button>
        <p class="mt-5 mb-3 text-muted">© <?= date("Y") ?></p>
        <p>Don't have an account yet? <a href='./signup.php'>register</a></p>
    </form>
    <script>
        function doValidate() {
            console.log("Validating...");
            try {
                email = document.getElementById("id_email").value;
                pw = document.getElementById("id_pass").value;
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
    </script>
</body>

</html>