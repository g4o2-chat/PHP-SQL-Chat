<nav class="navbar navbar-dark  fixed-top navbar-expand-lg bg-dark" data-bs-theme="dark" style="margin-bottom: 1000px;">
    <div class="container-fluid">
        <a class="navbar-brand" href="./index.php">
            <img src="./favicon.ico" alt="Logo" width="28" height="28" class="d-inline-block align-text-top">
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
                        Pages
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="./chat/chat.php">Chat</a></li>
                        <li><a class="dropdown-item" href="./users.php">Users</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled">Private Messaging</a>
                </li>
            </ul>
            <ul class="navbar-nav mr-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <?= isset($_SESSION['user_id']) ? "
                <div class='dropdown show' style='display:inline-block'>
                    <a href='#' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                        <img id='navbar-user-pfp' src='" . $userpfp . "' alt='" . $_SESSION['username'] . "'>
                    </a>
                    <ul class='dropdown-menu dropdown-menu-end' style='right: 0; left: auto;'>
                        <li><a class='dropdown-item' href='./profile.php?id=" . $_SESSION['user_id'] . "'>User Profile</a></li>
                        <li><a class='dropdown-item' href='./friend-requests.php'>Friend Requests</a></li>
                        <li>
                            <hr class='dropdown-divider'>
                        </li>
                        <li><a class='dropdown-item' href='./account-settings.php'>Account Settings</a></li>
                    </ul>
                </div>'"
                        : '' ?>

                </li>
            </ul>
            <?= isset($_SESSION['user_id']) ? '<a class="btn btn-outline-success" href="./logout.php">Logout</a>' : '<a class="btn btn-outline-success" href="./login.php">Login</a>' ?>
        </div>
    </div>
</nav>