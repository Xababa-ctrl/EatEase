// Je regroupe tout le JavaScript de la page d'accueil dans un seul bloc
// Cela évite les conflits et permet une meilleure organisation de mon code.

document.addEventListener('DOMContentLoaded', function () {

    /**
     * Fonction pour initialiser le carrousel des restaurants à la une.
     * 
     * Cette fonction va récupérer les données des restaurants en AJAX
     * depuis la route /carousel-data et construire dynamiquement le carrousel Bootstrap.
     * 
     * Avantage : Je peux changer les restaurants affichés dans le carrousel
     * sans toucher au HTML, juste en modifiant les données côté back-end.
     */
    function initCarousel() {
        const carousel = document.getElementById('restaurantCarousel');

        // Si aucun carrousel n'existe sur la page, je ne fais rien.
        if (!carousel) return;

        const carouselInner = carousel.querySelector('.carousel-inner');
        const carouselIndicators = carousel.querySelector('.carousel-indicators');
        const carouselDataUrl = carousel.dataset.url; // L'URL est définie directement en HTML (data-url)

        // Vérification : L'URL des données du carrousel est-elle définie ?
        if (!carouselDataUrl) {
            console.error('L\'URL des données du carrousel n\'est pas définie. Ajoutez l\'attribut data-url dans le HTML.');
            return;
        }

        // Je lance une requête AJAX (fetch) vers l'URL pour récupérer les données du carrousel
        fetch(carouselDataUrl)
            .then(response => response.ok ? response.json() : Promise.reject('Erreur réseau pour le carrousel'))
            .then(data => {
                // Si aucune donnée n'est renvoyée, j'affiche un message par défaut.
                if (data.length === 0) {
                    carouselInner.innerHTML = `
                        <div class="carousel-item active">
                            <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                                <p class="text-muted">Aucun restaurant à la une.</p>
                            </div>
                        </div>`;
                    return;
                }

                // Je vide le contenu actuel du carrousel avant d'insérer les nouveaux éléments.
                carouselInner.innerHTML = '';
                carouselIndicators.innerHTML = '';

                // Pour chaque restaurant reçu, je construis dynamiquement un slide du carrousel.
                data.forEach((item, index) => {
                    const isActive = index === 0 ? 'active' : '';

                    // Création des indicateurs (les petits points sous le carrousel)
                    const indicator = `
                        <button type="button" data-bs-target="#restaurantCarousel" data-bs-slide-to="${index}" class="${isActive}" aria-current="${isActive ? 'true' : 'false'}" aria-label="Slide ${index + 1}"></button>`;
                    carouselIndicators.insertAdjacentHTML('beforeend', indicator);

                    // Création de l'élément du carrousel
                    const carouselItem = `
                        <div class="carousel-item ${isActive}">
                            <img src="${item.image_url}" class="d-block w-100" alt="${item.name}" style="height: 400px; object-fit: cover;">
                            <div class="carousel-caption  bg-dark bg-opacity-50 p-3 rounded">
                                <h5>${item.name}</h5>
                                <p class="fst-italic">"${item.review_comment}" - ${item.reviewer_name}</p>
                                <a href="${item.show_url}" class="btn btn-primary btn-sm">Voir le restaurant</a>
                            </div>
                        </div>`;
                    carouselInner.insertAdjacentHTML('beforeend', carouselItem);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement du carrousel:', error);
                carouselInner.innerHTML = `
                    <div class="carousel-item active">
                        <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
                            <p class="text-danger">Erreur de chargement des données du carrousel.</p>
                        </div>
                    </div>`;
            });
    }

    /**
     * Fonction pour initialiser la barre de recherche AJAX des restaurants.
     * 
     * Cette fonctionnalité permet à l'utilisateur de rechercher un restaurant en direct,
     * avec autocomplétion, sans recharger la page.
     * 
     * Le système envoie la requête dès que l'utilisateur commence à taper
     * (avec un délai de 300ms pour éviter de surcharger le serveur).
     */
    function initSearch() {
        const searchInput = document.getElementById('restaurant-search');
        if (!searchInput) return; // S'il n'y a pas de champ de recherche, je ne fais rien.

        const resultsContainer = document.getElementById('search-results');
        const searchUrl = searchInput.dataset.searchUrl; // Je récupère l'URL depuis l'attribut data-search-url

        if (!searchUrl) {
            console.error('L\'URL de recherche n\'est pas définie. Ajoutez un attribut data-search-url au champ input.');
            return;
        }

        let debounceTimer; // Timer pour éviter d'envoyer trop de requêtes trop vite

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();
            clearTimeout(debounceTimer);

            // Si la recherche contient moins de 2 caractères, je vide les résultats
            if (query.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }

            // J'attends 300ms avant de lancer la requête AJAX
            debounceTimer = setTimeout(() => {
                resultsContainer.innerHTML = '<div class="list-group-item text-muted">Recherche en cours...</div>';

                fetch(`${searchUrl}?query=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.ok ? response.json() : Promise.reject('Erreur réseau pour la recherche'))
                .then(restaurants => {
                    resultsContainer.innerHTML = '';

                    const restaurantData = restaurants.data;

                    if (restaurantData && restaurantData.length > 0) {
                        // Je parcours les résultats et je crée un lien pour chaque restaurant trouvé.
                        restaurantData.forEach(restaurant => {
                            const link = document.createElement('a');
                            link.href = `/restaurants/${restaurant.id}`; // Je construis l'URL pour accéder à la fiche du restaurant
                            link.classList.add('list-group-item', 'list-group-item-action');
                            link.textContent = restaurant.name;
                            resultsContainer.appendChild(link);
                        });
                    } else {
                        resultsContainer.innerHTML = '<div class="list-group-item text-muted">Aucun restaurant trouvé.</div>';
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche AJAX:', error);
                    resultsContainer.innerHTML = '<div class="list-group-item text-danger">Erreur lors de la recherche.</div>';
                });
            }, 300);
        });

        // Si l'utilisateur clique en dehors du champ de recherche ou des résultats, je masque les résultats.
        document.addEventListener('click', function(event) {
            if (!searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
                resultsContainer.innerHTML = '';
            }
        });
    }

    // Je lance mes deux fonctionnalités AJAX dès que la page est chargée.
    initCarousel();
    initSearch();
});
