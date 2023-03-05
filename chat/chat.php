<?php
require_once "../pdo.php";
date_default_timezone_set('Asia/Taipei');

if (!isset($_SESSION["email"])) {
    include 'head.php';
    echo "<p align='center'>PLEASE LOGIN</p>";
    echo "<br />";
    echo "<p align='center'>Redirecting in 3 seconds</p>";
    header("refresh:3;url=login.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Document</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta charset="utf-8" />
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0" /> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <meta name="description" content="A chat application made with PHP and SQL">
    <meta name="keywords" content="">
    <meta name="author" content="Hu Kaixiang">

    <meta name="robots" content="index, follow">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/style.css?v=<?php echo time(); ?>">
    <!-- <link rel="stylesheet" href="https://kit.fontawesome.com/b60596f9d0.css" crossorigin="anonymous"> -->

    <link rel="apple-touch-icon" sizes="180x180" href="../favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon/favicon-16x16.png">
    <link rel="manifest" href="../favicon/site.webmanifest">
    <link rel="mask-icon" href="../favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <meta property="fb:page_id" content="">
    <meta property="og:title" content="g4o2-chat">
    <meta property="og:image" content="https://user-images.githubusercontent.com/103299803/196030342-1b944181-2ba5-4c1b-b762-c6e6827cc5cd.PNG">
    <meta property="og:description" content="A chat application made with PHP and SQL">
    <meta property="og:url" content="https://php-sql-chat.maxhu787.repl.co">
    <meta property="og:site_name" content="g4o2-chat">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="g4o2-chat">
    <meta name="twitter:image" content="https://user-images.githubusercontent.com/103299803/196030342-1b944181-2ba5-4c1b-b762-c6e6827cc5cd.PNG">
    <meta name="twitter:description" content="A chat application made with PHP and SQL">
    <meta name="twitter:url" content="https://php-sql-chat.maxhu787.repl.co/">
    <meta name="twitter:site" content="https://php-sql-chat.maxhu787.repl.co/">

    <meta name="google" value="notranslate">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/b60596f9d0.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="../scripts/main.js"></script>
</head>

<body>
    <div id="messages">
    </div>
    <form id='form' action="" autocomplete="off">
        <div>
            <input id='input' autocomplete="off" type="text" name="message" size="60" style="width: 55vw;" placeholder="Enter message and submit" /><button id="send">Send</button>
        </div>
    </form>
    <script src="./node_modules/socket.io/client-dist/socket.io.js"></script>
    <script>
        var socket = io("https://g4o2-api.maxhu787.repl.co/");
        var messages = document.getElementById('messages');
        var form = document.getElementById('form');
        var input = document.getElementById('input');
        var submitBtn = document.getElementById('send');
        do {
            username = prompt('Username');
        } while (username.match(/[^a-zA-Z0-9_]+/g) || username == "")
        if (username) {
            socket.emit('user-connect', username);
            socket.emit('load-messages', username);
        }
        socket.on('load-messages', function(data) {
            console.log(data);
            for (let i = 0; i < data.length; i++) {
                let pfpLink = '#';
                let item = document.createElement('div');
                let username = data[i]['username'];
                let date = data[i]['date'];
                date.toLocaleString();
                let message = data[i]['message'];
                let messageFiltered = message.replace(/[\u00A0-\u9999<>\&]/g, function(i) {
                    return '&#' + i.charCodeAt(0) + ';';
                });
                item.innerHTML = `<div class='message-container'><p class='message-info'>${username} ${date}</p><p class='message'><span style='word-wrap: break-word;'>${messageFiltered}</span></p></div>`;
                messages.appendChild(item);
                let chat = document.getElementById('messages')
                chat.scrollTop = chat.scrollHeight;
            }
        })

        socket.on('user-connect', function(username) {
            var item = document.createElement('div');
            item.textContent = `User ${username} connected`;
            messages.appendChild(item);
            window.scrollTo(0, document.body.scrollHeight);
        })

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&'); // $& means the whole matched string
        }

        function replaceAll(str, find, replace) {
            return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
        }
        socket.on('message-submit', function(messageDetails) {
            let pfpLink = '#';
            let item = document.createElement('div');
            let username = messageDetails.username;
            let date = messageDetails.date;
            date.toLocaleString();
            let message = messageDetails.message;
            let messageFiltered = message.replace(/[\u00A0-\u9999<>\&]/g, function(i) {
                return '&#' + i.charCodeAt(0) + ';';
            });
            item.innerHTML = `<div class='message-container'><p class='message-info'>${username} ${date}</p><p class='message'><span style='word-wrap: break-word;'>${messageFiltered}</span></p></div>`;
            messages.appendChild(item);
            let chat = document.getElementById('messages')
            chat.scrollTop = chat.scrollHeight;
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (input.value) {
                let date = new Date().toLocaleString();
                messageDetails = {
                    username: replaceAll(username, '<', '<'),
                    message: replaceAll(input.value, '<', '<'),
                    date: date
                }
                socket.emit('message-submit', messageDetails);
                input.value = '';
            }
        });

        window.addEventListener("keydown", event => {
            if ((event.keyCode == 191)) {
                if (input === document.activeElement) {
                    return;
                } else {
                    input.focus();
                    input.select();
                    event.preventDefault();
                }
            }
            if ((event.keyCode == 27)) {
                if (input === document.activeElement) {
                    document.activeElement.blur();
                    window.focus();
                    event.preventDefault();
                }
            }
        });
    </script>
</body>

</html>