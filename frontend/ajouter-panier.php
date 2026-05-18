<?php
session_start();

// On récupère l'ID envoyé par le bouton
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    // Initialise le panier s'il n'existe pas
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Ajoute le produit ou augmente la quantité
    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]++;
    } else {
        $_SESSION['panier'][$id] = 1;
    }

    // Redirige l'utilisateur d'où il venait (ou vers les menus)
    header("Location: nos-menus.php?statut=ajoute");
    exit();
}