<?php
include('modules/site.php');
include('header.php');

// Sécurisation des variables GET
$connecte = isset($_COOKIE['id']) ? htmlspecialchars($_COOKIE['id']) : '';
$id = isset($_COOKIE['id']) ? htmlspecialchars($_COOKIE['id']) : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des forums</title>
    <link rel="stylesheet" href="./css/listeforums.css">
</head>
<body>
    <div id="bas">
        <div id="divliste">
<?php
try {
    include('modules/bdd.php');
    
    // Requête pour obtenir toutes les tables commençant par "MSG_"
    $sql = "SHOW TABLES LIKE 'MSG_%'";
    $result = $bdd->query($sql);

    if ($result->rowCount() > 0) {
        echo "Liste des forums:<br>";
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $row[0] = str_replace('msg_', '', $row[0]);
            echo "            <a href='chat.php?forum=". $row[0]. "' id='bouton'>". $row[0]. "</a><br>";
        }
    } else {
        echo "Aucune table trouvée commençant par 'MSG_'.";
    }
    
    // Fermer la connexion
    $bdd = null;
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
        </div>
    </div>
</body>
</html>
