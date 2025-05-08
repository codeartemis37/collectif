<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            font-family: Arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<?php
// INSERT INTO `votes` (`vote1`, `vote2`, `vote3`, `vote4`) VALUES ('0', '0', '0', '0');
// INSERT INTO `voteurs` (`vote1`, `vote2`, `vote3`, `vote4`) VALUES ('', '', '', '');
$choix = ['0',
'1. je suis intelligent',
'2. je suis geek',
'3. je suis con',
'4. je suis un protogene'
];

// Votre code PHP ici

// Connexion à la base de données
include('modules/bdd.php');

// Requête d'incrémentation de la valeur de vote1
$requete = "UPDATE votes SET vote" . $_GET['vote'] . " = vote" . $_GET['vote'] . " + 1";
$bdd->exec($requete);
$requete = "UPDATE voteurs SET vote" . $_GET['vote'] . " = CONCAT(vote". $_GET['vote']. ", ' - ". $_GET['voteur']. "');";
#echo $requete;
$bdd->exec($requete);

// Affichage des résultats
echo "<br>Vous, ". $_GET['voteur'] ." avez voté " . $_GET['vote'] . "<br>";

// Création du tableau HTML
echo "<table>";
echo "<tr><th>qui a voté: </th><th>choix</th><th>Valeur</th></tr>";
for ($i = 1; $i <= 4; $i++) {
    $vote = $bdd->query("SELECT vote$i FROM `votes` WHERE 1")->fetchColumn();
    $voteur = $bdd->query("SELECT vote$i FROM `voteurs` WHERE 1")->fetchColumn();
    echo "<tr><td>$voteur</td><td>$choix[$i]</td><td>$vote</td></tr>";
}
echo "</table>";
?>
</body>
</html>