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
    <link rel="stylesheet" href="./css/chat.css?v=<?php echo time(); ?>">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="../scripts/main.js"></script>
</head>

<body>

    <section id="page-header">
        <h1 id="index-page-link"><a href="./index.php">g4o2&nbsp;chat</a></h1>
        <section style="overflow: auto;" id="guide">
            <p>Press <kbd>Enter</kbd> to submit message</p>
            <p>Press <kbd>/</kbd> to select <kbd>Esc</kbd> to deselect</p>
        </section>
    </section>
    <section>
        <div class="progress" id="chatcontent">
            <p style='text-align:center;color: #ffa500;'>This is the start of all messages</p>

            <a class="pfp-link" href="./profile.php?id=1"><img class="profile-image" src="../assets/images/default-user-round.png"></a>
            <div style="margin-left: 10px;margin-top: 18px;">
                <p class="stats"><a href="./profile.php?id=1" class="account rainbow_text_animated">g4o2</a> Sat, 04 Mar 2023 05:33:08</p>
                <p class="msg" id="6parent"><span id="6">chatScrolljjj</span> <button class="btn chat-btn" onclick="handleEdit(6)">Edit</button></p>
            </div>
        </div>
        <form id='form' action="" autocomplete="off">
            <div>
                <input id='message-input' autocomplete="off" type="text" name="message" size="60" style="width: 55vw;" placeholder="Enter message and submit" />
                <button class='button' id="submit">Send</button>
                <!-- <input class='button not-allowed' id="submit" type="submit" value="Chat" /> -->
                <input class='button' id='logout' type="submit" name="logout" value="Logout" />
            </div>
        </form>
    </section>
    <script src="./node_modules/socket.io/client-dist/socket.io.js"></script>
    <script>
        // $.getJSON('https://g4o2-api.maxhu787.repl.co/db/test', function(data) {
        //     console.log(data)
        // });
    </script>
    <script>
        // const socket = io("https://g4o2-api.maxhu787.repl.co");
        const socket = io("http://localhost:3000");
        var messages = document.getElementById('chatcontent');
        var form = document.getElementById('form');
        var input = document.getElementById('message-input');
        var submitBtn = document.getElementById('submit');
        var username = "<?= $_SESSION['username'] ?>";
        /*do {
            username = prompt('Username');
        } while (username.match(/[^a-zA-Z0-9_]+/g) || username == "")
        if (username) {
            socket.emit('user-connect', username);
            socket.emit('load-messages', username);
        }
*/
        socket.emit('user-connect', username);
        socket.emit('load-messages', username);

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/'/g, "&#x27;")
                .replace(/"/g, "&quot;");
        }
        fetch('http://localhost:3000/db/test')
            .then((response) => response.text())
            .then((body) => {
                data = JSON.parse(body);
                data = data['responce'];
                for (let i = 0; i < data.length; i++) {
                    let pfpsrc = '../assets/images/default-user-round.png';
                    let pfp = `<a class="pfp-link" href="./profile.php?id=${data[i]['user_id']}"><img class="profile-image" src="${pfpsrc}"></a>`;
                    let user = `<a href="./profile.php?id=${data[i]['user_id']}" class="account rainbow_text_animated">${data[i]['account']}</a>`;
                    let message = escapeHtml(data[i]["message"]);
                    let msg_parent_id = data[i]['message_id'] + "parent";
                    let stamp = data[i]["message_date"];
                    let info = `<p class="stats">${user} (${stamp})</p>`;
                    let editBtn = "";

                    if (data[i]['user_id'] === sessionStorage.getItem("user_id")) {
                        editBtn = `<button class="btn chat-btn" onclick="handleEdit(${data[i]['message_id']})">Edit</button>`;
                    }
                    let msg = `<p class="msg" id="${msg_parent_id}"><span id="${data[i]['message_id']}">${message}</span> ${editBtn}</p>`;
                    let div = `<div style="margin-left: 10px;margin-top: 18px;">${info}${msg}</div>`;

                    $("#chatcontent").append(pfp);
                    $("#chatcontent").append(div);
                    messages.scrollTop = messages.scrollHeight;
                }
            });
        socket.on('user-connect', function(username) {
            var item = document.createElement('div');
            item.textContent = `User ${username} connected`;
            messages.appendChild(item);
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

            /*
                        let item = document.createElement('div');
                        let username = messageDetails.account;
                        let date = messageDetails.message_date;
                        date.toLocaleString();
                        let message = messageDetails.message;
                        let messageFiltered = escapeHtml(message)
                        item.innerHTML = `<div class='message-container'><p class='message-info'>${username} ${date}</p><p class='message'><span style='word-wrap: break-word;'>${messageFiltered}</span></p></div>`;
                        messages.appendChild(item);*/
            messages.scrollTop = messages.scrollHeight;
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (input.value) {
                let date = new Date().toLocaleString();
                let message = input.value
                messageDetails = {
                    user_id: <?= $_SESSION['user_id'] ?>,
                    account: username,
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
    </script>
</body>

</html>