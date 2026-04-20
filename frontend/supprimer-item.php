<?php
session_start();

// 1. On vérifie qu'un ID a bien été envoyé dans l'URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];

    // 2. On vérifie si cet ID existe bien dans le panier en session
    if (isset($_SESSION['panier'][$id_a_supprimer])) {
        
        // 3. On supprime l'entrée correspondante
        unset($_SESSION['panier'][$id_a_supprimer]);
    }
}

// 4. On redirige immédiatement vers la page panier pour voir le résultat
header("Location: panier.php");
exit();