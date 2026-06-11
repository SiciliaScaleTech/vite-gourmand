// Cette partie permet de lancer le filtre automatiquement 

document.addEventListener("DOMContentLoaded", function() {
    console.log("Page chargée, lancement du filtre...");
    filterMenus();
});

function filterMenus() {
    // 1. Récupération sécurisée des éléments 
    const themeSelect = document.querySelector('select[name="theme"]');
    const prixInput = document.querySelector('input[name="prix_max"]');
    const persInput = document.querySelector('input[name="pers_min"]');
    const allergeneSelect = document.querySelector('select[name="allergene"]');

    // Si le formulaire n'est pas trouvé, on arrête tout pour ne pas créer d'erreur
    if (!themeSelect) return;

    const theme = themeSelect.value;
    const prixMax = prixInput.value ? parseFloat(prixInput.value) : null;
    const persMinReq = persInput.value ? parseInt(persInput.value) : null;
    const allergenePasVoulu = allergeneSelect ? allergeneSelect.value.toLowerCase() : "";

    const items = document.querySelectorAll('#menu-container .menu-item');
    
    let hasResults = false;

    items.forEach(item => {
        // 2. Récupération des données data-attributes du HTML
        const itemTheme = item.getAttribute('data-theme');
        const itemPrix = parseFloat(item.getAttribute('data-prix'));
        const itemPersMin = parseInt(item.getAttribute('data-pers-min'));
        const itemAllergenes = item.getAttribute('data-allergenes') ? item.getAttribute('data-allergenes').toLowerCase() : "";

        let isVisible = true;

        // 3. Application des filtres
        if (theme !== "" && itemTheme !== theme) isVisible = false;
        if (prixMax !== null && itemPrix > prixMax) isVisible = false;
        if (persMinReq !== null && itemPersMin < persMinReq) isVisible = false;
        
        // Logique allergène : si le menu CONTIENT l'allergène qu'on veut éviter, on cache
        if (allergenePasVoulu !== "" && itemAllergenes.includes(allergenePasVoulu)) {
            isVisible = false;
        }

        // 4. Affichage ou masquage
        item.style.display = isVisible ? "block" : "none";
        if (isVisible) hasResults = true;
    });

    // 5. Message "Aucun résultat"
    const noResult = document.getElementById('no-result-message');
    if (noResult) {
        noResult.style.display = hasResults ? "none" : "block";
    }
}
