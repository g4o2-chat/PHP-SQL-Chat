<?php
require_once "pdo.php";
require_once "head.php";
require_once "track_viewers.php";

if (isset($_SESSION['email'])) {
    $stmt = $pdo->query("SELECT * FROM account");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM account WHERE user_id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM user_status_log where user_Id = :usr");
    $stmt->execute(array(':usr' => $_SESSION['user_id']));
    $user_status_log = $stmt->fetch();
    $pfpsrc_default = './assets/images/default-user-square.png';

    if ($user[0]['pfp'] != null) {
        $userpfp = $user[0]['pfp'];
    } else {
        $userpfp = $pfpsrc_default;
    }

    if ($user_status_log != null) {
        $stmt = $pdo->prepare("UPDATE user_status_log SET last_active_date_time=? WHERE user_id=?");
        $stmt->execute([date(DATE_RFC2822), $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO user_status_log (user_id, last_active_date_time) VALUES (:usr, :date)');
        $stmt->execute(
            array(
                ':usr' => $_SESSION['user_id'],
                ':date' => date(DATE_RFC2822)
            )
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>G4o2 Chat</title>
    <link rel="stylesheet" href="./css/login.css">
    <style>
        main {
            display: none;
        }

        footer {
            display: none;
        }

        iframe {
            height: 150px !important;
            width: 250px !important;
            float: right;
            right: 0;
            position: absolute;
            display: none;
        }

        .img-container {
            /* position: relative; */
            width: 200px;
            height: 340px;
            background: rgba(0, 0, 0, 0);
            transform: rotate(-25deg) skew(25deg) scale(1);
            /* transform: rotate(-25deg) skew(25deg) scale(0.5); */
            transition: .4s;
            display: inline-block;
            /* margin-right: 400px; */
            margin-left: 100px;
        }

        .img-container img {
            position: absolute;
            height: 340px;
            width: 200px;
            transition: .4s;
            cursor: pointer;
            border-width: 30px 12px 35px;
            border-style: solid;
            border-radius: 8px;
            border-color: black;
        }

        .img-container:hover img:nth-child(4) {
            transform: translate(120px, -120px);
            opacity: 1;
        }

        .img-container:hover img:nth-child(3) {
            transform: translate(90px, -90px);
            opacity: 0.85;
        }

        .img-container:hover img:nth-child(2) {
            transform: translate(60px, -60px);
            opacity: 0.7;
        }

        .img-container:hover img:nth-child(1) {
            transform: translate(30px, -30px);
            opacity: 0.55;
        }

        #main-content {
            width: 100%;
            text-align: center;
        }

        #login {
            text-align: center;
            display: inline-block;
        }
    </style>
</head>

<body>
    <?php
    require_once "navbar.php";
    ?>
    <script>
        window.addEventListener("load", function() {
            $("#loading-screen").hide();
            $('main').fadeIn(1000);
            $('footer').fadeIn(1000);
            $('iframe').fadeIn(1000);
        });
    </script>
    <div class="w-100" style="background-color: #eee;margin: auto;">
        <main class="table-responsive">
            <?php
            if (isset($_SESSION["error"])) {
                echo ('<p class="text-danger">' . htmlentities($_SESSION["error"]) . "</p>");
                unset($_SESSION["error"]);
            }
            if (isset($_SESSION["success"])) {
                echo ('<p class="text-success">' . htmlentities($_SESSION["success"]) . "</p>");
                unset($_SESSION["success"]);
            }

            if (isset($_SESSION['user_id'])) {
                echo '<p>User ID >> ' . $_SESSION['user_id'] . "</p>";
            } else {
                echo '<p>Please <a class="btn btn-outline-success" href="./login.php">Login</a></p>';
            }
            echo '<a href="https://github.com/g4o2/PHP-SQL-Chat" target="_blank"><img src="https://github-readme-stats.vercel.app/api/pin/?username=g4o2&repo=PHP-SQL-Chat" alt="github repo"></a>';
            echo '<a href="https://github.com/g4o2/g4o2-api" target="_blank"><img src="https://github-readme-stats.vercel.app/api/pin/?username=g4o2&repo=g4o2-api" alt="github repo"></a><hr/>';
            ?>
            <div class="img-container">
                <img src='./assets/images/showcase/demo-2.PNG' />
                <img src='./assets/images/showcase/demo-3.PNG' />
                <img src='./assets/images/showcase/demo-2.PNG' />
                <img src='./assets/images/showcase/demo-1.PNG' />
            </div>
        </main>
        <?php
        if (isset($_SESSION['user_id'])) {
            // echo '<script> sessionStorage.setItem("user_id", "' . $_SESSION['user_id'] . '");</script>';
        } else {
            // echo '<script> sessionStorage.removeItem("user_id"); </script>';
        }
        ?>
    </div>
    <footer class="text-center text-lg-start bg-light text-muted">
        <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
            <div class="me-5 d-none d-lg-block">
                <!-- <span>Get connected with us on social networks:</span> -->
                <span>g4o2</span>
            </div>
            <div>
                <a href="https://www.facebook.com/profile.php?id=100075737822439" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/hu_kaixiang" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-google"></i>
                </a>
                <a href="https://www.instagram.com/hukaixiang1024/" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="https://www.linkedin.com/in/kaixiang-hu-6ab83a237/" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="https://github.com/Maxhu787/" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-github"></i>
                </a>
            </div>
        </section>
        <section class="">
            <div class="container text-center text-md-start mt-5">
                <div class="row mt-3">
                    <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                        <h6 class="text-uppercase fw-bold mb-4">
                            <i class="fas fa-gem me-3"></i>g4o2 chat
                        </h6>
                        <p>
                            The development of this chat started on August 23rd, 2022, as a side project of g4o2. Since then, it has been continually updated with new features. Currently, it boasts a user base of approximately 100 to 130 individuals, with some contributors who have assisted with testing the chat.
                        </p>
                    </div>
                    <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                        <h6 class="text-uppercase fw-bold mb-4">
                            pages
                        </h6>
                        <p>
                            <a href="./index.php" class="text-reset">Home</a>
                        </p>
                        <p>
                            <a href="./chat/chat.php" class="text-reset">Chat</a>
                        </p>
                        <p>
                            <a href="./users.php" class="text-reset">Users</a>
                        </p>
                        <p>
                            <a href="./profile.php" class="text-reset">Profile</a>
                        </p>
                    </div>
                    <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                        <h6 class="text-uppercase fw-bold mb-4">
                            links
                        </h6>
                        <p>
                            <a href="./terms-of-service.php" class="text-reset">Terms of Service</a>
                        </p>
                        <p>
                            <a href="./privacy-policy.php" class="text-reset">Privacy</a>
                        </p>
                        <p>
                            <a href="./cookie-policy.php" class="text-reset">Cookie Policy</a>
                        </p>
                        <p>
                            <a href="./contact.php" class="text-reset">Help</a>
                        </p>
                    </div>
                    <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                        <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                        <!-- <p><i class="fas fa-home me-3"></i> New York, NY 10012, US</p> -->
                        <p><i class="fas fa-envelope me-3"></i><a href="mailto:maxhu787@gmail.com">maxhu787@gmail.com</a></p>
                        <p><i class="fas fa-envelope me-3"></i><a href="mailto:g4o2@protonmail.com">g4o2@protonmail.com</a></p>
                        <p><i class="fas fa-envelope me-3"></i><a href="mailto:g4o3@protonmail.com">g4o3@protonmail.com</a></p>
                        <p><i class="fas fa-print me-3"></i> + 01 234 567 89</p>
                    </div>
                </div>
            </div>
        </section>
        <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
            © <?= date("Y") ?> Copyright g4o2. All rights reserved.
        </div>
</body>

</html>