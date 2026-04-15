function changeImg(menuId, newSrc) {
    // On cible la grande image de la modale spécifique via son ID
    const mainImg = document.getElementById('mainImg' + menuId);
    if (mainImg) {
        mainImg.src = newSrc;
    }
}