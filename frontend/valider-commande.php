<?php
session_start();
require_once '../backend/config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['panier'])) {
    header("Location: connexion.php");
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Calcul du total de base (HT)
    $total_articles = 0;
    foreach ($_SESSION['panier'] as $id_menu => $quantite) {
        $stmt = $pdo->prepare("SELECT prix_pers FROM menu WHERE id = ?");
        $stmt->execute([$id_menu]);
        $menu = $stmt->fetch();
        $total_articles += $menu['prix_pers'] * $quantite;
    }

    // --- NOUVEAU : CALCUL DE LA LOGIQUE MÉTIER ---
    
    // A. Calcul de la réduction (10% si + de 5 articles)
    $quantite_totale = array_sum($_SESSION['panier']);
    $reduction = ($quantite_totale > 5) ? ($total_articles * 0.10) : 0;

    // B. Récupération de la ville pour les frais kilométriques
    $stmt = $pdo->prepare("SELECT ville FROM utilisateurs WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $ville = strtolower(trim($user['ville'] ?? ''));

    if ($ville == "maisse") {
        $frais = 2.00;
    } elseif (in_array($ville, ["evry", "corbeil", "essonnes", "boutigny"])) {
        $frais = 5.00;
    } else {
        $frais = 8.50;
    }

    // C. TOTAL FINAL RÉEL
    $total_final = ($total_articles - $reduction) + $frais;

    // 2. Insertion dans 'commandes' (On enregistre le $total_final !)
    // J'ai ajouté le champ 'statut' pour que ce soit plus complet
    $stmt = $pdo->prepare("INSERT INTO commandes (id_utilisateur, total, statut, date_commande) VALUES (?, ?, 'En préparation', NOW())");
    $stmt->execute([$_SESSION['user_id'], $total_final]);
    $id_commande = $pdo->lastInsertId();

    // 3. Insertion dans 'details_commandes'
    foreach ($_SESSION['panier'] as $id_menu => $quantite) {
        $stmt = $pdo->prepare("SELECT prix_pers FROM menu WHERE id = ?");
        $stmt->execute([$id_menu]);
        $menu = $stmt->fetch();

        $stmt = $pdo->prepare("INSERT INTO details_commandes (id_commande, id_menu, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_commande, $id_menu, $quantite, $menu['prix_pers']]);
    }

    $pdo->commit();

    unset($_SESSION['panier']);
    header("Location: confirmation.php?id=" . $id_commande);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur lors de la commande : " . $e->getMessage());
}