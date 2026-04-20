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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= $menu['titre'] ?> - Vite & Gourmand</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/css/style.css">
</head>
<body class="bg-light">
    <header class="navbar-custom sticky-top shadow-sm">
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="assets/Logo-Vite&Gourmand.png" alt="Logo" width="70">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="nos-menus.php">Menus</a></li>
                        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
                    </ul>
                </div>

                <div class="d-flex align-items-center">
                    <a href="panier.php" class="cart-icon me-3 position-relative">
                        🛒<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small">0</span>
                    </a>
                    <a href="connexion.php" class="btn btn-dark rounded-pill px-4">👤</a>
                </div>
            </div>
        </nav>
    </header>

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
                <a href="contact.php?menu=<?= urlencode($menu['titre']) ?>" class="btn btn-success btn-lg">Commander ce menu</a>
                <p class="text-center text-danger mt-2 small">Attention : seulement <?= $menu['stock'] ?> menus disponibles !</p>
            </div>
        </div>
    </div>
</div>
<footer class="bg-dark text-white py-5 mt-5">
        <div class="container text-center text-md-start">
            <div class="row gy-4">
                <div class="col-md-3">
                    <h5 class="fw-bold text-cheddar">Vite & Gourmand</h5>
                    <p class="small text-secondary">L'art de bien manger, cuisiné par Julie et livré par José.</p>
                </div>

                <div class="col-md-3">
        <h6 class="fw-bold text-white mb-3">Nos Horaires</h6>
        <ul class="list-unstyled small text-secondary mx-auto mx-md-0" style="max-width: 200px;">
            <li class="d-flex justify-content-between border-bottom border-secondary mb-1">
                <span>Lundi</span> 
                <span class="text-danger fw-bold">Fermé</span>
            </li>
            <li class="d-flex justify-content-between border-bottom border-secondary mb-1">
                <span>Mar - Ven</span> 
                <span>11h - 21h</span>
            </li>
            <li class="d-flex justify-content-between border-bottom border-secondary mb-1">
                <span>Samedi</span> 
                <span>10h - 22h</span>
            </li>
            <li class="d-flex justify-content-between border-bottom border-secondary mb-1">
                <span>Dimanche</span> 
                <span>10h - 15h</span>
            </li>
        </ul>
    </div>

            <div class="col-md-3">
                <h6 class="fw-bold text-white mb-3">Informations</h6>
                <ul class="list-unstyled d-flex flex-column gap-2">
                    <li><a href="#" class="text-white-50 text-decoration-none small">Mentions Légales</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">Politique de cookies</a></li>
                    <li><a href="#" class="text-white-50 text-decoration-none small">CGU</a></li>
                </ul>
            </div>

            <div class="col-md-3 text-secondary small">
                <h6 class="fw-bold text-white mb-3">Contact</h6>
                <p class="mb-1">julie@vite-gourmand.fr</p>
                <p> José : 06.69.25.58.47</p>
            </div>
        </div>
        <hr class="my-4 border-secondary">
            <div class="text-center">
                <p class="mb-0 small text-secondary">&copy; 2026 Vite & Gourmand - Tous droits réservés.</p>
            </div>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>