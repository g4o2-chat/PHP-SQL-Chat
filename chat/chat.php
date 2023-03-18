<?php
require_once "../pdo.php";
date_default_timezone_set('Asia/Taipei');

if (!isset($_SESSION["email"])) {
    include 'head.php';
    echo "<p align='center'>PLEASE LOGIN</p>";
    echo "<br />";
    echo "<p align='center'>Redirecting in 3 seconds</p>";
    header("refresh:3;url=../login.php");
    die();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>g4o2 chat</title>
    <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="./css/chat.new.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="../scripts/main.js"></script>
</head>

<body>
    <div class="progress" id="chatcontent">
        <p style='text-align:center;color: #ffa500;'>This is the start of all messages</p>
        <div id="new-message-alert">New message</div>
    </div>
    <form id='form' action="" autocomplete="off">
        <input id='message-input' autocomplete="off" type="text" name="message" size="60" placeholder="Enter message and submit" />
        <button class='button' id="submit">Send</button>
    </form>
    <script src="./node_modules/socket.io/client-dist/socket.io.js"></script>
    <script>
        // const socket = io("https://g4o2-api.maxhu787.repl.co");
        const socket = io("http://localhost:3000");
        var messages = document.getElementById('chatcontent');
        var form = document.getElementById('form');
        var input = document.getElementById('message-input');
        var submitBtn = document.getElementById('submit');
        var user_id = '<?= $_SESSION['user_id'] ?>';
        /*do {
            username = prompt('Username');
        } while (username.match(/[^a-zA-Z0-9_]+/g) || username == "")
        if (username) {
            socket.emit('user-connect', username);
            socket.emit('load-messages', username);
        }*/
        socket.emit('user-connect', user_id);
        socket.emit('load-messages', user_id);

        function isScrolledToBottom() {
            var difference = messages.scrollHeight - messages.offsetHeight - messages.scrollTop;
            return difference <= 1;
        }

        function chatScroll() {
            /*if (messages.scrollTop >= (messages.scrollHeight - messages.offsetHeight) - 100) {
                messages.scrollTop = messages.scrollHeight;
            } else {
                messages.scrollTop = messages.scrollHeight;
            }*/
            messages.scrollTop = messages.scrollHeight;
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/'/g, "&#x27;")
                .replace(/"/g, "&quot;");
        }
        messages.addEventListener("scroll", (event) => {
            console.log(messages.scrollTop);
            console.log(messages.scrollHeight);
            console.log(messages.offsetHeight);
        });
        fetch('http://localhost:3000/db/chatlog')
            .then((response) => response.text())
            .then((body) => {
                data = JSON.parse(body);
                data = data['responce'];
                for (let i = 0; i < data.length; i++) {
                    fetch(`http://localhost:3000/db/users/${data[i]['user_id']}`)
                        .then((response) => response.text())
                        .then((body) => {
                            user_json = JSON.parse(body);
                            let pfpsrc = '../assets/images/default-user-round.png';
                            if (user_json['pfp']) {
                                pfpsrc = user_json['pfp']
                            }
                            let username = user_json['username'];
                            let pfp = `<a class="pfp-link" href="./profile.php?id=${data[i]['user_id']}"><img class="profile-image" src="${pfpsrc}"></a>`;
                            let user = `<a href="./profile.php?id=${data[i]['user_id']}" class="account rainbow_text_animated">${username}</a>`;
                            let message = escapeHtml(data[i]["message"]);
                            let msg_parent_id = data[i]['message_id'] + "parent";
                            let stamp = data[i]["message_date"];
                            let info = `<p class="stats">${user} (${stamp})</p>`;
                            let editBtn = "";

                            if (data[i]['user_id'] == <?= $_SESSION['user_id'] ?>) {
                                editBtn = `<button class="btn chat-btn" onclick="handleEdit(${data[i]['message_id']})">Edit</button>`;
                            }
                            let msg = `<p class="msg" id="${msg_parent_id}"><span id="${data[i]['message_id']}">${message}</span> ${editBtn}</p>`;
                            let div = `<div style="margin-left: 10px;margin-top: 18px;">${info}${msg}</div>`;

                            $("#chatcontent").append(pfp);
                            $("#chatcontent").append(div);
                            chatScroll();
                        })
                }
            });

        socket.on('user-connect', function(user_id) {
            fetch(`http://localhost:3000/db/users/${user_id}`)
                .then((response) => response.text())
                .then((body) => {
                    data = JSON.parse(body);
                    let username = data['username']
                    let userconnect = `<p style=''>User ${username} connected</s>`;
                    $("#chatcontent").append(userconnect);
                    chatScroll();
                });
        })

        socket.on('message-submit', function(messageDetails) {
            let pfpsrc = '../assets/images/default-user-round.png';
            let pfp = `<a class="pfp-link" href="./profile.php?id=${messageDetails['user_id']}"><img class="profile-image" src="${pfpsrc}"></a>`;
            let user = `<a href="./profile.php?id=${messageDetails['user_id']}" class="account rainbow_text_animated">${messageDetails['account']}</a>`;
            let message = escapeHtml(messageDetails['message']);
            let msg_parent_id = messageDetails['message_id'] + "parent";
            let stamp = messageDetails["message_date"];
            let info = `<p class="stats">${user} (${stamp})</p>`;
            let editBtn = "";

            if (messageDetails['user_id'] === sessionStorage.getItem("user_id")) {
                editBtn = `<button class="btn chat-btn" onclick="handleEdit(${messageDetails['message_id']})">Edit</button>`;
            }
            let msg = `<p class="msg" id="${msg_parent_id}"><span id="${messageDetails['message_id']}">${message}</span> ${editBtn}</p>`;
            let div = `<div style="margin-left: 10px;margin-top: 18px;">${info}${msg}</div>`;

            $("#chatcontent").append(pfp);
            $("#chatcontent").append(div);

            chatScroll();
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (input.value) {
                let date = new Date().toLocaleString();
                let message = input.value
                messageDetails = {
                    user_id: user_id,
                    message: message,
                    message_date: date
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

        function handleEdit(id) {
            alert(`Message editing not supported yet | message_id: ${id}`);
        }
    </script>
</body>

</html>