<?php
include('modules/site.php');
include("modules/bdd.php");
include('modules/emoji.php');
include('modules/cmds_bot.php');
require_once('modules/roles.php');
date_default_timezone_set("Europe/Paris");

header('Content-Type: application/json');

// Désactiver l'affichage des erreurs PHP (les erreurs seront loggées mais pas affichées)
ini_set('display_errors', 0);
error_reporting(E_ALL);


$id = isset($_COOKIE['id']) ? $_COOKIE['id'] : '';
$mute = false;


function auto_ping($text) {
    global $site, $roles;
    $special_roles = array('modo' => $roles['moderateur'], 'moderateurs' => $roles['moderateur']);
    preg_match_all('/@([a-zA-Z0-9_]+)/', $text, $matches);
    foreach ($matches[1] as $pseudo) {
        $users_to_ping = isset($special_roles[$pseudo]) ? $special_roles[$pseudo] : array($pseudo);
        foreach ($users_to_ping as $user) {
            $api_url = $site. "modules/api.php?action=create_notif&pseudo=" . urlencode($user) . "&message=" . urlencode("Vous avez été mentionné dans un message") . "&icone=mention&lien=" . urlencode($site . "chat.php?forum=" . urlencode($_POST['forum']));
            file_get_contents($api_url);
        }
    }
}

// Vérifier si l'utilisateur n'est pas mute
if (file_get_contents($site . "modules/api.php?action=ismute&pseudo=" . urlencode($id)) == "false") {
    if (isset($_POST['messages']) && !empty($_POST['messages'])) {
        $message = nl2br(htmlspecialchars($_POST['messages']));
        $heure = date('d/m/Y H:i:s');
        
        auto_ping($message);
        
        $table = 'MSG_' . (isset($_POST['forum']) ? $_POST['forum'] : 'default');
        $hash = hash("sha256", $id . $heure);
        $bdd->prepare("INSERT INTO $table (pseudo, messages, date, hash) VALUES (?, ?, ?, ?)")->execute(array($id, $message, $heure, $hash));
        file_get_contents($site. "modules/api.php?action=insert_log&log_action=envoi_message&personne=". urlencode($id). "&details=texte:". urlencode($message));
        
        file_get_contents($site. "modules/api.php?action=modifier_argent&pseudo=". urlencode($id). "&nb=". 1);
        file_get_contents($site. "modules/api.php?action=insert_log&log_action=ajout_argent&personne=". urlencode($id). "&details=nb:". urlencode(1));
        
        $bot_message = auto_BBCode(execute_bot($message, $site, $id));
        auto_ping($bot_message);
        if ($bot_message) {
            $bdd->prepare("INSERT INTO $table (pseudo, messages, date, hash) VALUES (?, ?, ?, ?)")->execute(array("bot", $bot_message, $heure, hash("sha256", "bot" . $heure)));
        }
        
        echo json_encode(['success' => true, 'message' => 'Message envoyé avec succès']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Le message est vide']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Vous avez été mute']);
}
?>
