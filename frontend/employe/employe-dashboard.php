<?php
session_start();
require_once '../../backend/config.php';

// 1. SÉCURITÉ : Vérification du rôle Employé ou Admin
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['employe', 'admin'])) {
    header('Location: ../index.php');
    exit();
}

// Vérification de la présence de l'ID de la commande
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: employe-dashboard.php');
    exit();
}

$commande_id = (int)$_GET['id'];
$message = "";
$messageClass = "";

// 2. TRAITEMENT DU CHANGEMENT DE STATUT (Formulaire POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouveau_statut'])) {
    $nouveau_statut = trim($_POST['nouveau_statut']);
    
    try {
        // On récupère le statut actuel de la commande avant modification pour éviter les doublons de baisse de stock
        $checkOld = $pdo->prepare("SELECT statut FROM commandes WHERE id = ?");
        $checkOld->execute([$commande_id]);
        $old_status = $checkOld->fetchColumn();

        // TRANSACTION SQL : On sécurise pour que le statut ET le stock changent ensemble sans bug
        $pdo->beginTransaction();

        // Étape A : Mise à jour du statut de la commande
        $updateStmt = $pdo->prepare("UPDATE commandes SET statut = ? WHERE id = ?");
        $updateStmt->execute([$nouveau_statut, $commande_id]);

        // Étape B :  SI LA COMMANDE PASSE À 'ACCEPTÉ' (Et qu'elle ne l'était pas déjà) -> ON BAISSE LES STOCKS
        if ($nouveau_statut === 'accepté' && $old_status !== 'accepté') {
            
            //  On récupère les plats/menus liés à cette commande
            // (Note : Ajuste le nom de la table 'commande_details' si elle s'appelle autrement en BDD)
            $itemsStmt = $pdo->prepare("SELECT id_menu, quantite FROM commande_details WHERE id_commande = ?");
            $itemsStmt->execute([$commande_id]);
            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

            // Requête pour retirer la quantité vendue du stock du menu
            $updateStockStmt = $pdo->prepare("UPDATE menu SET stock = stock - ? WHERE id = ?");

            foreach ($items as $item) {
                $updateStockStmt->execute([$item['quantite'], $item['id_menu']]);
            }
        }

        // Si tout s'est bien passé, on valide la transaction en base de données
        $pdo->commit();
        $message = "Le statut de la commande a été mis à jour avec succès !";
        $messageClass = "alert-success";

    } catch (PDOException $e) {
        // En cas d'erreur de BDD, on annule tout pour ne pas fausser les stocks
        $pdo->rollBack();
        $message = "Erreur lors de la mise à jour : " . $e->getMessage();
        $messageClass = "alert-danger";
    }
}

// 3. RÉCUPÉRATION DES INFOS DE LA COMMANDE ET DU CLIENT POUR L'AFFICHAGE
try {
    $cmdStmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, u.email, u.telephone 
                              FROM commandes c 
                              JOIN utilisateurs u ON c.id_utilisateur = u.id 
                              WHERE c.id = ?");
    $cmdStmt->execute([$commande_id]);
    $commande = $cmdStmt->fetch(PDO::FETCH_ASSOC);

    if (!$commande) {
        die("Commande introuvable.");
    }

    // Récupération des lignes de la commande (les plats achetés)
    $detailsStmt = $pdo->prepare("SELECT cd.*, m.titre, m.prix_pers 
                                  FROM commande_details cd 
                                  JOIN menu m ON cd.id_menu = m.id 
                                  WHERE cd.id_commande = ?");
    $detailsStmt->execute([$commande_id]);
    $liste_plats = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}

include '../includes/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <a href="employe-dashboard.php" class="btn btn-outline-secondary rounded-pill mb-4">⬅️ Retour au Dashboard</a>

            <?php if (!empty($message)): ?>
                <div class="alert <?= $messageClass ?> alert-dismissible fade show" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow border-0 rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="mb-0 fw-bold">Gestion Commande #<?= $commande['id'] ?></h3>
                        <small class="text-light-50">Passée le <?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?></small>
                    </div>
                    <span class="badge bg-primary text-uppercase px-3 py-2"><?= htmlspecialchars($commande['statut']) ?></span>
                </div>

                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <h5 class="fw-bold text-secondary mb-3">👤 Informations Client</h5>
                            <p class="mb-1"><strong>Nom complet :</strong> <?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?></p>
                            <p class="mb-1"><strong>Téléphone :</strong> <?= htmlspecialchars($commande['telephone'] ?? 'Non renseigné') ?></p>
                            <p class="mb-0"><strong>Email :</strong> <?= htmlspecialchars($commande['email']) ?></p>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h5 class="fw-bold text-secondary mb-3">Modifier le Statut</h5>
                            <form method="POST" action="employe-details-commande.php?id=<?= $commande_id ?>" class="row g-2 align-items-center">
                                <div class="col-8">
                                    <select name="nouveau_statut" class="form-select">
                                        <option value="reçue" <?= $commande['statut'] === 'reçue' ? 'selected' : '' ?>>Reçue</option>
                                        <option value="accepté" <?= $commande['statut'] === 'accepté' ? 'selected' : '' ?>>Accepté</option>
                                        <option value="en préparation" <?= $commande['statut'] === 'en préparation' ? 'selected' : '' ?>>En préparation</option>
                                        <option value="en cours de livraison" <?= $commande['statut'] === 'en cours de livraison' ? 'selected' : '' ?>>En cours de livraison</option>
                                        <option value="livré" <?= $commande['statut'] === 'livré' ? 'selected' : '' ?>>Livré</option>
                                        <option value="en attente du retour de matériel" <?= $commande['statut'] === 'en attente du retour de matériel' ? 'selected' : '' ?>>En attente du retour de matériel</option>
                                        <option value="terminée" <?= $commande['statut'] === 'terminée' ? 'selected' : '' ?>>Terminée</option>
                                        <option value="annulée" <?= $commande['statut'] === 'annulée' ? 'selected' : '' ?>>Annulée</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold">Mettre à jour</button>
                                </div>
                            </form>
                            <div class="form-text text-muted mt-2">Passer le statut à "Accepté" réduira automatiquement les stocks des menus associés.</div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="fw-bold text-secondary mb-3">Contenu de la commande</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Menu</th>
                                    <th class="text-center">Prix Unitaire</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead class="table-light">
                            <tbody>
                                <?php foreach ($liste_plats as $plat): ?>
                                    <tr>
                                        <td class="fw-bold"><?= htmlspecialchars($plat['titre']) ?></td>
                                        <td class="text-center"><?= number_format($plat['prix_pers'], 2, ',', ' ') ?> €</td>
                                        <td class="text-center">x<?= htmlspecialchars($plat['quantite']) ?></td>
                                        <td class="text-end fw-bold"><?= number_format($plat['prix_pers'] * $plat['quantite'], 2, ',', ' ') ?> €</td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-dark">
                                    <td colspan="3" class="text-end fw-bold">Montant total de la commande :</td>
                                    <td class="text-end fw-bold"><?= number_format($commande['total'], 2, ',', ' ') ?> €</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>