<?php
session_start();
require_once '../../backend/config.php';

// SÉCURITÉ : Accès réservé aux employés et admins
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['employe', 'admin'])) {
    header('Location: ../index.php');
    exit();
}

$message = "";

// Récupération d'un éventuel message flash de succès
if (isset($_SESSION['flash_success'])) {
    $message = "<div class='alert alert-success fw-bold'>" . $_SESSION['flash_success'] . "</div>";
    unset($_SESSION['flash_success']);
}

// 1. TRAITEMENT DE LA SUPPRESSION D'UN MENU / PLAT
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?"); 
        $stmt->execute([$delete_id]);
        $message = "<div class='alert alert-success fw-bold'>🗑️ Le menu a été retiré de la carte avec succès !</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger fw-bold'>⚠️ Impossible de supprimer ce menu. S'il est lié à des commandes passées, la base de données bloque la suppression.</div>";
    }
}

// 2. RÉCUPÉRATION DE TOUS LES MENUS
try {
    $stmt = $pdo->prepare("SELECT * FROM menu ORDER BY categorie ASC, titre ASC"); 
    $stmt->execute();
    $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Erreur SQL de chargement : " . $e->getMessage() . "</div>";
    $menus = [];
}

include '../includes/header.php';
?>

<main class="container py-5">
    <div class="row">
        
        <div class="col-12 mb-4 d-flex justify-content-between align-items-center border-bottom pb-3">
            <div>
                <a href="employe-dashboard.php" class="btn btn-outline-secondary rounded-pill btn-sm mb-2">⬅️ Retour au Dashboard</a>
                <h2 class="mb-0 fw-bold">Gestion de la Carte (Menus & Plats)</h2>
            </div>
            <a href="employe-ajouter-menu.php" class="btn btn-success rounded-pill fw-bold shadow-sm">+ Ajouter un menu / plat</a>
        </div>

        <?= $message ?>

        <div class="col-12">
            <div class="card shadow border-0 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="ps-3" style="width: 80px;">Visuel</th>
                                    <th>Nom / Titre</th>
                                    <th>Catégorie</th>
                                    <th>Description / Allergènes</th>
                                    <th>Stock</th>
                                    <th>Prix (Pers)</th>
                                    <th class="text-center pe-3" style="width: 220px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($menus)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <p class="fs-4 mb-1">La carte est vide </p>
                                            <p class="mb-0">Clique sur "Ajouter un menu / plat" pour commencer.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($menus as $m): ?>
                                        <tr>
                                            <td class="ps-3">
                                                <?php 
                                                    $galerie_images = !empty($m['galerie']) ? explode('|', $m['galerie']) : [];
                                                    $image_vignette = !empty($galerie_images[0]) ? $galerie_images[0] : 'assets/images/pizza-placeholder.jpg';
                                                    
                                                    $chemin_image_vignette = "../" . $image_vignette;
                                                ?>
                                                <img src="<?= htmlspecialchars($chemin_image_vignette) ?>" 
                                                     alt="<?= htmlspecialchars($m['titre']) ?>" 
                                                     class="rounded shadow-sm" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td>
                                                <span class="fw-bold text-dark d-block"><?= htmlspecialchars($m['titre']) ?></span>
                                                <small class="text-muted">Tech : <?= htmlspecialchars($m['nom_technique'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary px-2 py-1 text-capitalize"><?= htmlspecialchars($m['categorie']) ?></span>
                                            </td>
                                            <td>
                                                <small class="d-block text-dark text-truncate" style="max-width: 250px;">
                                                    <?= htmlspecialchars($m['description'] ?? 'Aucune description.') ?>
                                                </small>
                                                <?php if(!empty($m['allergene']) && strtolower($m['allergene']) !== 'neant'): ?>
                                                    <small class="text-danger d-block">⚠️ Allergènes : <?= htmlspecialchars($m['allergene']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (($m['stock'] ?? 0) <= 0): ?>
                                                    <span class="badge bg-danger px-2 py-1">Épuisé (0)</span>
                                                <?php elseif (($m['stock'] ?? 0) <= 5): ?>
                                                    <span class="badge bg-warning text-dark px-2 py-1">Faible (<?= htmlspecialchars($m['stock']) ?>)</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success px-2 py-1">En stock (<?= htmlspecialchars($m['stock']) ?>)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-success"><?= number_format($m['prix_pers'], 2, ',', ' ') ?> €</td>
                                            <td class="text-center pe-3">
                                                <div class="d-flex gap-2 justify-content-center">
                                                    <a href="employe-modification-menu.php?id=<?= $m['id'] ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">Modifier</a>
                                                    <a href="employe-carte.php?delete_id=<?= $m['id'] ?>" 
                                                       class="btn btn-sm btn-danger rounded-pill px-3" 
                                                       onclick="return confirm('Supprimer définitivement le menu « <?= htmlspecialchars($m['titre']) ?> » ?');">
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
        </div>

    </div>
</main>

<?php include '../includes/footer.php'; ?>