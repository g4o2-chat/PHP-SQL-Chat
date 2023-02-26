<?php
require_once "pdo.php";
require_once "head.php";

if (!isset($_GET['id'])) {
    echo "<p align='center' class='text-danger'>Missing user parameter</p>";
    die();
}
$pfpsrc = './assets/images/default-user-square.png';
$stmt = $pdo->prepare("SELECT * FROM account WHERE user_id=?");
$stmt->execute([$_GET['id']]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) > 0) {
    foreach ($rows as $test) {
        if ($test['pfp'] != null) {
            $pfpsrc = $test['pfp'];
        }
        $show_email = $test['show_email'];
        $username = $test['username'];
        $name = $test['name'];
        $pfp = $pfpsrc;
        $about = $test['about'];
        $email = ($show_email === "True") ? $test['email'] : 'Hidden';
    }
} else {
    echo "<p align='center' class='text-danger'>User not found</p>";
    die();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= (isset($name)) ? $username . " ($name)" : $username ?></title>
</head>

<body>
    <?php
    include_once "navbar.php";
    ?>
    <br />
    <div class="card" style="width: 18rem;margin: auto;">
        <img src="<?= $pfp ?>" height="280px" class="card-img-top" alt="User profile picture">
        <div class="card-body">
            <h5 class="card-title"><?= htmlentities($username) ?></h5>
            <p class="card-text"><?= htmlentities($name) ?></p>
        </div>
        <ul class="list-group list-group-flush">
            <li class="list-group-item"><?= htmlentities($about) ?></li>
            <li class="list-group-item">Test random text</li>
            <li class="list-group-item">Test random text</li>
        </ul>
        <div class="card-body">
            <a href="#" class="card-link">Link</a>
            <a href="#" class="card-link">Link</a>
        </div>
    </div>
</body>

</html>