<?php
include("bdd.php");

// Vérification et assainissement du paramètre 'forum'
$table = isset($_GET['forum']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['forum']) : '';

// Vérification que la table existe
$stmt = $bdd->prepare("SHOW TABLES LIKE :table");
$stmt->execute(['table' => "msg_$table"]);

if ($stmt->rowCount() > 0) {
    // Utilisation de requêtes préparées pour plus de sécurité
    $bdd->beginTransaction();
    
    try {
        $stmt = $bdd->prepare("TRUNCATE TABLE `msg_$table`");
        $stmt->execute();
        
        $stmt = $bdd->prepare("INSERT INTO `msg_$table` (pseudo, messages, date, hash) VALUES (:pseudo, :message, :date, :hash)");
        $currentDate = date('d/m/Y H:i:s');
        $hash = hash("sha256", 'System'. $currentDate);
        $stmt->execute([
            'pseudo' => 'System',
            'message' => 'Serveur réinitialisé',
            'date' => $currentDate,
            'hash' => $hash
        ]);
        
        $bdd->commit();
        echo "Réinitialisation réussie";
    } catch (Exception $e) {
        $bdd->rollBack();
        echo "Erreur lors de la réinitialisation : " . $e->getMessage();
    }
} else {
    echo "Table non trouvée";
}
?>
