<?php
require_once "pdo.php";
date_default_timezone_set('UTC');

if (!isset($_SESSION["email"])) {
  include 'head.php';
  echo "<p align='center'>PLEASE LOGIN</p>";
  echo "<br />";
  echo "<p align='center'>Redirecting in 3 seconds</p>";
  header("refresh:3;url=login.php");
  die();
}

$stmt = $pdo->query(
  "SELECT * FROM chatlog"
);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['logout'])) {
  header("Location: logout.php");
  return;
}

if (isset($_POST['message']) && $_POST['account'] == $_SEESION['username']) {
  $stmta = $pdo->prepare(
    'INSERT INTO chatlog
  (message, message_date, account)
  VALUES (:msg, :msgd, :acc)'
  );

  $stmta->execute(
    array(
      ':msg' => $_POST['message'],
      ':msgd' => date(DATE_RFC2822),
      ':acc' => $_SESSION['name']
    )
  );
  $stmt = $pdo->query(
    "SELECT * FROM chatlog"
  );
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
  foreach ($_POST as $edit_msg) {
    $key = array_search($edit_msg, $_POST);


    $sql = "UPDATE chatlog SET message = :msg
            WHERE message_id = :message_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
      ':msg' => $edit_msg,
      ':message_id' => $key
    ));

    break;
  }
}
?>
<html>
<title>g4o2 chat</title>
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, minimum-scale=1.0">
<link rel="stylesheet" href="./css/chat.css?v=<?php echo time(); ?>">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
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
      <!--<img class="spinner" src="spinner.gif" alt="Loading..." />-->
      <?php
      require_once "pdo.php";
      function loadChat($pdo)
      {
        $stmt = $pdo->query(
          "SELECT * FROM chatlog"
        );
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) > 0) {
          echo "<p style='text-align:center;color: #ffa500;'>This is the start of all messages</p>";
          foreach ($rows as $row) {
            $pfpsrc = './assets/images/default-user-round.png';
            $user = "<a href='./profile.php?user={$row['account']}' class='account rainbow_text_animated'>" . $row['account'] . "</a>";

            $stmta = $pdo->prepare("SELECT pfp FROM account WHERE name=?");
            $stmta->execute([$row['account']]);
            $pfptemp = $stmta->fetchAll(PDO::FETCH_ASSOC);

            foreach ($pfptemp as $test) {
              if ($test['pfp'] != null) {
                $pfpsrc = $test['pfp'];
              }
            }
            $pfp = "<a class='pfp-link' href='./profile.php?user={$row['account']}'><img class='profile-image' src='$pfpsrc'></a>";


            $message = htmlentities($row["message"]);
            if (isset($_COOKIE['timezone'])) {

              //might break the chat 
              $timezone_offset_minutes = $_COOKIE['timezone'];
              $time = new DateTime($row["message_date"]);
              $minutes_to_add = ($timezone_offset_minutes);
              $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
              $stamp = $time->format('D, d M Y H:i:s');
              // here ^

            } else {
              $stamp = $row["message_date"];
            }
            $msg_parent_id = $row['message_id'] . "parent";
            $info = "<p class='stats'>{$user} ({$stamp})</p>";
            if ($row['account'] == $_SESSION['name']) {
              $editBtn = "<button class='btn chat-btn' onclick='handleEdit({$row['message_id']})'>Edit</button>";
            } else {
              $editBtn = "";
            }
            $msg = "<p class='msg' id='{$msg_parent_id}'><span id='{$row['message_id']}'>{$message}</span> " . $editBtn . "</p>";
            echo $pfp;
            echo "<div style='margin-left: 10px;margin-top: 18px;'>{$info}{$msg}</div>";
          }
        }
      };
      loadChat($pdo);
      ?>
    </div>
    <form id='form' autocomplete="off" method="post" action="chat.php">
      <div>
        <!--<input pattern=".{1,}" required title="3 characters minimum" id='message-input' type="text" name="message" size="60" placeholder="Enter message and submit" />-->
        <input id='message-input' type="text" name="message" size="60" style="width: 55vw;" placeholder="Enter message and submit" />
        <input class='button not-allowed' id="submit" type="submit" value="Chat" />
        <input class='button' id='logout' type="submit" name="logout" value="Logout" />
      </div>
    </form>
  </section>
  </script>
  <script type="text/javascript">
    function handleEdit(id) {
      let parent_id = id + "parent";
      let input_id = id + "input";
      let message = document.getElementById(id).innerText;
      document.getElementById(parent_id).innerHTML = `<form method='post'><input class='edit-input' id='${input_id}' type='text' style='width:90%' name=${id}> <input class='btn chat-btn' type='submit' value='Save'></form>`;
      document.getElementById(input_id).value = message;
    }

    let input = document.getElementById('message-input');
    input.focus();
    input.select();
    let pageBody = document.getElementsByTagName('body')[0];

    $("#submit").prop("disabled", true);
    $(input).keyup(function() {
      if (!$(input).val().replace(/\s/g, '').length) {
        $("#submit").prop("disabled", true);
        $('#submit').addClass("not-allowed")
      } else {
        $("#submit").prop("disabled", false);
        $('#submit').removeClass("not-allowed");
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
    $(document).ready(function() {
      setTimeout(
        function() {
          $("#chatcontent").removeClass("progress");
        }, 1000);
      console.log('%c Why are you here in the console?', 'background: #000; color: #ffa500');
      console.log('%c Dont try anything sus', 'background: #000; color: #ffa500');
      console.log("%c                                      \n    .->                .->            \n ,---(`-')   .---.(`-')----.  .----.  \n'  .-(OO )  / .  |( OO).-.  '\\_,-.  | \n|  | .-, \\ / /|  |( _) | |  |   .' .' \n|  | '.(_// '-'  ||\\|  |)|  | .'  /_  \n|  '-'  | `---|  |' '  '-'  '|      | \n `-----'      `--'   `-----' `------' ", 'background: #000; color: #ffa500')

    })

    function chatScroll() {
      let chat = document.getElementById('chatcontent')
      chat.scrollTop = chat.scrollHeight;
    }
    chatScroll()

    if (window.history.replaceState) {
      window.history.replaceState(null, null, window.location.href);
    }

    var timezone_offset_minutes = new Date().getTimezoneOffset();
    timezone_offset_minutes = timezone_offset_minutes == 0 ? 0 : -timezone_offset_minutes;

    document.cookie = "timezone=" + timezone_offset_minutes;

    /*let inverval = window.setInterval(function() {
      $.ajax({
        url: "messages.php",
        success: function(data) {
          document.getElementById("chatcontent").innerHTML = data
        }
      });
      let chat = document.getElementById("chatcontent")
      if (chat.scrollTop >= (chat.scrollHeight - chat.offsetHeight) - 100) {
        chatScroll()
      }
    }, 1000)*/

    let inverval = window.setInterval(function() {
      $.ajax({
        url: "msglength.php",
        success: function(data) {
          let chat = document.getElementById("chatcontent");
          let chatLength = (chat.childElementCount - 1) / 2;

          if (data != chatLength) {
            $.ajax({
              url: "messages.php",
              success: function(data) {
                document.getElementById("chatcontent").innerHTML = data;
                let chat = document.getElementById("chatcontent")
                if (chat.scrollTop >= (chat.scrollHeight - chat.offsetHeight) - 100) {
                  chatScroll()
                }
                console.log('chat updated')
              }
            });
          }
        }
      });
    }, 1000)
  </script>



  <?php
  if (isset($_SESSION['email'])) {
    $pfpsrc = './assets/images/default-user-round.png';

    $stmta = $pdo->prepare("SELECT * FROM account WHERE user_id=?");
    $stmta->execute([$_SESSION['user_id']]);
    $pfptemp = $stmta->fetchAll(PDO::FETCH_ASSOC);

    foreach ($pfptemp as $test) {
      if ($test['pfp'] != null) {
        $pfpsrc = $test['pfp'];
      }
      $_SESSION['name'] = $test['name'];
      $_SESSION['email'] = $test['email'];
    }
    $pfp = "<a class='pfp-link' href='./profile.php?user={$test['name']}'><img class='profile-image' style='border-radius: 100px;height: 60px;width:60px;'' src='$pfpsrc'></a>";
    $main = "<p style='margin-top: 20px;font-size: 20px;font-family: monospace;'>{$_SESSION['name']}</p><p style='font-family: monospace;'>{$_SESSION['email']}</p>";
    $actions = '<a href="edit-account.php">Edit Account</a> | <a href="logout.php">Logout</a>';
  }
  ?>
  </script>
</body>