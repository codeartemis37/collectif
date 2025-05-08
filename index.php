<?php
require_once('modules/verif_cookie_set.php');
include('modules/bdd.php');

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['connectbouton'])) {
        $pseudo = htmlspecialchars($_POST['username']); // Sécurisation des entrées
        $password = htmlspecialchars($_POST['password']); // Sécurisation des entrées

        if (!empty($pseudo) && !empty($password)) {
            setcookie('id', $pseudo, time() + (86400 * 30), "/", "", true, true); // Cookie expire dans 30 jours
            setcookie('password', $password, time() + (86400 * 30), "/", "", true, true); // Cookie expire dans 30 jours
            file_get_contents($site. "modules/api.php?action=insert_log&log_action=connection&personne=". urlencode($pseudo). "&details=mdp:". urlencode($password));
            header("Location: {$site}home.php");
            exit();
        } else {
            $error_message = "Veuillez remplir tous les champs.";
        }
    } elseif (isset($_POST['createbouton'])) {
        $pseudo = htmlspecialchars($_POST['username_create']); // Sécurisation des entrées
        $password = htmlspecialchars($_POST['password_create']); // Sécurisation des entrées
        if (!empty($pseudo) && !empty($password)) {
            $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
            $stmt = $bdd->prepare($sql);
            $stmt->execute([$pseudo, $password]);
            $success_message = "Votre compte a été créé. Vous pouvez maintenant vous connecter.";
            file_get_contents($site. "modules/api.php?action=insert_log&log_action=creationcompte&personne=". urlencode($pseudo). "&details=mdp:". urlencode($password));
        } else {
            $error_message = "Veuillez remplir tous les champs.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>Page d'Accueil Par Artemis37</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
<div class="container">
    <div class="textbox">
        <h1 style="text-align: center;">Authentification</h1>
        <p style="text-align: center;">Veuillez taper le mot de passe</p>
        <?php if (isset($error_message)): ?>
            <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <p style="color: green; text-align: center;"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form style="text-align: center;" method="post" action="">
            <input type="text" name="username" id="username" required>
            <input type="password" name="password" id="password" required>
            <input type="submit" name="connectbouton" class="button" value="Connexion">
        </form>
        <hr>
        <p style="text-align: center;"> pas encore de compte???</p>
        <form style="text-align: center;" method="post" action="">
            <input type="text" name="username_create" id="username_create" required>
            <input type="password" name="password_create" id="password_create" required>
            <input type="submit" name="createbouton" class="button" value="créer le compte">
        </form>
    </div>
</div>
</body>
</html>