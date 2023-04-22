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

if (isset($_SESSION["email"])) {
    $stmt = $pdo->prepare("SELECT * FROM account WHERE user_id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $pfpsrc_default = './assets/images/default-user-square.png';

    if ($user[0]['pfp'] != null) {
        $userpfp = $user[0]['pfp'];
    } else {
        $userpfp = $pfpsrc_default;
    }
}

if (isset($_POST["submit"])) {
    if (!file_exists($_FILES['fileToUpload']['tmp_name']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name'])) {
        $stmta = $pdo->prepare("SELECT pfp FROM account WHERE user_id=?");
        $stmta->execute([$_SESSION['user_id']]);
        $pfptemp = $stmta->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pfptemp as $test) {
            if ($test['pfp'] != null) {
                $base64 = $test['pfp'];
            }
        }
    } else {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        $uploadOk = 1;
        $path = $_FILES["fileToUpload"]["tmp_name"];
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }
    if ($check !== false) {
        $statement = $pdo->prepare("SELECT user_id FROM account where email = :em");
        $statement->execute(array(':em' => $_POST['email']));
        $checkEmail = $statement->fetch();

        if ($checkEmail['user_id'] == $_SESSION['user_id'] || $checkEmail['user_id'] == "") {
            $emailCheck = true;
        } else {
            $emailCheck = false;
        }

        if ($emailCheck != false) {
            $statement = $pdo->prepare("SELECT user_id FROM account where username = :username");
            $statement->execute(array(':username' => $_POST['username']));
            $checkUsername = $statement->fetch();
            if ($checkUsername['user_id'] == $_SESSION['user_id'] || $checkUsername['user_id'] == "") {
                $usernameCheck = true;
            } else {
                $usernameCheck = false;
            }
            if ($usernameCheck != false) {
                if (isset($_POST['password'])) {
                    $salt = getenv('SALT');
                    $newPassword = $_POST['password'];
                    $hash = hash("md5", $salt . $newPassword);
                }
                if ($_POST["show_email"] == "on") {
                    $show_email = "True";
                } else {
                    $show_email = "False";
                }

                $sql = "UPDATE account SET pfp = :pfp, 
            username = :newUsername,
            name = :newName,
            email = :email,
            password = :password,
            about = :about,
            show_email = :showEmail
            WHERE user_id = :usrid";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array(
                    ':pfp' => $base64,
                    ':usrid' => $_SESSION['user_id'],
                    ':newUsername' => $_POST['username'],
                    ':newName' => $_POST['name'],
                    ':email' => $_POST['email'],
                    ':password' => $hash,
                    ':about' => $_POST['about'],
                    ':showEmail' => $show_email
                ));
                $_SESSION['success'] = 'Account details updated.';
            } else {
                $_SESSION['error'] = 'Username taken';
            }
        } else {
            $_SESSION['error'] = 'Email taken';
        }
    } else {
        $_SESSION['error'] = "File is not an image.";
        $uploadOk = 0;
    }
    header("Location: ./index.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Account Settings</title>
    <style>
        html,
        body {
            height: 100%;
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

<body>
    <?php
    require_once "navbar.php";
    ?>
    <main style="padding-top: 100px;">
        <form class="form-signin" action="account-settings.php" method="post" enctype="multipart/form-data" autocomplete="off">
            <h1 class="h3 mb-3 font-weight-normal">Account Settings</h1>
            Select image to upload for <?= htmlentities($_SESSION['username']) ?>
            <input type="file" name="fileToUpload" id="fileToUpload">
            <label for="name" class="sr-only">Username</label>
            <input type="text" name="username" class="form-control" placeholder="" required="" autofocus="" value="<?= htmlentities($user[0]['username']) ?>">
            <label for="name" class="sr-only">Name</label>
            <input type="text" name="name" class="form-control" placeholder="" required="" autofocus="" value="<?= htmlentities($user[0]['name']) ?>">
            <label for="email" class="sr-only">Email</label>
            <input type="email" name="email" class="form-control" placeholder="" required="" value="<?= htmlentities($user[0]['email']) ?>">
            <label for="about" class="sr-only">About</label>
            <input type="text" name="about" class="form-control" placeholder="" required="" value="<?= htmlentities($user[0]['about']) ?>">
            <label for="password" class="sr-only">New Password</label>
            <input type="password" name="password" class="form-control" placeholder="Password" required="">
            <div class="checkbox mb-3">
                <label>
                    <input type="checkbox" name="show_email" <?php echo ($user[0]['show_email'] == 'True') ? 'checked' : '' ?>> Show Email
                </label>
            </div>
            <input class="btn btn-lg btn-primary btn-block" type="submit" name="submit" value="Save Changes">
            <br />
            <a href="./index.php">Cancel</a> | <a href="./delete-account.php">Delete Account</a>
        </form>
    </main>
</body>

</html>