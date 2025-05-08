<?php
include_once('site.php');
include('bdd.php');

$root_path = $site;
$current_path = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

function verifyCredentials($username, $password) {
    global $bdd; // Connexion à la base de données

    $query = "SELECT password FROM users WHERE username = :username";
    $stmt = $bdd->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        return $password == $row['password'];
    }

    return false;
}

if ($current_path === $root_path) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if (verifyCredentials($username, $password)) {
            setcookie('id', $username, time() + 3600, '/', '', isset($_SERVER['HTTPS']), true);
            setcookie('password', $password, time() + 3600, '/', '', isset($_SERVER['HTTPS']), true);
        } else {
            header("Location: " . $site);
            exit();
        }
    }
} else {
    if (isset($_COOKIE['id']) && isset($_COOKIE['password'])) {
        if (!verifyCredentials($_COOKIE['id'], $_COOKIE['password'])) {
            // Cookie invalide, supprimer le cookie et rediriger vers index.php
            setcookie('id', '', time() - 3600, '/');
            setcookie('password', '', time() - 3600, '/');
            header("Location: " . $site);
            exit();
        }
    } else {
        // Pas de cookie 'id' ni 'password', rediriger vers index.php
        header("Location: " . $site);
        exit();
    }
}

// Si l'authentification est réussie, continuer avec le reste du script
?>
