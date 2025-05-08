<?php
include('modules/site.php');
include("modules/bdd.php");

$msgmax = 20;
// Utilisation de requête préparée pour éviter les injections SQL
$stmt = $bdd->prepare('SELECT * FROM `msg_' . $_GET["forum"] . '` ORDER BY id DESC LIMIT :msgmax');
$stmt->bindParam(':msgmax', $msgmax, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
$messageCount = count($messages);

function getColorFromString($string) {
    return '#' . substr(md5($string), 0, 6);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="css/loadmessage.css">
</head>
<body>
    <div class="date-display">
        On est le : <span><?= date('d/m/y H:i:s') ?></span>
    </div>

    <?php if ($messageCount >= $msgmax): ?>
        <div class="alert">
            <p>Attention : Le nombre de messages dépasse la limite autorisée !</p>
            <a href="<?php echo $site; ?>/modules/videur.php?forum=<?php echo $_GET["forum"]; ?>" target="_blank">Vider la base de données</a>
        </div>
    <?php else: ?>
        <div class="notalert">
            <p>Nombre de messages : <?= $messageCount ?></p>
        </div>
    <?php endif; ?>

    <?php foreach ($messages as $message): 
        $pseudo = htmlspecialchars($message['pseudo']);
        $firstLetter = strtoupper(substr($pseudo, 0, 1));
        $bgColor = getColorFromString($pseudo);
        $imagePath = "css/img/{$pseudo}.jpg";
    ?>
        <div class="message-container">
            <div class="message-header">
                <?php if (file_exists($imagePath)): ?>
                    <img src="<?= $imagePath ?>" alt="Profile" class="profile-image">
                <?php else: ?>
                    <div class="profile-letter" style="background-color: <?= $bgColor ?>;">
                        <?= $firstLetter ?>
                    </div>
                <?php endif; ?>
                <h4><?= $pseudo ?></h4><span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[<?= $message['date'] ?>]</span>
                </h4><span style="font-size: 0.8em; color: #808080;">[<?= $message['hash'] ?>]</span>
            </div>
            <p><?= nl2br(($message['messages'])) ?></p>
        </div>
    <?php endforeach; ?>

</body>
</html>
