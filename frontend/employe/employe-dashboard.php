<?php
session_start();
require_once '../../backend/config.php';

// 1. SÉCURITÉ STRICTE
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['employe', 'admin'])) {
    header('Location: ../connexion.php');
    exit();
}

// LE CODE COULEUR DES STATUTS (Classes Bootstrap)
$couleurs_statut = [
    'reçue' => 'bg-secondary text-white',
    'accepté' => 'bg-info text-dark',
    'en préparation' => 'bg-warning text-dark',
    'en cours de livraison' => 'bg-primary text-white',
    'livré' => 'bg-success text-white',
    'en attente du retour de matériel' => 'bg-danger text-white fw-bold animate-pulse',
    'terminée' => 'bg-dark text-white',
    'annulée' => 'bg-light text-danger border border-danger'
];

// 2. TRAITEMENT DE LA SUPPRESSION
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id_commande'])) {
    $id_a_supprimer = (int)$_GET['id_commande'];
    
    try {
        $pdo->beginTransaction();
        
        $deleteDetails = $pdo->prepare("DELETE FROM details_commandes WHERE id_commande = ?");
        $deleteDetails->execute([$id_a_supprimer]);
        
        $deleteCmd = $pdo->prepare("DELETE FROM commandes WHERE id = ?");
        $deleteCmd->execute([$id_a_supprimer]);
        
        $pdo->commit();
        header('Location: employe-dashboard.php?msg=deleted');
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
}

// 3. RÉCUPÉRATION DE TOUTES LES COMMANDES
try {
    $stmt = $pdo->query("SELECT c.*, u.nom, u.prenom 
                         FROM commandes c 
                         JOIN utilisateurs u ON c.id_utilisateur = u.id 
                         ORDER BY c.date_commande DESC");
    $commandes = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des commandes : " . $e->getMessage());
}

include '../includes/header.php';
?>

<main class="container py-5">
    <!-- En-tête du Dashboard avec le titre, le badge et les boutons regroupés à gauche -->
    <div class="mb-5">
        <h1 class="fw-bold mb-2">Tableau de bord - Employé</h1>
        
        <!-- Barre d'alignement horizontal pour le badge et les boutons -->
        <div class="d-flex align-items-center flex-wrap gap-2">

        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="../admin/admin-dashboard.php" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-2 fw-bold shadow-sm me-2">
                    ⬅️ Retour Espace Admin
                </a>
            <?php endif; ?>
            
            <a href="employe-carte.php" class="btn btn-sm btn-dark border-0 rounded-pill px-3 py-2 fw-bold shadow-sm">
                Gestion de la Carte
            </a>

            <a href="employe-avis.php" class="btn btn-sm btn-warning border-0 rounded-pill px-3 py-2 fw-bold shadow-sm text-dark">
                Gérer les Avis Clients
            </a>
        </div>
    </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <div class="alert alert-success alert-dismissible fade show fw-bold" role="alert">
            🗑️ La commande a été supprimée avec succès.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="p-3">N° Commande</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Statut</th>
                            <th class="text-center p-3" style="width: 220px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($commandes)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Aucune commande pour le moment.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($commandes as $c): ?>
                                <?php 
                                    // On récupère la couleur associée au statut actuel, ou gris par défaut si inconnu
                                    $classe_couleur = $couleurs_statut[$c['statut']] ?? 'bg-secondary text-white';
                                ?>
                                <tr>
                                    <td class="p-3 fw-bold">#<?= $c['id'] ?></td>
                                    <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($c['date_commande'])) ?></td>
                                    <td class="fw-bold"><?= number_format($c['total'], 2, ',', ' ') ?> €</td>
                                    <td>
                                        <span class="badge <?= $classe_couleur ?> text-uppercase px-3 py-2 shadow-sm" style="font-size: 0.85rem;">
                                            <?= htmlspecialchars($c['statut']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center p-3">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="employe-details-commande.php?id=<?= $c['id'] ?>" 
                                               class="btn btn-sm btn-outline-dark rounded-pill px-3 fw-bold">
                                                Modifier
                                            </a>
                                            <a href="employe-dashboard.php?action=supprimer&id_commande=<?= $c['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer définitivement la commande #<?= $c['id'] ?> ?');">
                                                Supprimer
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>