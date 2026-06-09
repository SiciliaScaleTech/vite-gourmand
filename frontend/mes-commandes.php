<?php
include 'includes/header.php';
require_once '../backend/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// Récupérer l'historique des commandes de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM commandes WHERE id_utilisateur = ? ORDER BY date_commande DESC");
$stmt->execute([$_SESSION['user_id']]);
$commandes = $stmt->fetchAll();
?>

<main class="container py-5">
    <h2 class="mb-4">Mes Commandes 📦</h2>

    <?php if (empty($commandes)): ?>
        <div class="alert alert-light border shadow-sm">
            Vous n'avez pas encore passé de commande. 
            <a href="nos-menus.php" class="alert-link text-primary">Découvrez nos menus ici !</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle shadow-sm bg-white rounded">
                <thead class="table-dark">
                    <tr>
                        <th>N° Commande</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commandes as $cmd): ?>
                        <tr>
                            <td><strong>#<?= $cmd['id'] ?></strong></td>
                            <td><?= date('d/m/Y H:i', strtotime($cmd['date_commande'])) ?></td>
                            <td><?= number_format($cmd['total'], 2) ?> €</td>
                            <td>
                                <span class="badge rounded-pill bg-info text-dark">
                                    <?= htmlspecialchars($cmd['statut']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-dark rounded-pill px-3 disabled">Détails</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>