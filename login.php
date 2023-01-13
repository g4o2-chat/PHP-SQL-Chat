<?php
session_start();
ob_start();
require_once "pdo.php";
require_once "head.php";

date_default_timezone_set('Asia/Taipei');

$host = $_SERVER['HTTP_HOST'];
$ruta = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$url = "http://$host$ruta";

if (isset($_POST["cancel"])) {
    header("Location: $url/index.php");
    die();
}

if (isset($_POST["email"]) && isset($_POST["pass"])) {
    unset($SESSION["name"]);
    unset($SESSION["user_id"]);
    session_destroy();
    session_start();
    $salt = getenv('SALT');
    $check = hash("md5", $salt . $_POST["pass"]);

    $stmt = $pdo->prepare(
        'SELECT user_id, name, email, disabled
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
            header("Location: $url/login.php");
            die();
        }
        if ($_POST["email"] == 'g4o2@protonmail.com' || $_POST["email"] == 'g4o3@protonmail.com' || $_POST["email"] == 'maxhu787@gmail.com') {
            // error_log("Login success admin account (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        } else {
            error_log("Login success " . $_POST['email'] . " " . $ip . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        }
        $_SESSION["user_id"] = $row["user_id"];
        $_SESSION["name"] = $row["name"];
        $_SESSION['email'] = $row['email'];
        $_SESSION["success"] = "Logged in.";
        header("Location: $url/index.php");
        die();
    } else {
        $_SESSION["error"] = "Incorrect email or password";
        error_log("Login fail wrong password " . $_POST['email'] . " " . $check . " " . $ip . " (" . date(DATE_RFC2822) . ")\n", 3, "./logs/logs.log");
        header("Location: $url/login.php");
        die();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="./css/login.css?v=<?php echo time(); ?>">
</head>

<body>
    <div id="particles-js"></div>
    <div class="center">
        <h1>Login to g4o2</h1>
        <?php
        if (isset($_SESSION["error"])) {
            echo ('<p style="color: red;">' . htmlentities($_SESSION["error"]) . "</p>");
            unset($_SESSION["error"]);
        }
        if (isset($_SESSION["success"])) {
            echo ('<p style="color: green">' . htmlentities($_SESSION["success"]) . "</p>");
            unset($_SESSION["success"]);
        }
        ?>
        <form method="post">
            <div class="input-field">
                <input class="input" type="text" name="email" id="id_email" required>
                <span></span>
                <label for='email'>Email</label>
            </div>
            <div class="input-field">
                <input class="input" type="password" name="pass" id="id_1723" required>
                <span></span>
                <label for='pass'>Password</label>
            </div>
            <div class="forgot-password">Forgot Password</div>
            <div style="text-align:center">
                <input id="submit" type="submit" onclick="return doValidate();" value="Login">
            </div>
            <div class="sign-up">
                Don't have an account yet? <a href='./signup.php'>register</a>
            </div>
            <!-- <input id="cancel" class="btn" type="submit" name="cancel" value="Cancel"> -->
        </form>
        <!-- <p style='text-align: center;color:red;'>Try the password 'password' if you can't login</p> -->
    </div>
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
    </script>
</body>

</html>