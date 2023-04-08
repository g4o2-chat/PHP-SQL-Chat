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
    <link rel="stylesheet" href="./css/chat.css">
    <style>
        #chatcontent {
            display: none;
        }

        #form {
            display: none;
        }

        #loading-screen {
            text-align: center;
            font-size: 20px;
            color: #000;
        }

        #loading-screen img {
            margin-bottom: 30px;
            height: 180px;
            width: 180px;
            animation-name: logo-spin;
            animation-duration: 3s;
            animation-iteration-count: infinite;
        }

        @keyframes logo-spin {
            25% {
                transform: rotate(90deg);
            }

            50% {
                transform: rotate(190deg);
            }

            75% {
                transform: rotate(270deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="../scripts/main.js"></script>
</head>

<body>
    <div class="container">
        <div class="box box-1">
            <ul id="users">
                <!-- <li>
                    <img src="./assets/images/default-user.png" alt="Profile picture for username">
                    <span>username</span>
                </li> -->
            </ul>
        </div>
        <div class="box box-2">
            <div id="messages">
                <!-- <div class="message-container">
                    <img class="message-sender-pfp" src="./assets/images/default-user.png" alt="Profile picture for g4o2">
                    <div class="message-content">
                        <div class="message-header">
                            <span class="message-sender">g4o2</span>
                            (<time class="message-datetime" datetime="2022-11-04T16:33:55Z">Fri, 04 Nov 2022 16:33:55
                                +0000</time>)
                        </div>
                        <div class="message-body">
                            <span class="message">a random message</span>
                        </div>
                    </div>
                </div> -->
            </div>
            <form id="message-form">
                <input type="text" id="message-input" placeholder="Type your message...">
                <button type="submit" id="submit">Send</button>
            </form>
        </div>
        <div class="box box-3">
            <p>&copy; <span id="footer-year">2023</span> G4O2 Chat. All rights reserved.</p>
        </div>
    </div>
    </div>
    <script src="./node_modules/socket.io/client-dist/socket.io.js"></script>
    <script src="./index.js"></script>
    <script>
        const url = "https://g4o2-api.maxhu787.repl.co";
        // const url = "http://localhost:3000";
        const socket = io(url);
        const messages = document.getElementById('messages');
        const form = document.getElementById('message-form');
        const input = document.getElementById('message-input');
        const submitBtn = document.getElementById('submit');
        const user_id = '<?= $_SESSION['user_id'] ?>';
        let msg_load_index = 1;
        let first_load_messages = true;

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

        fetch(url.concat('/db/users'))
            .then((response) => response.text())
            .then((body) => {
                users = JSON.parse(body);
                users = users['responce'];
                // console.log(users)
                for (let i = 0; i < users.length; i++) {
                    let pfp = '../assets/images/default-user-square.png';
                    if (users[i]['pfp']) {
                        pfp = users[i]['pfp'];
                    }
                    const user = new User(users[i]['username'], pfp, 0);
                    user.addUserToUsers();
                }
            });

        socket.emit('load-message', msg_load_index);
        socket.on('load-message', function(chatlog) {
            for (let i = 0; i < chatlog.length; i++) {
                const message = new Message(chatlog[i]['message'], chatlog[i]['message_date'], chatlog[i]['user_id'], chatlog[i]['username'], chatlog[i]['pfp']);
                message.appendMessageBefore();
            }
            if (first_load_messages) {
                chatScroll()
            } else {
                $(messages).scrollTop($(messages).scrollTop() + 60 * chatlog.length);
            }
            first_load_messages = false;
            msg_load_index += 10;
        })

        socket.emit('user-connect', user_id);
        socket.on('user-connect', function(user_id) {
            fetch(url.concat(`/db/users/${user_id}`))
                .then((response) => response.text())
                .then((body) => {
                    data = JSON.parse(body);
                    let username = data['username']
                    let userconnect = `<p style=''>User ${username} connected</s>`;
                });
        })

        socket.on('message-submit', function(messageDetails) {
            const message = new Message(messageDetails['message'], messageDetails['message_date'], messageDetails['user_id'], messageDetails['username'], messageDetails['pfp']);
            message.appendMessage();
            chatScroll()
        });

        socket.on('message-error', function(err) {
            document.location.href = `https://http.cat/${err}`;
        })

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (input.value) {
                let date = new Date().toUTCString()
                let message = input.value
                messageDetails = {
                    message: message,
                    message_date: date,
                    user_id: user_id
                }
                socket.emit('message-submit', messageDetails);
                input.value = '';
                let noMsgElement = document.getElementById('no-msg');
                if (noMsgElement && getComputedStyle(noMsgElement).display !== "none") {
                    noMsgElement.style.display = "none";
                }
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


        messages.addEventListener("scroll", function() {
            if (messages.scrollTop === 0) {
                socket.emit('load-message', msg_load_index);
            }
        });
    </script>
</body>

</html>