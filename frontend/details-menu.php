<?php include 'includes/header.php'; ?>

<?php
require_once '../backend/config.php';

// 1. On récupère l'ID envoyé dans l'URL (ex: details-menu.php?id=1)
// On utilise filter_input pour sécuriser la donnée reçue
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    // Si pas d'ID valide, on redirige vers la liste
    header('Location: nos-menus.php');
    exit;
}

try {
    // 2. On va chercher TOUTES les infos de ce menu précis
    $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $menu = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si le menu n'existe pas en BDD
    if (!$menu) {
        die("Désolé, ce menu n'existe pas.");
    }

    // 3. On prépare les listes (on transforme les chaînes de texte en tableaux PHP)
    $galerie = explode('|', $menu['galerie']);
    $plats = explode('|', $menu['plats']);

} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>



<div class="container py-5">
    <a href="nos-menus.php" class="btn btn-outline-secondary mb-4">← Retour aux menus</a>

    <div class="row g-5">
        <div class="col-lg-6">
            <div id="carouselMenu" class="carousel slide shadow rounded overflow-hidden" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php foreach ($galerie as $index => $image): ?>
                        <div class="carousel-item <?= ($index === 0) ? 'active' : '' ?>">
                            <img src="<?= $image ?>" class="d-block w-100" alt="Photo du menu" style="height: 450px; object-fit: cover;">
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselMenu" data-bs-content="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselMenu" data-bs-next="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        </div>

        <div class="col-lg-6">
            <h1 class="display-5 fw-bold mb-3"><?= $menu['titre'] ?></h1>
            <p class="lead text-muted mb-4"><?= $menu['description'] ?></p>
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h4 class="card-title mb-3">Composition du menu</h4>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($plats as $plat): ?>
                            <li class="list-group-item px-0"> <?= $plat ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-6">
                    <p class="mb-1 text-muted">Prix par personne</p>
                    <h3 class="text-primary"><?= $menu['prix_pers'] ?> €</h3>
                </div>
                <div class="col-6">
                    <p class="mb-1 text-muted">Minimum requis</p>
                    <h3><?= $menu['pers_min'] ?> pers.</h3>
                </div>
            </div>

            <div class="alert alert-warning border-0 shadow-sm">
                <strong>Allergènes :</strong> <?= $menu['allergene'] ?>
            </div>

            <div class="p-3 bg-white rounded shadow-sm mb-4">
                <p class="mb-1 text-muted small">Conditions particulières :</p>
                <p class="mb-0 italic"><?= $menu['conditions'] ?></p>
            </div>

            <div class="d-grid gap-2">
                <a href="ajouter-panier.php?id=<?= $menu['id'] ?>" class="btn btn-cheddar btn-lg px-5 rounded-pill fw-bold">
                    Commander ce menu
                </a>
                <p class="text-center text-danger mt-2 small">Attention : seulement <?= $menu['stock'] ?> menus disponibles !</p>
            </div>
        </div>
    </div>
</div>


<?php include 'includes/footer.php'; ?>