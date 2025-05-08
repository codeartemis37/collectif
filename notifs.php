<?php
include("modules/bdd.php");
include('modules/site.php');

// Requête préparée pour éviter les injections SQL
$sql = 'SELECT * FROM `notifs` WHERE `pseudo` LIKE ?';
$stmt = $bdd->prepare($sql);
$pseudoPattern = '%' . $_COOKIE['id'] . '%';
$stmt->execute([$pseudoPattern]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Conteneur des notifications
echo '<div style="
    height: 300px; 
    witdh: 300px; 
    overflow-y: auto; 
    border: 1px solid #ddd; 
    border-radius: 5px; 
    padding: 10px; 
    background-color: #fefefe; 
    font-family: Arial, sans-serif;
    font-size: 14px;
    color: #333;
">';

// Vérification s'il y a des messages
if (empty($messages)) {
    echo '<p style="text-align: center; color: #888;">Pas de notifications</p>';
} else {
    // Affichage des messages
    foreach ($messages as $message) {
        $backgroundColor = ($message['lu'] === 'no') ? '#ffe6e6' : '#f0f0f0'; // Rouge clair pour non lues, gris clair pour lues
        $icon = ($message['icone'] === 'reponse') ? '↪ ' : ''; // Icône si nécessaire
        
        echo '<div style="
            background-color: ' . $backgroundColor . '; 
            padding: 8px; 
            margin-bottom: 5px; 
            border-radius: 3px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        ">';
        echo '<a href="'. $site. '/modules/notif_lue.php?redirect=' . htmlspecialchars($message['id_notif']) . '">' . htmlspecialchars($icon) . htmlspecialchars($message['message']) . '</a>';
        echo '</div>';
    }
}

echo '</div>';
?>
