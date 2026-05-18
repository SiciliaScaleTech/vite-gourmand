function filterMenus() {
    // Récupération des valeurs des filtres
    const themeSelected = document.querySelector('select[name="theme"]').value;
    const prixMaxInput = document.querySelector('input[name="prix_max"]').value;
    const minRange = document.querySelector('input[name="min"]').value;
    const maxRange = document.querySelector('input[name="max"]').value;
    const persMinInput = document.querySelector('input[name="pers_min"]').value;

    const items = document.querySelectorAll('.menu-item');
    const noResult = document.getElementById('no-result-message');
    let hasResults = false; // Flag pour savoir si on a trouvé quelque chose

    items.forEach(item => {
        let isVisible = true;

        // Récupération des données du menu
        const itemTheme = item.getAttribute('data-theme');
        const itemPrix = parseFloat(item.getAttribute('data-prix'));
        const itemPersMin = parseInt(item.getAttribute('data-pers-min'));

        // --- FILTRES ---
        if (themeSelected !== "" && itemTheme !== themeSelected) isVisible = false;
        if (prixMaxInput !== "" && itemPrix > parseFloat(prixMaxInput)) isVisible = false;
        if (minRange !== "" && itemPrix < parseFloat(minRange)) isVisible = false;
        if (maxRange !== "" && itemPrix > parseFloat(maxRange)) isVisible = false;
        
        // Logique Personnes : On cache si le menu exige plus de monde que ce que l'utilisateur a
        if (persMinInput !== "" && itemPersMin < parseInt(persMinInput)) {
    isVisible = false;
}

        // --- AFFICHAGE ---
        if (isVisible) {
            item.style.display = "block";
            hasResults = true; // On a trouvé au moins un menu !
        } else {
            item.style.display = "none";
        }
    });

    // Gestion du message d'erreur
    noResult.style.display = hasResults ? "none" : "block";
}
