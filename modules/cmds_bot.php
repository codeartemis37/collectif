<?php
// Liste des modérateurs
require_once('roles.php');


// file_get_contents($site. "modules/api.php?action=modifier_argent&pseudo=". urlencode($id). "&nb=". 5);
// file_get_contents($site. "modules/api.php?action=insert_log&log_action=ajout_argent&personne=". urlencode($id). "&details=nb:". urlencode(5));

function execute_bot($command, $site, $user) {
    if ($command[0] !== '!') return null;
    $parts = explode(' ', trim($command));
    $commandName = strtolower(substr($parts[0], 1));
    $args = array_slice($parts, 1);
    
    $commands = array(
        'random' => function() { return "Nombre aléatoire entre 1 et 100 : " . mt_rand(1, 100); },
        'say' => function() use ($args) { return implode(' ', $args); },
        'stat_user' => function() use ($site, $args) {
            if (count($args) < 2) return "Usage: !stat_user [pseudo] [salon]";
            $query = "SELECT COUNT(*) as count FROM msg_{$args[1]} WHERE pseudo = '{$args[0]}'";
            $result = json_decode(file_get_contents($site . "modules/api.php?action=execute_query&query=" . urlencode($query)), true);
            return $result && $result['success'] ? "{$args[0]} a posté {$result['result'][0]['count']} messages dans ce salon" : "Erreur lors de la récupération des statistiques.";
        },
        'date_creation_compte' => function() use ($site, $args) { return get_date_info($site, $args[0], 'creationcompte', "Le compte de %s a été créé le %s."); },
        'date_derniere_connection' => function() use ($site, $args) { return get_date_info($site, $args[0], 'connection', "Le compte de %s a été connecté pour la derniere fois le %s.", 'DESC'); },
        'date_premiere_connection' => function() use ($site, $args) { return get_date_info($site, $args[0], 'connection', "Le compte de %s a été connecté pour la premiere fois le %s."); },
        'report' => function() use ($args, $user) {
            if (count($args) < 1) return "Usage: !report [hash msg]";
            return "@modo signalement message ". $args[0]. " par ". $user;
        },
        'donner' => function() use ($args, $user, $site) {
            if (count($args) < 2 || !is_numeric($args[1]) || $args[1] <= 0) {
                return "Usage: !donner [personne] [montant positif]";
            }

            $destinataire = urlencode($args[0]);
            $montant = intval($args[1]);
            $api = $site . "modules/api.php?action=";
            
            $argentActuel = json_decode(file_get_contents($api . "argent_actuel&pseudo=" . urlencode($user)), true);
            if (!$argentActuel['success'] || $argentActuel['argent'] < $montant) {
                return "Fonds insuffisants pour ce transfert.";
            }
            
            $debit = json_decode(file_get_contents($api . "modifier_argent&pseudo=" . urlencode($user) . "&nb=-" . $montant), true);
            $credit = json_decode(file_get_contents($api . "modifier_argent&pseudo=" . $destinataire . "&nb=" . $montant), true);
            if (!$debit['success'] || !$credit['success']) {
                if ($credit['success']) {
                    file_get_contents($api . "modifier_argent&pseudo=" . urlencode($user) . "&nb=" . $montant);
                }
                return "Erreur lors du transfert. Opération annulée.";
            }
            
            $log = file_get_contents($api . "insert_log&log_action=transfert_argent&personne=" . urlencode($user) . "&details=vers:" . $destinataire . ",nb:" . $montant);
            return "Transfert réussi ! Solde restant : " . ($argentActuel['argent'] - $montant);
        }
    );

    $mod_commands = array(
        'suppr' => function() use ($site, $args) {
            if (count($args) < 2) return "Usage: !suppr [salon] [hash]";
            $deleteQuery = "DELETE FROM `msg_{$args[0]}` WHERE `hash` = '{$args[1]}'";
            file_get_contents($site . "modules/api.php?action=execute_query&query=" . urlencode($deleteQuery));
            return "La requête de suppression pour le message avec le hash {$args[1]} a été exécutée, pas de reponse"; },
        'mute' => function() use ($site, $args) {
            if (count($args) < 2) return "Usage: !mute [nom] [temps]";
            file_get_contents($site. "modules/api.php?action=create_mute&pseudo=". $args[0]. "&duree=". $args[1]);
            return "Mute de ". $args[0]. " pour temps de ". $args[1];
        },
        'demute' => function() use ($site, $args) {
            if (count($args) < 1) return "Usage: !demute [nom]";
            file_get_contents($site. "modules/api.php?action=demute&pseudo=". $args[0]);
            return "Mute de ". $args[0]. " supprimé";
        },
        'ismute' => function() use ($site, $args) {
            if (count($args) < 1) return "Usage: !ismute [nom]";
            if (file_get_contents($site. "modules/api.php?action=ismute&pseudo=". $args[0]) == "true") {
                return $args[0]. " est mute";
            }
            return $args[0]. " n'est pas mute";
        },
        'vider' => function() use ($site, $args) {
            if (count($args) < 1) return "Usage: !vider [salon]";
            file_get_contents($site. "modules/videur.php?forum=". $args[0]);
            return "Salon ". $args[0]. " vidé";
        },
        'supprnotifs' => function() use ($site, $args) {
            if (count($args) < 1) return "Usage: !supprnotifs [nom]";
            file_get_contents($site. "modules/api.php?action=supprnotifs&pseudo=". $args[0]);
            return "Notifs de ". $args[0]. " supprimées";
        }
    );

    if (isset($commands[$commandName])) {
        return $commands[$commandName]();
    } elseif (isset($mod_commands[$commandName])) {
        if (a_le_role($user, "moderateur")) {
            return $mod_commands[$commandName]();
        } else {
            return "Vous n'avez pas les permissions nécessaires pour exécuter cette commande.";
        }
    } else {
        return "Commande inconnue. Les commandes disponibles sont : " . implode(', ', array_merge(array_keys($commands), array_keys($mod_commands)));
    }
}

function get_date_info($site, $pseudo, $action, $message, $order = 'ASC') {
    $query = "SELECT `date` FROM `logs` WHERE `action` = '$action' AND `personne` = '$pseudo' ORDER BY `date` $order LIMIT 1";
    $result = json_decode(file_get_contents($site . "modules/api.php?action=execute_query&query=" . urlencode($query)), true);
    return $result && $result['success'] && isset($result['result'][0]['date']) 
        ? sprintf($message, $pseudo, date('d/m/Y', strtotime($result['result'][0]['date'])))
        : "Aucune information trouvée pour le compte de $pseudo.";
}
?>
