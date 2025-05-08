<?php
$bdd = new PDO('mysql:host=localhost;dbname=id19750853_messageries;charset=utf8;', 'root', 'root');

// Configuration de PDO pour récupérer les erreurs
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>