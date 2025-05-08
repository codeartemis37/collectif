<?php
include("bdd.php");

// Vérification et assainissement du paramètre 'redirect'
$id_notif = isset($_GET['redirect']) ? filter_var($_GET['redirect'], FILTER_SANITIZE_STRING) : '';

if (!empty($id_notif)) {
    try {
        // Démarrage de la transaction
        $bdd->beginTransaction();

        // Mise à jour de la notification pour la marquer comme lue
        $stmt = $bdd->prepare("UPDATE `notifs` SET `lu` = 'yes' WHERE `id_notif` = :id_notif");
        $stmt->execute(['id_notif' => $id_notif]);
        
        // Récupération de l'URL de redirection
        $stmt = $bdd->prepare("SELECT `lien` FROM `notifs` WHERE `id_notif` = :id_notif");
        $stmt->execute(['id_notif' => $id_notif]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Validation de la transaction
        $bdd->commit();
        
        if ($result && isset($result['lien'])) {
            // Redirection vers le lien stocké dans la base de données
            header("Location: " . $result['lien']);
            exit();
        } else {
            throw new Exception("Lien de redirection non trouvé");
        }
    } catch (Exception $e) {
        // En cas d'erreur, annulation de la transaction
        if ($bdd->inTransaction()) {
            $bdd->rollBack();
        }
        error_log("Erreur lors de la mise à jour de la notification : " . $e->getMessage());
        echo "Une erreur est survenue. Veuillez réessayer plus tard.";
    }
} else {
    echo "ID de notification invalide";
}
?>
