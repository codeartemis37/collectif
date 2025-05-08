<?php
// Sécurisation des variables GET
$connecte = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
include('../menuframe.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Titre</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        #bas {
            background-color: #0e0e0e;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        #divliste {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        #bouton {
            display: inline-block;
            border: none;
            border-radius: 5px;
            padding: 10px;
            margin: 5px;
            font-size: 16px;
            background-color: #333;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        #bouton:hover {
            background-color: #0066ff;
        }
    </style>
</head>
<body>
    <div id="bas">
        <div id="divliste">
            <a href="../connexion.php" id="bouton">message privé</a>
            <a href="../html/editeur.html" id="bouton">éditeur de code</a>
        </div>
    </div>
</body>
</html>
