<?php
session_start();
require_once '../backend/config.php';

// Sécurité : Si pas connecté ou panier vide 
if (!isset($_SESSION['user_id']) || empty($_SESSION['panier'])) {
    header("Location: connexion.php");
    exit();
}

try {
    $pdo->beginTransaction(); // On démarre une transaction pour être sûr que tout s'enregistre ou rien du tout

    // 1. Calcul du total (on récupère les prix en BDD)
    $total_commande = 0;
    foreach ($_SESSION['panier'] as $id_menu => $quantite) {
        $stmt = $pdo->prepare("SELECT prix_pers FROM menu WHERE id = ?");
        $stmt->execute([$id_menu]);
        $menu = $stmt->fetch();
        $total_commande += $menu['prix_pers'] * $quantite;
    }

    // 2. Insertion dans la table 'commandes'
    $stmt = $pdo->prepare("INSERT INTO commandes (id_utilisateur, total) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $total_commande]);
    $id_commande = $pdo->lastInsertId(); // On récupère l'ID de la commande tout juste créée

    // 3. Insertion de chaque article dans 'details_commandes'
    foreach ($_SESSION['panier'] as $id_menu => $quantite) {
        $stmt = $pdo->prepare("SELECT prix_pers FROM menu WHERE id = ?");
        $stmt->execute([$id_menu]);
        $menu = $stmt->fetch();

        $stmt = $pdo->prepare("INSERT INTO details_commandes (id_commande, id_menu, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_commande, $id_menu, $quantite, $menu['prix_pers']]);
    }

    $pdo->commit(); // On valide tout en BDD

    // 4. On vide le panier !
    unset($_SESSION['panier']);

    // 5. Redirection vers une page de succès
    header("Location: confirmation.php?id=" . $id_commande);
    exit();

} catch (Exception $e) {
    $pdo->rollBack(); // En cas d'erreur, on annule tout ce qui a été fait
    die("Erreur lors de la commande : " . $e->getMessage());
}