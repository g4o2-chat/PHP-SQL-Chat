<?php
session_start();
ob_start();
ini_set('display_errors', 0);
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
</head>

<body>
    <div class="container">
        <div class="box box-1">
            <ul id="users"></ul>
        </div>
        <div class="box box-2">
            <div id="messages"></div>
            <button onclick="scrollBottom()" id="scrollBottom" title="Go to the bottom"><img src="../assets/images/up-arrow.svg" style="height: 20px;"></button>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="../scripts/main.js"></script>
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
        const user_id = '<?=$_SESSION['user_id']?>';
        // const user_id = parseInt(sessionStorage.getItem("user_id"));
        let msg_load_index = 1;
        let first_load_messages = true;
        let chat_entire_load = false;

        function chatScroll() {
            messages.scrollTop = messages.scrollHeight;
        }

        function scrollBottom() {
            messages.scrollTo({
                top: messages.scrollHeight,
                left: 0,
                behavior: 'smooth'
            });
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
            // console.log(chatlog)
            if (chatlog.length == 0 && chat_entire_load == false) {
                const pTag = document.createElement("p");
                pTag.innerText = "The conversation starts here.";
                pTag.style.textAlign = "center";
                pTag.style.color = "orange";
                const firstChild = document.getElementById('messages').firstChild;
                document.getElementById('messages').insertBefore(pTag, firstChild);
                chat_entire_load = true
            }
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
            msg_load_index += 25;
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