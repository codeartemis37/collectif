<?php
session_start();

# $bdd = new PDO('mysql:host=localhost;dbname=id19750853_message;charset=utf8;', 'id19750853_gabriel', 'Gabriel.exe06!');
include('modules/bdd.php');

if(isset($_POST['valider'])) {
    if(!empty($_POST['pseudo'])) {
        $recupUser = $bdd->prepare('SELECT * FROM users WHERE pseudo = ?');
        $recupUser->execute(array($_POST['pseudo']));

        if($recupUser->rowCount() > 0) {
            $_SESSION['pseudo'] = $_POST['pseudo'];
            $_SESSION['id'] = $recupUser->fetch()['id'];
            header('Location: session.php');
            exit;
        }
        else {
            echo "Aucun utilisateur trouvé.";
        }
    }
    else {
        echo "Veuillez entrer votre pseudo.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace de connexion</title>
    <style>
        body {
            background-color: #0e0e0e;
            color: #fff;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        form {
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        input[type='submit'], input[type='text'] {
            border: none;
            border-radius: 5px;
            padding: 10px;
            margin: 5px;
            font-size: 16px;
            background-color: #333;
            color: #fff;
            outline: none;
        }
        input[type='submit'] {
            cursor: pointer;
            background-color: #0066ff;
            transition: background-color 0.3s ease;
        }
        input[type='submit']:hover {
            background-color: #0047b3;
        }
    </style>
</head>
<body>
    <form action="" method="post">
        <h1>veillez entrer votre nom</h1>
        <input type="text" name="pseudo" placeholder="Enter your username">
        <input type="submit" name="valider" value="Submit">
    </form>
</body>
</html>
