<?php
session_start();
require_once('bdd.php');

if (!isset($_SESSION['history'])) {
    $_SESSION['history'] = [];
}

function executeCommand($command) {
    global $bdd;
    $parts = explode(' ', $command);
    $action = $parts[0];

    switch ($action) {
        case 'help':
            return "Commandes disponibles :\n" .
                   "help - Affiche cette aide\n" .
                   "date - Affiche la date et l'heure actuelles\n" .
                   "clear - Efface l'écran\n" .
                   "get_last_timestamp [forum] - Affiche le timestamp du dernier message du forum indiqué\n" .
                   "get_last_items [forum] [limit] - Affiche les derniers éléments du forum\n" .
                   "get_stats [forum] - Affiche les statistiques du forum\n" .
                   "creer_salon [nom] - Crée un nouveau salon\n" .
                   "create_notif [pseudo] [message] [icone] [lien] - Crée une nouvelle notification\n" .
                   "insert_log [action] [personne] [details] - Ajoute un log\n" .
                   "execute_query [requête SQL] - Exécute une requête SQL personnalisée\n";
        case 'date':
            return date('Y-m-d H:i:s');
        case 'clear':
            $_SESSION['history'] = [];
            return "Écran effacé.";
        case 'get_last_timestamp':
            if (count($parts) < 2) return "Erreur : Veuillez spécifier un forum.";
            return json_encode(getLastTimestamp($bdd, "msg_" . $parts[1]));
        case 'get_last_items':
            if (count($parts) < 2) return "Erreur : Veuillez spécifier un forum et éventuellement une limite.";
            $limit = isset($parts[2]) ? intval($parts[2]) : 10;
            return json_encode(getLastItems($bdd, "msg_" . $parts[1], $limit));
        case 'get_stats':
            if (count($parts) < 2) return "Erreur : Veuillez spécifier un forum.";
            return json_encode(getStats($bdd, "msg_" . $parts[1]));
        case 'creer_salon':
            if (count($parts) < 2) return "Erreur : Veuillez spécifier un nom pour le salon.";
            return json_encode(CreateSalon($bdd, "msg_" . $parts[1]));
        case 'create_notif':
            if (count($parts) < 5) return "Erreur : Veuillez spécifier pseudo, message, icone et lien.";
            return json_encode(create_notif($bdd, $parts[1], $parts[2], $parts[3], $parts[4]));
        case 'insert_log':
            if (count($parts) < 4) return "Erreur : Veuillez spécifier action, personne et details.";
            $details = implode(' ', array_slice($parts, 3));
            return json_encode(insertLog($bdd, $parts[1], $parts[2], $details));
        case 'execute_query':
            if (count($parts) < 2) return "Erreur : Veuillez spécifier une requête SQL.";
            $query = implode(' ', array_slice($parts, 1));
            try {
                $stmt = $bdd->query($query);
                return generateTable($stmt->fetchAll(PDO::FETCH_ASSOC));
            } catch (PDOException $e) {
                return "Erreur SQL : " . $e->getMessage();
            }
        default:
            return "Commande non reconnue. Tapez 'help' pour voir les commandes disponibles.";
    }
}

function getLastTimestamp($bdd, $table) {
    $stmt = $bdd->query("SELECT MAX(date) as last_timestamp FROM `$table`");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getLastItems($bdd, $table, $limit = 10) {
    $stmt = $bdd->prepare("SELECT * FROM `$table` ORDER BY date DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStats($bdd, $table) {
    $stmt = $bdd->query("SELECT COUNT(*) as total, MIN(date) as oldest, MAX(date) as newest FROM `$table`");
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function CreateSalon($bdd, $table) {
    $bdd->exec("CREATE TABLE `$table` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `pseudo` varchar(10000) COLLATE utf8_unicode_ci NOT NULL,
        `messages` text COLLATE utf8_unicode_ci NOT NULL,
        `date` text COLLATE utf8_unicode_ci NOT NULL,
        `hash` text COLLATE utf8_unicode_ci NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12");
    return ["success" => "Le salon '$table' a été créé avec succès."];
}

function create_notif($bdd, $pseudo, $message, $icone, $lien) {
    $stmt = $bdd->prepare("INSERT INTO notifs (pseudo, message, icone, lien, lu, id_notif) VALUES (?, ?, ?, ?, 'no', MD5(NOW()))");
    $stmt->execute([$pseudo, $message, $icone, $lien]);
    return ["success" => true, "message" => "Notification créée avec succès"];
}

function insertLog($bdd, $action, $personne, $details) {
    $stmt = $bdd->prepare("INSERT INTO logs (action, personne, details, date) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$action, $personne, $details]);
    return ["success" => true, "message" => "Log inséré avec succès"];
}

function generateTable($data) {
    if (empty($data)) return "Aucun résultat.";
    $html = "<table><tr>";
    foreach (array_keys($data[0]) as $header) {
        $html .= "<th>" . htmlspecialchars($header) . "</th>";
    }
    $html .= "</tr>";
    foreach ($data as $row) {
        $html .= "<tr>";
        foreach ($row as $cell) {
            $html .= "<td>" . htmlspecialchars($cell) . "</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";
    return $html;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $commande = isset($_POST["commande"]) ? trim($_POST["commande"]) : "";
    if (!empty($commande)) {
        $resultat = executeCommand($commande);
        $_SESSION['history'][] = [
            'commande' => $commande,
            'resultat' => $resultat
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terminal PHP</title>
    <link rel="stylesheet" href="../css/wp-cli.css">
</head>
<body>
    <div id="terminal">
        <h2>Terminal PHP</h2>
        <?php
        foreach ($_SESSION['history'] as $entry) {
            echo "<p class='prompt'>" . htmlspecialchars($entry['commande']) . "</p>";
            echo "<pre>" . $entry['resultat'] . "</pre>";
        }
        ?>
        <form method="post" action="">
            <div class="prompt">
                <input type="text" id="commande" name="commande" autofocus>
                <input type="submit" value="Exécuter">
            </div>
        </form>
    </div>
</body>
</html>
