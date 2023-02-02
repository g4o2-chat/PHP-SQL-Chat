<?php
require_once "pdo.php";
require_once "head.php";

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
        $stmt = $pdo->prepare("UPDATE user_status_log SET account=?, last_active_date_time=? WHERE user_id=?");
        $stmt->execute([$_SESSION['username'], date(DATE_RFC2822), $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO user_status_log (user_id, account, last_active_date_time) VALUES (:usr, :acc, :date)');
        $stmt->execute(
            array(
                ':usr' => $_SESSION['user_id'],
                ':acc' => $_SESSION['username'],
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
</head>

<body>
    <nav class="navbar fixed-top navbar-expand-lg bg-dark" data-bs-theme="dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="./index.php">
                <img src="./assets/images/g4o2.jpeg" alt="Logo" width="24" height="24" class="d-inline-block align-text-top">
                G4o2 Chat
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://github.com/g4o2" target="_blank">Github</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Links
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Chat</a></li>
                            <li><a class="dropdown-item" href="./profile.php?id=<?=$_SESSION['user_id']?>">User Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="./account-settings.php">Account Settings</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled">Private Messaging (coming soon)</a>
                    </li>
                </ul>
                <?= isset($_SESSION['user_id']) ? '<a class="btn btn-outline-success" href="./logout.php">Logout</a>' : '<a class="btn btn-outline-success" href="./login.php">Login</a>' ?>
            </div>
        </div>
    </nav>
    <main>
        <div class="w-75 p-2" style="background-color: #eee;margin: auto;">
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
                echo 'User ID ' . $_SESSION['user_id'];
                echo '
            <table class="table table-light table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col" style="background-color: #eee;">#</th>
                        <th scope="col" style="background-color: #eee;">Username</th>
                        <th scope="col" style="background-color: #eee;">Name</th>
                        <th scope="col" style="background-color: #eee;">Email</th>
                        <th scope="col" style="background-color: #eee;">Last active</th>
                    </tr>
                </thead>
                <tbody>';
                foreach ($accounts as $account) {
                    if ($account['pfp'] != null) {
                        $pfpsrc = $account['pfp'];
                    } else {
                        $pfpsrc = $pfpsrc_default;
                    }

                    $pfp = "<a class='pfp-link' href='./profile.php?id={$account['user_id']}'><img style='border-radius: 100px; margin-left: 10px; ' height='20px' width='20px' src='$pfpsrc'></a>";

                    $statement = $pdo->prepare("SELECT * FROM user_status_log where user_Id = :usr");
                    $statement->execute(array(':usr' => $account['user_id']));
                    $user_status_log = $statement->fetch();
                    $userStatus = ($user_status_log != null) ? $user_status_log['last_active_date_time'] : "Undefined";;

                    if ($userStatus === "Undefined") {
                        $diff = "<p class='text-danger'>Undefined</p>";
                    } else {
                        $last_online = $userStatus;
                        $current_date_time = date(DATE_RFC2822);
                        $last_online = new DateTime($last_online);
                        $current_date_time = new DateTime($current_date_time);

                        $diff = $current_date_time->diff($last_online)->format("last online %a days %h hours and %i minutes ago");
                        $exploded = explode(" ", $diff);

                        if ($exploded[2] == "1") {
                            $diff = "<p class='text-warning'>$exploded[2]&nbsp;day&nbsp;ago</p>";
                        } elseif ($exploded[4] == "1") {
                            $diff = "<p class='text-warning''>$exploded[4]&nbsp;hour&nbsp;ago</p>";
                        } elseif ($exploded[7] == "1") {
                            $diff = "<p class=' text-warning''>$exploded[7]&nbsp;minute&nbsp;ago</p>";
                        } elseif ($exploded[2] !== "0") {
                            $diff = "<p class='text-warning''>$exploded[2]&nbsp;days&nbsp;ago</p>";
                        } elseif ($exploded[4] !== "0") {
                            $diff = "<p class=' text-warning''>$exploded[4]&nbsp;hours&nbsp;ago</p>";
                        } elseif ($exploded[7] !== "0") {
                            $diff = "<p class='text-warning''>$exploded[7]&nbsp;minutes&nbsp;ago</p>";
                        } else {
                            $diff = "<p class=' text-success'>Online</p>";
                        }
                    }
                    echo "<tr><th scope='row'>";
                    echo ($account['user_id']);
                    echo $pfp;
                    echo ("</th><td>");
                    echo "<a href='./profile.php?id={$account['user_id']}'>" . htmlentities($account['username']) . "</a>";
                    echo "</td><td>";
                    echo "<p>".htmlentities($account['name']) . "</p>";
                    echo "</td><td>";
                    echo ($account['show_email'] === "True") ? "<p class=''>" . htmlentities($account['email']) . "</p>" : "<p class='text-warning'>Hidden</p>";
                    echo ("</td><td>");
                    echo $diff;
                    echo ("</td></tr>\n");
                    echo ("</td></tr>\n");
                }
                echo "<tbody></table>";
            } else {
                echo '<p>Please login</p>';
            }
            ?>
        </div>
    </main>
    <footer class="text-center text-lg-start bg-light text-muted">
        <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
            <div class="me-5 d-none d-lg-block">
                <!-- <span>Get connected with us on social networks:</span> -->
                <span>Footer</span>
            </div>
            <div>
                <a href="#" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-google"></i>
                </a>
                <a href="#" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="me-4 text-reset text-decoration-none" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="https://github.com/g4o2" class="me-4 text-reset text-decoration-none" target="_blank">
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
                            Development of this chat began on 2022/8/23, as a side project of <a href="https://github.com/Maxhu787" target="_blank">g4o2</a>, and has been constantly updated with new and exciting features ever since.
                            With about 50 users and some contributors that helped with the testing of this chat.

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
                            <a href="#" class="text-reset">Chat</a>
                        </p>
                        <p>
                            <a href="#" class="text-reset">Users</a>
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
                            <a href="#" class="text-reset">Settings</a>
                        </p>
                        <p>
                            <a href="#" class="text-reset">Help</a>
                        </p>
                    </div>
                    <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                        <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                        <!-- <p><i class="fas fa-home me-3"></i> New York, NY 10012, US</p> -->
                        <p><i class="fas fa-envelope me-3"></i>Maxhu787@gmail.com</p>
                        <p><i class="fas fa-envelope me-3"></i>g4o2@protonmail.com</p>
                        <p><i class="fas fa-envelope me-3"></i>g4o3@protonmail.com</p>
                        <p><i class="fas fa-print me-3"></i> + 01 234 456 89</p>
                    </div>
                </div>
            </div>
        </section>
        <div class="text-center p-4" style="background-color: rgba(0, 0, 0, 0.05);">
            © <?= date("Y") ?> Copyright g4o2. All rights reserved.
        </div>
</body>

</html>