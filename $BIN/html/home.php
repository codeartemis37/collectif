<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet" type="text/css" href="css/conteneur.css">
    <link rel="stylesheet" type="text/css" href="css/menu.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f2f2; /* Couleur de fond */
            color: #333333; /* Couleur du texte */
            margin: 0;
            padding: 0;
        }
        .content-container {
            position: relative;
            padding: 30px;
            margin: 20px auto;
            max-width: 800px;
            background-color: #ffffff; /* Couleur du fond des conteneurs */
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); /* Ombre légère */
            text-align: center;
        }
        .content-container h2 {
            color: #fff; /* Couleur du titre */
        }
        .content-container p {
            color: #666666; /* Couleur du texte */
        }
        .content-container a {
            color: #e67e22; /* Couleur du lien */
            text-decoration: none;
        }
        .content-container a:hover {
            text-decoration: underline;
        }
        iframe {
            width: 100%;
            height: 500px;
            border: none;
        }
        /* Styles CSS pour la mise en page */
        .carousel-container {
            max-width: 600px;
            text-align: center;
            overflow: hidden;
            margin-left: auto;
            margin-right: auto;
            margin-top: 20px;
        }
        .carousel {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .carousel img {
            max-width: 100%;
            height: auto;
            flex: 0 0 100%; /* Largeur fixe pour chaque image */
        }
        .carousel-button {
            background-color: #333;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- Menu déroulant -->
    <div id="menu" style="background-color: rgb(18, 34, 65);">
        <ul>
            <li><a href="homeid.php?id=<?= $connecte; ?>">home</a>&emsp;&emsp;<span>|</span>&emsp;&emsp;
                <a href="html/editeur.html">éditeur de code</a>&emsp;&emsp;<span>|</span>&emsp;&emsp;
                <a href="upload.php?id=<?= $connecte; ?>">envoie de fichier</a>&emsp;&emsp;<span>|</span>&emsp;&emsp;
                <a href="chatid.php?id=<?= $connecte; ?>">chat</a></li>
        </ul>
        <center>
            <table style="background-color: #333; border: 1;">
                <tr>
                    <th>
                        <h1>news</h1>
                        <!--<iframe src="html/carousel.html" width="450" height="325"></iframe>-->
                        <iframe src="loadMessage.php" width="250" height="325"></iframe>
                    </th>
                </tr>
            </table>
        </center>
    </div>

    <!-- Bouton pour afficher/masquer le menu -->
    <div style="display: flex; justify-content: space-between; padding: 10px;">
        <button id="toggle-button" onclick="toggleMenu()">Afficher le menu</button>
        <button id="toggle-button" onclick="EasterEgg()">role: <?php $param = $connecte; include 'modules/roles.php'; ?></button>
    </div>

    <div class="content-container">
        <h2>projet du mois</h2>
        <p>pour l'instant le projet du mois va être discuté dans la prochaine réunion</p>
        <br>
        <a href="projets/2023/09.html">le projet du mois</a>
    </div>

    <div class="content-container">
        <h2>défi du mois</h2>
        <p>le défi du mois va être de décrypter le fichier .crypt</p>
        <br>
        <a href="defis/2023/09.html">le défi du mois</a>
    </div>

    
    </div>

    <div class="carousel-container">
        <div class="carousel">
            <img src="news/réunion.png" alt="Image 1">
            <img src="news/alerte.png" alt="Image 2">
            <img src="news/rip.jpg" alt="Image 3">
        </div>
        <button class="carousel-button" id="prevBtn">Précédent</button>
        <button class="carousel-button" id="nextBtn">Suivant</button>

    <script>
        // JavaScript pour faire défiler les images du carrousel
        const carousel = document.querySelector('.carousel');
        const images = document.querySelectorAll('.carousel img');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        let currentIndex = 0;
        let isAnimating = false;

        // Fonction pour afficher l'image suivante
        function nextImage() {
            if (!isAnimating) {
                isAnimating = true;
                currentIndex = (currentIndex + 1) % images.length;
                updateCarousel();
                setTimeout(() => {
                    isAnimating = false;
                }, 500); // Durée de l'animation en millisecondes
            }
        }

        // Fonction pour afficher l'image précédente
        function prevImage() {
            if (!isAnimating) {
                isAnimating = true;
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                updateCarousel();
                setTimeout(() => {
                    isAnimating = false;
                }, 500); // Durée de l'animation en millisecondes
            }
        }

        // Fonction pour défilement automatique toutes les 5 secondes
        function autoScroll() {
            nextImage();
            setTimeout(autoScroll, 5000); // Défilement automatique toutes les 5 secondes
        }

        // Démarrez le défilement automatique au chargement de la page
        setTimeout(autoScroll, 5000);

        // Met à jour l'affichage du carrousel
        function updateCarousel() {
            const offset = -currentIndex * 100; // Largeur de chaque image
            carousel.style.transform = `translateX(${offset}%)`;
        }

        // Gestionnaires d'événements pour les boutons
        nextBtn.addEventListener('click', nextImage);
        prevBtn.addEventListener('click', prevImage);
    </script>

    <script>
        function toggleMenu() {
            var menu = document.getElementById("menu");
            if (menu.style.display === "none") {
                menu.style.display = "block";
                document.getElementById("toggle-button").textContent = "Masquer le menu";
            } else {
                menu.style.display = "none";
                document.getElementById("toggle-button").textContent = "Afficher le menu";
            }
        }

        function EasterEgg() {
            alert('vous avez trouvé l\'easter egg!!!')
        }
    </script>
</body>

</html>
