<?php
session_start();
require_once '../backend/config.php';

// 🛡️ SÉCURITÉ : Vérification du rôle Employé ou Admin
// Si l'utilisateur n'est pas connecté OU n'a pas le bon rôle, on le jette à l'accueil
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['employe', 'admin'])) {
    header('Location: index.php');
    exit();
}

// --- LOGIQUE DES FILTRES DE RECHERCHE ---
$status_filter = $_GET['statut'] ?? '';
$search_client = trim($_GET['client'] ?? '');

// 🛠️ CORRECTION ICI : Utilisation de c.id_utilisateur selon l'image_4fc982.png
$sql = "SELECT c.*, u.nom, u.prenom, u.email, u.telephone 
        FROM commandes c 
        JOIN utilisateurs u ON c.id_utilisateur = u.id WHERE 1=1";
$params = [];

// Filtre par statut si Julie en choisit un
if (!empty($status_filter)) {
    $sql .= " AND c.statut = ?";
    $params[] = $status_filter;
}

// Filtre par nom/prénom de client si Julie tape une recherche
if (!empty($search_client)) {
    $sql .= " AND (u.nom LIKE ? OR u.prenom LIKE ?)";
    $params[] = "%$search_client%";
    $params[] = "%$search_client%";
}

$sql .= " ORDER BY c.date_commande DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params); // La ligne 36 va adorer cette correction !
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<main class="container-fluid py-5 px-4">
    <div class="row">
        
        <!-- En-tête du Dashboard épuré -->
        <div class="col-12 mb-4 d-flex justify-content-between align-items-center border-bottom pb-3">
            <div>
                <h2>Espace Employé — Tableau de Bord</h2>
                <p class="text-muted mb-0">Connecté(e) en tant que : <strong><?= htmlspecialchars($_SESSION['user_prenom'] ?? 'Julie') ?></strong></p>
            </div>
            <a href="employe-avis.php" class="btn btn-warning rounded-pill fw-bold shadow-sm">💬 Modérer les avis clients</a>
        </div>

        <div class="col-12 mb-4">
            <div class="card bg-light border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="GET" action="employe-dashboard.php" class="row g-3 align-items-end">
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Rechercher un client (Nom/Prénom)</label>
                            <input type="text" name="client" class="form-control" value="<?= htmlspecialchars($search_client) ?>" placeholder="Ex: Dupont">
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Filtrer par Statut</label>
                            <select name="statut" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="reçue" <?= $status_filter === 'reçue' ? 'selected' : '' ?>>Reçue</option>
                                <option value="accepté" <?= $status_filter === 'accepté' ? 'selected' : '' ?>>Accepté</option>
                                <option value="en préparation" <?= $status_filter === 'en préparation' ? 'selected' : '' ?>>En préparation</option>
                                <option value="en cours de livraison" <?= $status_filter === 'en cours de livraison' ? 'selected' : '' ?>>En cours de livraison</option>
                                <option value="livré" <?= $status_filter === 'livré' ? 'selected' : '' ?>>Livré</option>
                                <option value="en attente du retour de matériel" <?= $status_filter === 'en attente du retour de matériel' ? 'selected' : '' ?>>En attente du retour de matériel</option>
                                <option value="terminée" <?= $status_filter === 'terminée' ? 'selected' : '' ?>>Terminée</option>
                                <option value="annulée" <?= $status_filter === 'annulée' ? 'selected' : '' ?>>Annulée</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4 d-flex gap-2">
                            <button type="submit" class="btn btn-dark w-100 rounded-pill">Appliquer les filtres</button>
                            <a href="employe-dashboard.php" class="btn btn-outline-secondary w-100 rounded-pill">Réinitialiser</a>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3">N°</th>
                                    <th>Client</th>
                                    <th>Contact</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Statut Actuel</th>
                                    <th class="text-center pe-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($commandes)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Aucune commande trouvée.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($commandes as $cmd): ?>
                                        <tr>
                                            <td class="ps-3 fw-bold">#<?= $cmd['id'] ?></td>
                                            <td><?= htmlspecialchars($cmd['prenom'] . ' ' . $cmd['nom']) ?></td>
                                            <td>
                                                <small class="d-block">📞 <?= htmlspecialchars($cmd['telephone'] ?? 'Non renseigné') ?></small>
                                                <small class="text-muted">✉️ <?= htmlspecialchars($cmd['email']) ?></small>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></td>
                                            <td class="fw-bold"><?= number_format($cmd['total'], 2, ',', ' ') ?> €</td>
                                            <td>
                                                <?php
                                                // Changement de couleur dynamique du badge selon le statut
                                                $badge_color = "bg-secondary";
                                                if($cmd['statut'] === 'en préparation') $badge_color = "bg-warning text-dark";
                                                if($cmd['statut'] === 'en cours de livraison') $badge_color = "bg-info text-dark";
                                                if($cmd['statut'] === 'livré') $badge_color = "bg-success";
                                                if($cmd['statut'] === 'en attente du retour de matériel') $badge_color = "bg-danger";
                                                if($cmd['statut'] === 'terminée') $badge_color = "bg-dark";
                                                if($cmd['statut'] === 'annulée') $badge_color = "bg-light text-danger border border-danger";
                                                ?>
                                                <span class="badge <?= $badge_color ?> px-2 py-2 text-uppercase"><?= htmlspecialchars($cmd['statut']) ?></span>
                                            </td>
                                            <td class="text-center pe-3">
                                                <a href="employe-details-commande.php?id=<?= $cmd['id'] ?>" class="btn btn-sm btn-primary rounded-pill px-3">Gérer la commande</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</main>

<?php include 'includes/footer.php'; ?>