<?php
header('Content-Type: application/json');
include("bdd.php");

// Fonctions utilitaires
function sendError($message) {
    echo json_encode(["error" => $message]);
    exit;
}

function getParam($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

// Configuration
$action = getParam('action', 'default_action');
$context = getParam('context', 'general');

// Fonctions principales

function NbNotifs($bdd, $person) {
    try {
        $stmt = $bdd->prepare("SELECT COUNT(*) as nb_notifs FROM notifs WHERE pseudo = :pseudo AND lu = 'no'");
        $stmt->execute([':pseudo' => $person]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        return ["error" => "Erreur de base de données: " . $e->getMessage()];
    }
}

function getLastTimestamp($bdd, $table) {
    try {
        $stmt = $bdd->query("SELECT MAX(date) as last_timestamp FROM `msg_$table`");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ["last_timestamp" => $result['last_timestamp'] ?? '0'];
    } catch (PDOException $e) {
        return ["error" => "Erreur de base de données: " . $e->getMessage()];
    }
}

function GetListeConnectes($bdd, $table) {
    try {
        $query = "SELECT DISTINCT pseudo FROM `msg_$table` WHERE STR_TO_DATE(date, '%d/%m/%Y %H:%i:%s') >= DATE_SUB(NOW(), INTERVAL 120 SECOND)";
        $stmt = $bdd->query($query);
        $connectedUsers = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        // return $query;
        return ["connected_users" => $connectedUsers];
    } catch (PDOException $e) {
        return ["error" => "Erreur de base de données: " . $e->getMessage()];
    }
}





function getLastItems($bdd, $table, $limit = 10) {
    try {
        $stmt = $bdd->prepare("SELECT * FROM `msg_$table` ORDER BY date DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $items ? ["items" => $items] : ["error" => "Aucun élément trouvé"];
    } catch (PDOException $e) {
        return ["error" => "Erreur de base de données: " . $e->getMessage()];
    }
}

function getStats($bdd, $table) {
    try {
        $stmt = $bdd->query("SELECT COUNT(*) as total, MIN(date) as oldest, MAX(date) as newest FROM `msg_$table`");
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);
        return $stats ? ["stats" => $stats] : ["error" => "Impossible de récupérer les statistiques"];
    } catch (PDOException $e) {
        return ["error" => "Erreur de base de données: " . $e->getMessage()];
    }
}

function IsMute($bdd, $nom) {
    try {
        $stmt = $bdd->prepare("SELECT timestamp_debut, duree FROM mutes WHERE pseudo = :nom");
        $stmt->execute(['nom' => $nom]);
        $mute = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($mute) {
            $timestamp_fin = $mute['timestamp_debut'] + $mute['duree'];
            $timestamp_actuel = time();

            if ($timestamp_fin < $timestamp_actuel) {
                // Le mute est terminé
                return false;
            } else {
                // Le mute est toujours actif
                return true;
            }
        } else {
            // L'utilisateur n'est pas muté
            return false;
        }
    } catch (PDOException $e) {
        return ["error" => "Erreur de base de données: " . $e->getMessage()];
    }
}

function CreateSalon($bdd, $table) {
    try {
        $bdd->exec("CREATE TABLE `msg_$table` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `pseudo` varchar(10000) COLLATE utf8_unicode_ci NOT NULL,
            `messages` text COLLATE utf8_unicode_ci NOT NULL,
            `date` text COLLATE utf8_unicode_ci NOT NULL,
            `hash` text COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12");
        
        return ["success" => "Le salon 'msg_$table' a été créé avec succès."];
    } catch (PDOException $e) {
        return ["error" => "Erreur lors de la création du salon: " . $e->getMessage()];
    }
}



function insertLog($bdd, $action, $personne, $details) {
    try {
        $stmt = $bdd->prepare("INSERT INTO logs (action, personne, details, date) VALUES (:action, :personne, :details, NOW())");
        $stmt->execute([':action' => $action, ':personne' => $personne, ':details' => $details]);
        return ["success" => true, "message" => "Log inséré avec succès"];
    } catch (PDOException $e) {
        return ["error" => "Erreur d'insertion du log: " . $e->getMessage()];
    }
}

function create_notif($bdd, $pseudo, $message, $icone, $lien) {
    try {
        $stmt = $bdd->prepare("INSERT INTO notifs (pseudo, message, icone, lien, lu, id_notif) VALUES (:pseudo, :message, :icone, :lien, 'no', MD5(NOW()))");
        $stmt->execute([':pseudo' => $pseudo, ':message' => $message, ':icone' => $icone, ':lien' => $lien]);
        return ["success" => true, "message" => "Notification créée avec succès"];
    } catch (PDOException $e) {
        return ["error" => "Erreur lors de la création de la notification: " . $e->getMessage()];
    }
}

function SupprNotifs($bdd, $pseudo) {
    try {
        $stmt = $bdd->prepare("DELETE FROM `notifs` WHERE `pseudo` = :pseudo");
        $stmt->execute(['pseudo' => $pseudo]);
        return ["success" => true, "message" => "Notifications supprimées avec succès"];
    } catch (PDOException $e) {
        return ["error" => "Erreur lors de la suppression des notifications: " . $e->getMessage()];
    }
}


function executeCustomQuery($bdd, $query) {
    try {
        $stmt = $bdd->query($query);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (count($results) > 0) {
            return ["success" => true, "result" => $results];
        } else {
            return ["success" => true, "result" => "La requête n'a retourné aucun résultat."];
        }
    } catch (PDOException $e) {
        return ["error" => "Erreur lors de l'exécution de la requête : " . $e->getMessage()];
    }
}

function createMute($bdd, $pseudo, $duree) {
    try {
        $timestamp_debut = time();
        $stmt = $bdd->prepare("INSERT INTO mutes (pseudo, timestamp_debut, duree) VALUES (:pseudo, :timestamp_debut, :duree)");
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':timestamp_debut' => $timestamp_debut,
            ':duree' => $duree
        ]);
        return ["success" => true, "message" => "Mute créé avec succès pour $pseudo pendant $duree secondes"];
    } catch (PDOException $e) {
        return ["error" => "Erreur lors de la création du mute: " . $e->getMessage()];
    }
}

function deMute($bdd, $pseudo, $duree) {
    try {
        $stmt = $bdd->prepare("DELETE FROM `mutes` WHERE `pseudo` = :pseudo");
        $stmt->execute([
            ':pseudo' => $pseudo
        ]);
        return ["success" => true, "message" => "Mute supprimé avec succès pour $pseudo pendant $duree secondes"];
    } catch (PDOException $e) {
        return ["error" => "Erreur lors de la suppression du mute: " . $e->getMessage()];
    }
}

function ArgentActuel($bdd, $pseudo) {
    try {
        // Tentative de récupération de l'argent pour le pseudo
        $stmt = $bdd->prepare("SELECT `valeur` FROM `argent` WHERE `pseudo` = :pseudo");
        $stmt->execute([':pseudo' => $pseudo]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Le pseudo existe, on retourne la valeur
            $argent = $result['valeur'];
            return ["success" => true, "message" => "Récupération réussie", "argent" => $argent];
        } else {
            // Le pseudo n'existe pas, on crée une nouvelle entrée
            $insertStmt = $bdd->prepare("INSERT INTO `argent` (`pseudo`, `valeur`) VALUES (:pseudo, 0)");
            $insertStmt->execute([':pseudo' => $pseudo]);
            
            if ($insertStmt->rowCount() > 0) {
                return ["success" => true, "message" => "Nouveau compte créé avec 0 argent", "argent" => 0];
            } else {
                return ["success" => false, "message" => "Échec de la création du nouveau compte"];
            }
        }
    } catch (PDOException $e) {
        return ["success" => false, "message" => "Erreur lors de l'opération: " . $e->getMessage()];
    }
}


function ModifierArgent($bdd, $pseudo, $montant) {
    try {
        // Récupérer l'argent actuel
        $resultatActuel = ArgentActuel($bdd, $pseudo);
        
        if (!$resultatActuel['success']) {
            return $resultatActuel; // Retourner l'erreur si la récupération a échoué
        }
        
        $argentActuel = $resultatActuel['argent'];
        
        // Vérifier si on essaie de retirer plus d'argent que disponible
        if ($montant < 0 && abs($montant) > $argentActuel) {
            return [
                "success" => false,
                "message" => "Erreur : Tentative de retirer plus d'argent que le solde disponible. Solde actuel : $argentActuel"
            ];
        }
        
        $nouvelArgent = $argentActuel + $montant;
        
        // Mettre à jour l'argent
        $stmt = $bdd->prepare("UPDATE `argent` SET `valeur` = :valeur WHERE `pseudo` = :pseudo");
        $stmt->execute([
            ':valeur' => $nouvelArgent,
            ':pseudo' => $pseudo
        ]);
        
        if ($stmt->rowCount() > 0) {
            $message = $montant >= 0 ? "ajouté" : "retiré";
            return [
                "success" => true, 
                "message" => "Argent $message avec succès pour $pseudo. Nouveau solde : $nouvelArgent",
                "nouveauSolde" => $nouvelArgent
            ];
        } else {
            return ["success" => false, "message" => "Aucune modification effectuée pour $pseudo"];
        }
    } catch (PDOException $e) {
        return ["success" => false, "message" => "Erreur lors de la modification de l'argent: " . $e->getMessage()];
    }
}


// Traitement des actions
switch ($action) {
    case 'get_last_timestamp':
        $table = $context === 'forum' ? "msg_" . getParam('forum', 'general') : $context;
        $result = getLastTimestamp($bdd, $table);
        break;

    case 'get_liste_connectes':
        $table = $context === 'forum' ? "msg_" . getParam('forum', 'general') : $context;
        $result = GetListeConnectes($bdd, $table);
        break;

    case 'get_last_items':
        $table = $context === 'forum' ? "msg_" . getParam('forum', 'general') : $context;
        $limit = getParam('limit', 10);
        $result = getLastItems($bdd, $table, $limit);
        break;

    case 'get_stats':
        $table = $context === 'forum' ? "msg_" . getParam('forum', 'general') : $context;
        $result = getStats($bdd, $table);
        break;

    case 'nb_notifs':
        $person = getParam('person');
        if (!$person) {
            $result = ["error" => "Le paramètre 'person' est requis pour obtenir le nombre de notifications"];
        } else {
            $result = NbNotifs($bdd, $person);
            if (!is_array($result)) {
                $result = ["nb_notifs" => $result];
            }
        }
        break;

    case 'create_salon':
        $nom = getParam('nom', '');
        if (empty($nom)) {
            $result = ["error" => "Le nom du salon est requis."];
        } else {
            $table = "msg_" . preg_replace('/[^a-zA-Z0-9_]/', '_', $nom);
            $result = CreateSalon($bdd, $table);
        }
        break;

    case 'supprnotifs':
        $pseudo = getParam('pseudo', '');
        if (empty($pseudo)) {
            $result = ["error" => "Le pseudo est requis."];
        } else {
            $result = SupprNotifs($bdd, $pseudo);
        }
        break;

    case 'ismute':
        $nom = getParam('pseudo', '');
        if (empty($nom)) {
            $result = ["error" => "Le nom est requis."];
        } else {
            $result = IsMute($bdd, $nom);
        }
        break;

    case 'insert_log':
        $logAction = getParam('log_action');
        $personne = getParam('personne');
        $details = getParam('details');

        if (!$logAction || !$personne || !$details) {
            $result = ["error" => "Les paramètres log_action, personne et details sont requis pour insérer un log"];
        } else {
            $result = insertLog($bdd, $logAction, $personne, $details);
        }
        break;

    case 'create_notif':
        $pseudo = getParam('pseudo');
        $message = getParam('message');
        $icone = getParam('icone');
        $lien = getParam('lien');

        if (!$pseudo || !$message || !$icone || !$lien) {
            $result = ["error" => "Les paramètres pseudo, message, icone et lien sont requis pour créer une notification"];
        } else {
            $result = create_notif($bdd, $pseudo, $message, $icone, $lien);
        }
        break;

    case 'execute_query':
        $query = getParam('query');
        if (!$query) {
            $result = ["error" => "Le paramètre 'query' est requis pour exécuter une requête SQL"];
        } else {
            $result = executeCustomQuery($bdd, $query);
        }
        break;

    case 'create_mute':
        $pseudo = getParam('pseudo');
        $duree = getParam('duree');
        if (!$pseudo || !$duree) {
            $result = ["error" => "Les paramètres 'pseudo' et 'duree' sont requis pour créer un mute"];
        } else {
            $result = createMute($bdd, $pseudo, $duree);
        }
        break;

    case 'argent_actuel':
        $pseudo = getParam('pseudo');
        if (!$pseudo) {
            $result = ["error" => "Le paramètre 'pseudo' est requis"];
        } else {
            $result = ArgentActuel($bdd, $pseudo);
        }
        break;

    case 'modifier_argent':
        $pseudo = getParam('pseudo');
        $nb = getParam('nb');
        if (!$pseudo || !$nb) {
            $result = ["error" => "Les paramètres 'pseudo' et 'nb' sont requis"];
        } else {
            $result = ModifierArgent($bdd, $pseudo, $nb);
        }
        break;

    case 'demute':
        $pseudo = getParam('pseudo');
        if (!$pseudo) {
            $result = ["error" => "Le paramètre 'pseudo' sont requis pour supprimer un mute"];
        } else {
            $result = deMute($bdd, $pseudo);
        }
        break;

    default:
        $result = ["error" => "Action non reconnue"];
}

// Envoi de la réponse
echo json_encode($result);
?>
