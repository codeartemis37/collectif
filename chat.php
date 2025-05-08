<?php
date_default_timezone_set('Europe/Paris');
include('modules/site.php');
include("modules/bdd.php");
require_once('modules/roles.php');
include('header.php');

$id = isset($_COOKIE['id']) ? $_COOKIE['id'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/chat.css">
    <link rel="stylesheet" href="css/connectes.css">
    <title>Chat</title>
</head>
<body>
<div class="chat-container">
    <div class="message-display">
        <div id="message" class="message-content"></div>
        <div class="form-container">
            <form id="chatForm" method="POST">
                <h4 style="color: #ffd700;">Connecté en tant que : <?php echo htmlspecialchars($id); ?></h4>
                <textarea class="textboxmsg" name="messages" id="messageInput" style="width:100%;"></textarea><br>
                <input type="submit" name="valider" value="Envoyer">
            </form>
        </div>
    </div>
    <div class="message-display" id="listeconnectes"></div>
    </div>
    <script>
    
    function ListConnectés() {
        $('#listeconnectes').load('connectes.php?forum=<?php echo urlencode(isset($_GET['forum']) ? $_GET['forum'] : ''); ?>');
    }
    
    var lastTimestamp = undefined;
    function checkForNewMessages(isme = 0) {
        ListConnectés();
        $.getJSON('<?= $site ?>modules/api.php?action=get_last_timestamp&context=<?php echo urlencode(isset($_GET['forum']) ? $_GET['forum'] : ''); ?>', function(data) {
            console.log("<?= $site ?>modules/api.php?action=get_last_timestamp&context=<?php echo urlencode(isset($_GET['forum']) ? $_GET['forum'] : ''); ?>");
            if (data && data.last_timestamp !== undefined && data.last_timestamp !== lastTimestamp) {
                if (lastTimestamp !== undefined && !isme) {
                    playBeep(1);
                }
                $('#message').load('<?= $site ?>loadMessage.php?forum=<?php echo urlencode(isset($_GET['forum']) ? $_GET['forum'] : ''); ?>');
                lastTimestamp = data.last_timestamp;
            }
        });
    }
    
    // Initialiser l'audio lors d'une interaction utilisateur
    document.addEventListener('click', initAudio, { once: true });
    setInterval(checkForNewMessages, 1000);

    // Gestion de l'envoi du formulaire sans rechargement
    $(document).ready(function() {
        $('#chatForm').submit(function(e) {
            e.preventDefault();
            var message = $('#messageInput').val();
            $.ajax({
                type: 'POST',
                url: '<?= $site ?>send_message.php',
                data: { 
                    messages: message, 
                    forum: '<?php echo urlencode(isset($_GET['forum']) ? $_GET['forum'] : ''); ?>'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#messageInput').val('');
                        checkForNewMessages(1);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erreur lors de l'envoi du message:", error);
                    alert("Une erreur est survenue lors de l'envoi du message.");
                }
            });
        });
    });
    </script>
</body>
</html>
