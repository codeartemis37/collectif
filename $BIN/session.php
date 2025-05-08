<?php 
session_start();
include('modules/bdd.php');
if(!$_SESSION['pseudo']){
    header('location: connexion.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tous les utilisateurs</title>
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
        .user {
            background-color: #444;
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            text-align: left;
            max-width: 300px;
        }
        a {
            text-decoration: none;
            color: #fff;
        }
        a:hover {
            color: #0066ff;
        }
    </style>
</head>
<body>
    <?php 
        $recupUser = $bdd->query('SELECT * FROM users');
        while($user = $recupUser->fetch()){
    ?>
    <div class="user">
        <a href="message.php?id=<?php echo $user['id']; ?>">
            <p><?php echo $user['pseudo']; ?></p>
        </a>
    </div>
    <?php
        }
    ?>
</body>
</html>
