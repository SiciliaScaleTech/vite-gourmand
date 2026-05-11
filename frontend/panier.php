<?php
include 'includes/header.php';
require_once '../backend/config.php';

$panier_details = [];
$total_general = 0;
$reduction = 0;
$frais_livraison = 0;
$total_final = 0;
$quantite_totale = 0;

if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    // 1. Récupération des produits
    $ids = array_keys($_SESSION['panier']);
    $comma_separated_ids = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, titre, prix_pers, galerie FROM menu WHERE id IN ($comma_separated_ids)");
    $stmt->execute($ids);
    $menus_bdd = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Calcul du total HT et préparation des détails
    foreach ($menus_bdd as $menu) {
        $quantite = $_SESSION['panier'][$menu['id']];
        $sous_total = $menu['prix_pers'] * $quantite;

        // Nettoyage image
        $galerie_nettoyee = str_replace('assets/', '', $menu['galerie']);
        $images_tableau = explode('|', $galerie_nettoyee);
        $premiere_image = $images_tableau[0];
        
        $total_general += $sous_total;

        $panier_details[] = [
            'id' => $menu['id'],
            'titre' => $menu['titre'],
            'prix' => $menu['prix_pers'],
            'img' => $premiere_image,
            'qte' => $quantite,
            'sous_total' => $sous_total
        ];
    }

    // --- CALCUL DES FRAIS ET RÉDUCTIONS ---
    
    $quantite_totale = array_sum($_SESSION['panier']);

    // 1. Réduction 10% si > 5 articles
    if ($quantite_totale > 5) {
        $reduction = $total_general * 0.10;
    }

    // 2. Frais kilométriques
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT ville FROM utilisateurs WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        if ($user) {
            $ville_client = strtolower(trim($user['ville']));

            if ($ville_client == "maisse") {
                $frais_livraison = 2.00;
            } elseif (in_array($ville_client, ["evry", "corbeil", "essonnes", "boutigny"])) {
                $frais_livraison = 5.00;
            } else {
                $frais_livraison = 8.50;
            }
        }
    }

    // 3. Total Final
    $total_final = ($total_general - $reduction) + $frais_livraison;
} 
// L'accolade fermante du "if empty panier" doit être ici, après tous les calculs
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
                            <span class="badge bg-secondary p-2 px-3"><?= $item['qte'] ?></span>
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

        <div class="card shadow-sm p-4 mt-4 rounded-4">
            <div class="d-flex justify-content-between">
                <span>Sous-total :</span>
                <span><?= number_format($total_general, 2) ?> €</span>
            </div>

            <?php if ($reduction > 0): ?>
                <div class="d-flex justify-content-between text-success">
                    <span>Réduction Groupe (10%) :</span>
                    <span>- <?= number_format($reduction, 2) ?> €</span>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between">
                <span>Frais de livraison :</span>
                <span>+ <?= number_format($frais_livraison, 2) ?> €</span>
            </div>

            <hr>

            <div class="d-flex justify-content-between h4">
                <strong>Total à payer :</strong>
                <strong class="text-primary"><?= number_format($total_final, 2) ?> €</strong>
            </div>

            <div class="alert alert-info mt-3 small">
                <i class="bi bi-info-circle"></i> Le paiement sera effectué lors du retrait de votre commande.
            </div>

            <a href="valider-commande.php" class="btn btn-cheddar btn-lg w-100 rounded-pill mt-2">
                Confirmer ma commande
            </a>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="nos-menus.php" class="btn btn-outline-dark rounded-pill">Continuer mes achats</a>
        </div>
    <?php endif; ?>
</main>



<?php include 'includes/footer.php'; ?>