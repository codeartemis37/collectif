<?php
include('../modules/site.php');
include('../header.php');
$id = isset($_COOKIE['id']) ? $_COOKIE['id'] : '';
?>

<?php
$dir = "./uploads/";
$files = [];

if (is_dir($dir)) {
    $files = array_diff(scandir($dir), array('..', '.'));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = '';
    if (is_dir($dir)) {
        if(isset($_POST['confirm_delete'])) {
            foreach($files as $file) {
                $filePath = $dir . $file;
                if(is_file($filePath)) {
                    unlink($filePath);
                }
            }
            $message = 'Tous les fichiers du répertoire "uploads" ont été supprimés avec succès.';
            $files = []; // Vider le tableau des fichiers après la suppression
        } else {
            $message = 'Suppression annulée.';
        }
    } else {
        $message = "Le répertoire 'uploads' n'existe pas.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des fichiers</title>
</head>
<body>
    <div class="container">
        <h1>Gestion des fichiers</h1>
        
        <?php if (isset($message)): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="confirmation-box">
            <h3>Fichiers dans le répertoire "uploads" :</h3>
            <div class="file-list">
                <?php
                if (empty($files)) {
                    echo "<p>Aucun fichier trouvé dans le répertoire uploads.</p>";
                } else {
                    echo "<ul>";
                    foreach ($files as $file) {
                        echo "<li>" . htmlspecialchars($file) . "</li>";
                    }
                    echo "</ul>";
                }
                ?>
            </div>
            <form method="post">
                <label>Êtes-vous sûr de vouloir supprimer tous les fichiers du répertoire "uploads" ?</label>
                <button type="submit" name="confirm_delete">Confirmer</button>
                <button type="submit" name="cancel_delete">Annuler</button>
            </form>
        </div>
    <?php
        include('footer.php');
    ?>
    </div>
</body>
</html>
