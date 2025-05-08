    <link rel="stylesheet" type="text/css" href="https://colectif.000webhostapp.com/css/news.css">
    <div class="carousel-container">
        <div class="carousel">
            <img src="https://colectif.000webhostapp.com/alerte.png" alt="Image 1">
            <img src="https://colectif.000webhostapp.com/rip.jpg" alt="Image 2">
            <img src="https://colectif.000webhostapp.com/ab.png" alt="Image 3">
        </div>
        <button class="carousel-button" id="prevBtn">Précédent</button>
        <button class="carousel-button" id="nextBtn">Suivant</button>
    </div>

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
                setTimeout(() => {isAnimating = false;}, 500); // Durée de l'animation en millisecondes
            }
        }

        // Fonction pour afficher l'image précédente
        function prevImage() {
            if (!isAnimating) {
                isAnimating = true;
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                updateCarousel();
                setTimeout(() => {isAnimating = false;}, 500); // Durée de l'animation en millisecondes
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

        function EasterEgg() {alert('vous avez trouvé l\'easter egg!!!');}

        // JavaScript pour ajuster la position du carrousel si nécessaire
        window.addEventListener('load', function() {
            var carouselContainer = document.querySelector('.carousel-container');
            var carouselRect = carouselContainer.getBoundingClientRect();
            var defiMois = document.querySelector('.content-container');

            var defiMoisRect = defiMois.getBoundingClientRect();

            if (carouselRect.bottom > defiMoisRect.top) {
                var marginBottom = carouselRect.bottom - defiMoisRect.top + 300; // Ajouter une marge de 20px
                carouselContainer.style.bottom = marginBottom + 'px';
            }
        });
    </script>
