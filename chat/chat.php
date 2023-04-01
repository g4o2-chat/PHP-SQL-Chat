<?php
require_once "../pdo.php";
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
    <main>
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
            const url = "http://localhost:3000"
            const socket = io(url);
            const messages = document.getElementById('chatcontent');
            const form = document.getElementById('form');
            const input = document.getElementById('message-input');
            const submitBtn = document.getElementById('submit');
            const user_id = '<?= $_SESSION['user_id'] ?>';

            socket.emit('user-connect', user_id);
            socket.emit('load-messages', user_id);

            function chatScroll() {
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
            fetch(url.concat('/db/chatlog'))
                .then((response) => response.text())
                .then((body) => {
                    chatlog = JSON.parse(body);
                    chatlog = chatlog['responce'];
                    for (let i = 0; i < chatlog.length; i++) {
                        let username;
                        let user_id;
                        let pfpsrc;
                        let data = chatlog;
                        fetch(url.concat(`/db/users/${data[i]['user_id']}`))
                            .then((response) => response.text())
                            .then((body) => {
                                user_json = JSON.parse(body);
                                pfpsrc = '../assets/images/default-user-round.png';
                                if (user_json['pfp']) {
                                    pfpsrc = user_json['pfp']
                                }
                                username = user_json['username'];
                                user_id = user_json['user_id'];
                                let pfp = `<a class="pfp-link" href="./profile.php?id=${user_id}"><img class="profile-image" src="${pfpsrc}"></a>`;
                                let user = `<a href="./profile.php?id=${user_id}" class="account rainbow_text_animated">${username}</a>`;
                                let message = data[i]["message"];
                                message = escapeHtml(message);
                                let msg_parent_id = data[i]['message_id'] + "parent";
                                let message_date = data[i]["message_date"];
                                let localDate = new Date(message_date.toLocaleString());

                                let info = `<p class="stats">${user} (${localDate})</p>`;
                                let editBtn = "";

                                if (user_id == <?= $_SESSION['user_id'] ?>) {
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
                fetch(url.concat(`/db/users/${user_id}`))
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
                fetch(url.concat(`/db/users/${messageDetails['user_id']}`))
                    .then((response) => response.text())
                    .then((body) => {
                        user_json = JSON.parse(body);
                        let pfpsrc = '../assets/images/default-user-round.png';
                        if (user_json['pfp']) {
                            pfpsrc = user_json['pfp']
                        }
                        let username = user_json['username'];
                        let pfp = `<a class="pfp-link" href="./profile.php?id=${messageDetails['user_id']}"><img class="profile-image" src="${pfpsrc}"></a>`;
                        let user = `<a href="./profile.php?id=${messageDetails['user_id']}" class="account rainbow_text_animated">${username}</a>`;
                        let message = escapeHtml(messageDetails["message"]);
                        let msg_parent_id = messageDetails['message_id'] + "parent";
                        let message_date = messageDetails["message_date"];
                        let localDate = new Date(message_date.toLocaleString());

                        let info = `<p class="stats">${user} (${localDate})</p>`;
                        let editBtn = "";

                        if (messageDetails['user_id'] == <?= $_SESSION['user_id'] ?>) {
                            editBtn = `<button class="btn chat-btn" onclick="handleEdit(${messageDetails['message_id']})">Edit</button>`;
                        }
                        let msg = `<p class="msg" id="${msg_parent_id}"><span id="${messageDetails['message_id']}">${message}</span> ${editBtn}</p>`;
                        let div = `<div style="margin-left: 10px;margin-top: 18px;">${info}${msg}</div>`;

                        $("#chatcontent").append(pfp);
                        $("#chatcontent").append(div);
                        chatScroll();
                    })

            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (input.value) {
                    let date = new Date().toUTCString()
                    let message = input.value
                    messageDetails = {
                        user_id: user_id,
                        message: message,
                        message_date: date
                    }
                    fetch(url.concat(`/db/insert/message?message=${message}&message_date=${date}&user_id=${user_id}`))
                        .then((response) => response.text())
                        .then((body) => {
                            responce = JSON.parse(body);
                            alert(responce);
                        })
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
    </main>
</body>

</html>