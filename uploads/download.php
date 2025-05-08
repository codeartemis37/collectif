<?phpfunction forcerTelechargement($nom, $situation){    if (is_file($situation)) { // Vérifie si le chemin pointe vers un fichier
        $poids = filesize($situation); // Obtient la taille du fichier en octets
        header('Content-Type: application/octet-stream');
        header('Content-Length: '. $poids);
        header('Content-disposition: attachment; filename='. $nom);
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        readfile($situation);
        exit();
    } else {
        echo "Erreur : Le chemin spécifié ne pointe pas vers un fichier valide.";
    }
}
// Appel de la fonction avec le nom du fichier et son emplacementforcerTelechargement($_GET['file'], './uploads/'.$_GET['file']);?>