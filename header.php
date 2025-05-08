<?php
include("modules/bdd.php");
include('modules/site.php');
require_once('modules/roles.php');
require_once('modules/verif_cookie_set.php');

// Assurez-vous que le cookie 'id' est défini
$id = $_COOKIE['id'];

// Fonction pour générer une couleur basée sur une chaîne
function getColorFromString($string) {
    return '#' . substr(md5($string), 0, 6);
}

$imagePath = $site. "css/img/". htmlspecialchars($id). ".jpg";
$firstLetter = $id ? strtoupper(substr($id, 0, 1)) : '?';
$bgColor = getColorFromString($id);

// Fonction pour obtenir le nombre de notifications
function getNotificationCount() {
    global $site, $id;
    $url = $site. "modules/api.php?action=nb_notifs&person=" . urlencode($id);
    $response = @file_get_contents($url);
    if ($response === false) {
        return 0; // Retourne 0 si l'API n'est pas accessible
    }
    $data = json_decode($response, true);
    return isset($data['nb_notifs']) ? intval($data['nb_notifs']) : 0;
}

$notificationCount = getNotificationCount();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Collectif</title>
    <link rel="stylesheet" href="<?= $site ?>css/chat.css">
    <link rel="stylesheet" href="<?= $site ?>css/header.css">
    <link rel="icon" type="image/jpg" href="<?= $site ?>favicon.jpg"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="header">
        <h1>Le Collectif</h1>
    </div>
    <div class="button-container">
        <button id="toggle-button" onclick="toggleMenu()">Afficher le menu</button>
        <div style="display: flex; align-items: center;">
            <div class="profile-container">
                <?php if (@file_get_contents($imagePath) !== false): ?>
                    <img id="notifs-button" onclick="toggleNotifs()" src="<?= htmlspecialchars($imagePath) ?>" alt="Profile" class="profile-image">
                <?php else: ?>
                    <div id="notifs-button" onclick="toggleNotifs()" class="profile-letter" style="background-color: <?= htmlspecialchars($bgColor) ?>;">
                        <?= htmlspecialchars($firstLetter) ?>
                    </div>
                <?php endif; ?>
                <span class="notification-badge" <?= $notificationCount > 0 ? '' : 'style="display:none;"' ?>><?= $notificationCount ?></span>
            </div>
            <button onclick="EasterEgg()" style="margin-left: 10px;">
                Role: <?php echo plus_grand_role_de($id); ?>
            </button>
            <span class="span-argent"></span>
        </div>
    </div>

    <!-- Menu déroulant -->
    <div id="menu" style="display: none;">
        <nav class="main-nav">
            <ul class="nav-menu">
                <li><a href="<?= $site ?>home.php">Accueil</a></li>
                <li><a href="<?= $site ?>uploads/">Envoi de fichier</a></li>
                <li><a href="<?= $site ?>listeforums.php">Liste des forums</a></li>
                <li><a href="<?= $site ?>chat.php?forum=message">Chat</a></li>
                <li><a href="<?= $site ?>modules/wp-cli.php">Wp-Cli</a></li>
            </ul>
        </nav>
        <div style="text-align: center;">
            <table style="background-color: #333; border: 1px solid #000; margin: 0 auto;"> 
                <tr>
                    <th>
                        <h1>news</h1>
                        <iframe src="<?= $site ?>/news/?month=<?= date('m') ?>&year=<?= date('Y') ?>" width="450" height="325"></iframe>
                        <iframe src="<?= $site ?>/loadMessage.php?forum=message" width="250" height="325"></iframe>
                    </th>
                </tr>
            </table>
        </div>
    </div>

    <div id="notifs" style="display: none; z-index: 99; position: absolute; right: 150px;">
        <!-- Le contenu des notifications sera chargé ici -->
    </div>

    <script>
    function toggleMenu() {
        var menu = document.getElementById("menu");
        var button = document.getElementById("toggle-button");
        if (menu.style.display === "none") {
            menu.style.display = "block";
            button.textContent = "Masquer le menu";
        } else {
            menu.style.display = "none";
            button.textContent = "Afficher le menu";
        }
    }

    function toggleNotifs() {
        var notifs = document.getElementById("notifs");
        notifs.style.display = notifs.style.display === "none" ? "block" : "none";
    }

    function EasterEgg() {
        alert('Vous avez trouvé l\'easter egg !');
    }

    let audioContext;
    function initAudio() {
        audioContext = new (window.AudioContext || window.webkitAudioContext)();
    }
    
    function playBeep(type) {
        if (!audioContext || audioContext.state === 'suspended') {
            initAudio();
        }
    
        if (audioContext.state === 'running') {
            const oscillator = audioContext.createOscillator();
            oscillator.type = "sine";
            oscillator.frequency.setValueAtTime(type * 800, audioContext.currentTime);
            oscillator.connect(audioContext.destination);
            oscillator.start();
            setTimeout(() => oscillator.stop(), 500);
        }
    }

    let ancientCount = 0;
    function updateNotificationCount() {
        $.ajax({
            url: '<?= $site ?>modules/api.php?action=nb_notifs&person=<?php echo urlencode($id); ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                var count = parseInt(data.nb_notifs);
                $('.notification-badge').text(count);
                if (count > 0) {
                    $('.notification-badge').show();
                    if (count != ancientCount) {
                        playBeep(2);
                        ancientCount = count;
                    }
                } else {
                    $('.notification-badge').hide();
                }
            },
            error: function() {
                console.error('Erreur lors de la récupération des notifications');
            }
        });
    }

    function updateArgent() {
        $.ajax({
            url: '<?= $site ?>modules/api.php?action=argent_actuel&pseudo=<?= urlencode($id); ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    var argent = parseInt(data.argent);
                    $('.span-argent').text("Solde: " + argent);
                } else {
                    console.error('Erreur: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de la récupération des crédits:', error);
            }
        });
    }


    $(document).ready(function() {
        setInterval(load_notifs, 1000);
        updateNotificationCount();
        setInterval(updateNotificationCount, 1000); // Mise à jour toutes les secondes
        setInterval(updateArgent, 1000); // Mise à jour toutes les secondes
    });

    function load_notifs() {
        $('#notifs').load('<?= $site ?>/notifs.php');
    }

    </script>
</body>
</html>
