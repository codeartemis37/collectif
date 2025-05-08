<?php
include('modules/site.php');
include('modules/verif_cookie_set.php');
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet" type="text/css" href="css/conteneur.css">
    <style>
        body {
            background-image: url('css/img/background.jpg');
        }

    </style>
</head>

<body>
<?php
include('header.php');
?>
    <div class="content-container" style="margin-right: 30%;"> <!-- Modification de la marge -->
        <h2>NEWS</h2>
       <iframe src='news/'></iframe>
    </div><p>

    <div class="content-container" style="margin: margin-right: 30%;"> <!-- Modification de la marge -->
        <h2>défi du mois</h2>
        <p>le défi du mois va être de décrypter le fichier .crypt</p>
        <br>
        <a href="defis/2023/09.html">le défi du mois</a>
    </div><p>

    <div class="content-container" style="margin-right: 30%;"> <!-- Modification de la marge -->
        <h2>projet du mois</h2>
        <p>pour l'instant le projet du mois va être discuté dans la prochaine réunion</p>
        <br>
        <a href="projets/2023/09.html">le projet du mois</a>
    </div><p>
</body>
</html>
