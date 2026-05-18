<?php
session_start();
require_once '../backend/config.php';

// --- 1. LOGIQUE DE MISE À JOUR (PLUS / MOINS / SUPPRIMER) ---
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id_modif = $_GET['id'];
    $action = $_GET['action'];

    if (isset($_SESSION['panier'][$id_modif])) {
        if ($action === 'augmenter') {
            $_SESSION['panier'][$id_modif]++;
        } elseif ($action === 'diminuer') {
            $_SESSION['panier'][$id_modif]--;
            // Si la quantité tombe à 0, on retire le menu du panier
            if ($_SESSION['panier'][$id_modif] <= 0) {
                unset($_SESSION['panier'][$id_modif]);
            }
        }
    }
    // On recharge la page pour valider les calculs et "nettoyer" l'URL des paramètres GET
    header("Location: panier.php");
    exit();
}

// --- 2. RÉCUPÉRATION DES DONNÉES DU PANIER ---
$panier_details = [];
$total_general = 0;

if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    $ids = array_keys($_SESSION['panier']);
    $comma_separated_ids = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, titre, prix_pers, galerie FROM menu WHERE id IN ($comma_separated_ids)");
    $stmt->execute($ids);
    $menus_bdd = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($menus_bdd as $menu) {
        $quantite = $_SESSION['panier'][$menu['id']];
        $sous_total = $menu['prix_pers'] * $quantite;
        $total_general += $sous_total;

        // Gestion de l'image
        $galerie_nettoyee = str_replace('assets/', '', $menu['galerie']);
        $images_tableau = explode('|', $galerie_nettoyee);
        $premiere_image = $images_tableau[0];

        $panier_details[] = [
            'id' => $menu['id'],
            'titre' => $menu['titre'],
            'prix' => $menu['prix_pers'],
            'img' => $premiere_image,
            'qte' => $quantite,
            'sous_total' => $sous_total
        ];
    }
}

include 'includes/header.php'; 
?>

<main class="container py-5">
    <h1 class="mb-4">Mon Panier 🛒</h1>

    <?php if (empty($panier_details)): ?>
    <div class="alert alert-cheddar shadow-sm border-0">
        Votre panier est vide. <a href="nos-menus.php" class="fw-bold text-dark">Découvrir nos menus</a>
    </div>
<?php else: ?>
    
        <div class="table-responsive">
            <table class="table align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Menu</th>
                        <th>Prix Unitaire</th>
                        <th>Quantité</th>
                        <th>Sous-total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($panier_details as $item): ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="assets/<?= $item['img'] ?>" alt="<?= $item['titre'] ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded me-3">
                                <span class="fw-bold"><?= $item['titre'] ?></span>
                            </div>
                        </td>
                        <td><?= number_format($item['prix'], 2) ?> €</td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <a href="panier.php?id=<?= $item['id'] ?>&action=diminuer" 
                                class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center" 
                                style="width: 28px; height: 28px; text-decoration: none;">
                                -
                                </a>

                                <span class="fw-bold fs-5" style="min-width: 20px; text-align: center;">
                                    <?= $item['qte'] ?>
                                </span>

                                <a href="panier.php?id=<?= $item['id'] ?>&action=augmenter" 
                                class="btn btn-sm btn-outline-success rounded-circle d-flex align-items-center justify-content-center" 
                                style="width: 28px; height: 28px; text-decoration: none;">
                                +
                                </a>
                            </div>
                        </td>
                        <td class="fw-bold"><?= number_format($item['sous_total'], 2) ?> €</td>
                        <td>
                            <a href="supprimer-item.php?id=<?= $item['id'] ?>" class="text-danger"onclick="return confirm('Voulez-vous vraiment supprimer ce menu ?')">🗑️</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <td colspan="3" class="text-end fw-bold fs-5">Total Général :</td>
                        <td colspan="2" class="text-primary fw-bold fs-5"><?= number_format($total_general, 2) ?> €</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="alert alert-info d-inline-block mt-2 border-0 shadow-sm">
            <i class="bi bi-info-circle-fill me-2"></i> 
            ℹ️ <strong>Paiement sécurisé :</strong> Le règlement s'effectue directement lors du retrait de votre commande.
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="nos-menus.php" class="btn btn-outline-dark rounded-pill">Continuer mes achats</a>
            <a href="valider-commande.php" class="btn btn-cheddar rounded-pill px-5 fw-bold">Valider la commande</a>
        </div>
    <?php endif; ?>
</main>



<?php include 'includes/footer.php'; ?>