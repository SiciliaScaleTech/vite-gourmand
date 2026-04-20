<?php
include 'includes/header.php';
require_once '../backend/config.php';

$panier_details = [];
$total_general = 0;

if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    // On récupère tous les IDs du panier pour faire une seule requête SQL
    $ids = array_keys($_SESSION['panier']);
    $comma_separated_ids = implode(',', array_fill(0, count($ids), '?'));

    $stmt = $pdo->prepare("SELECT id, titre, prix_pers, galerie FROM menu WHERE id IN ($comma_separated_ids)");
    $stmt->execute($ids);
    $menus_bdd = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // On prépare un tableau propre pour l'affichage
    foreach ($menus_bdd as $menu) {
        $quantite = $_SESSION['panier'][$menu['id']];
        $sous_total = $menu['prix_pers'] * $quantite;

        $galerie_nettoyee = str_replace('assets/', '', $menu['galerie']);
    // 2. On découpe la chaîne dès qu'on voit un "|"
        $images_tableau = explode('|', $galerie_nettoyee);
    // 3. On ne garde que la première image (l'index 0)
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
}
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

        <div class="d-flex justify-content-between mt-4">
            <a href="nos-menus.php" class="btn btn-outline-dark rounded-pill">Continuer mes achats</a>
            <a href="commander.php" class="btn btn-cheddar rounded-pill px-5 fw-bold">Valider la commande</a>
        </div>
    <?php endif; ?>
</main>



<?php include 'includes/footer.php'; ?>