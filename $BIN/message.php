<?php
session_start();
include('modules/bdd.php');
if (!$_SESSION['pseudo']) {
    header('location: connexion.php');
}
if (isset($_GET['id']) AND !empty($_GET['id'])) {

    $getid = $_GET['id'];
    $recupUser = $bdd->prepare('SELECT * FROM users WHERE id = ?');
    $recupUser->execute(array($getid));
    if ($recupUser->rowCount() > 0) {
        if (isset($_POST['envoyer'])) {
            $message = htmlspecialchars($_POST['message']);
            $inserMessage = $bdd->prepare('INSERT INTO messageprivé(message, id_destinataire, id_auteur)VALUES(?, ?, ?)');
            $inserMessage->execute(array($message, $getid, $_SESSION['id']));
        }
    } else {
        echo "Aucun utilisateur trouvé";
    }
} else {
    echo "Aucun identifiant trouvé";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message</title>
    <style>
        body {
            background-color: #0e0e0e;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        form {
            text-align: left;
            margin-bottom: 20px;
            max-width: 300px;
        }

        textarea {
            width: 100%;
            height: 100px;
            padding: 5px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            background-color: #0066ff;
            color: #fff;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #0047b3;
        }

        #messages {
            max-width: 400px;
        }

        #messages p {
            background-color: #444;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            word-wrap: break-word;
        }

        #messages p.red {
            color: red;
        }

        #messages p.blue {
            color: blue;
        }
    </style>
</head>

<body>
    <form action="" method="POST">
        <textarea name="message" placeholder="Type your message here"></textarea>
        <br><br>
        <input type="submit" name="envoyer" value="Send">
    </form>

    <section id='messages'>
        <?php
        $recupMessages = $bdd->prepare('SELECT * FROM messageprivé WHERE id_auteur = ? AND id_destinataire = ? OR id_auteur = ? AND id_destinataire = ?');
        $recupMessages->execute(array($_SESSION['id'], $getid, $getid, $_SESSION['id']));
        while ($message = $recupMessages->fetch()) {
            if ($message['id_destinataire'] == $_SESSION['id']) {
        ?>
                <p class="red"><?= $message['message']; ?></p>
            <?php
            } elseif ($message['id_destinataire'] == $getid) {
            ?>
                <p class="blue"><?= $message['message']; ?></p>
        <?php
            }
        }
        ?>
    </section>
</body>

</html>
