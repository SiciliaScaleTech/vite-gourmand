<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand | Accueil</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/css/style.css">
    <link rel="stylesheet" href="styles/css/nos-menus.css">
</head>
<body>

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
                        <li class="nav-item"><a class="nav-link active fw-bold" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="menus.php">Menus</a></li>
                        <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
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

        <section class="hero-banner text-center">
            <div class="container">
                <h1 class="display-3 fw-bold">Vite & Gourmand</h1>
                <p class="fs-4">Nos Menus Thématiques</p>
                <a href="#menus" class="btn btn-cheddar btn-lg px-5 py-3 rounded-pill fw-bold">Commander maintenant</a>
            </div>
        </section>

       <main class="container py-5">

    <section class="card shadow-sm border-0 bg-mimolette-light p-4 mb-5">
        <form id="filterForm" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Thème</label>
                <select name="theme" class="form-select border-0 shadow-sm">
                    <option value="">Tous les thèmes</option>
                    <option value="Noel">Noël</option>
                    <option value="Paques">Pâques</option>
                    <option value="Halloween">Halloween</option>
                    <option value="Classique">Classique</option>
                    <option value="Mariage">Mariage</option>
                    <option value="Bapteme">Baptême</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Régime</label>
                <select name="regime" class="form-select border-0 shadow-sm">
                    <option value="">Tous</option>
                    <option value="vegetarien">Végétarien</option>
                    <option value="vegan">Vegan</option>
                    <option value="classique">Classique</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Prix Max</label>
                <input type="number" name="prix_max" class="form-control border-0 shadow-sm" placeholder="Max €">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Fourchette (€)</label>
                <div class="d-flex gap-2">
                    <input type="number" name="min" class="form-control border-0 shadow-sm" placeholder="Min">
                    <input type="number" name="max" class="form-control border-0 shadow-sm" placeholder="Max">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Pers. Min</label>
                <input type="number" name="pers_min" class="form-control border-0 shadow-sm" placeholder="Nb pers.">
            </div>
            <div class="col-12 text-center mt-4">
                <button type="button" onclick="filterMenus()" class="btn btn-cheddar rounded-pill px-5 fw-bold shadow">
                    Actualiser les menus 
                </button>
            </div>
        </form>
    </section>

    <div class="row g-4" id="menusContainer">
        
        <div class="col-md-4 menu-item" data-theme="Noel">
            <div class="card h-100 shadow-sm border-0 card-hover">
                <img src="assets/noel-img-menu.jpg" class="card-img-top rounded-top" alt="Noël">
                <div class="card-body">
                    <h5 class="fw-bold">Menu de Noël</h5>
                    <p class="text-muted small">Tradition et féerie avec notre chapon farci.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-cheddar">45€ / pers.</span>
                        <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalNoel">Détails</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 menu-item" data-theme="Paques">
            <div class="card h-100 shadow-sm border-0 card-hover">
                <img src="assets/paque-img-menu.jpg" class="card-img-top rounded-top" alt="Pâques">
                <div class="card-body">
                    <h5 class="fw-bold">Menu de Pâques</h5>
                    <p class="text-muted small">L'agneau de sept heures et ses douceurs chocolatées.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-cheddar">38€ / pers.</span>
                        <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalPaques">Détails</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 menu-item" data-theme="Halloween">
            <div class="card h-100 shadow-sm border-0 card-hover">
                <img src="assets/halloween-img-menu.jpg" class="card-img-top rounded-top" alt="Halloween">
                <div class="card-body">
                    <h5 class="fw-bold">Menu Halloween</h5>
                    <p class="text-muted small">Déclinaison de courges et saveurs mystérieuses.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-cheddar">32€ / pers.</span>
                        <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalHalloween">Détails</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 menu-item" data-theme="Classique">
            <div class="card h-100 shadow-sm border-0 card-hover">
                <img src="assets/classique-img-menu.jpg" class="card-img-top rounded-top" alt="Classique">
                <div class="card-body">
                    <h5 class="fw-bold">Menu Classique</h5>
                    <p class="text-muted small">Les incontournables de Julie pour vos repas quotidiens.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-cheddar">25€ / pers.</span>
                        <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalClassique">Détails</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 menu-item" data-theme="Mariage">
            <div class="card h-100 shadow-sm border-0 card-hover">
                <img src="assets/mariage-img-menu.jpg" class="card-img-top rounded-top" alt="Mariage">
                <div class="card-body">
                    <h5 class="fw-bold">Menu Mariage</h5>
                    <p class="text-muted small">Un banquet d'exception pour le plus beau jour.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-cheddar">85€ / pers.</span>
                        <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalMariage">Détails</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 menu-item" data-theme="Bapteme">
            <div class="card h-100 shadow-sm border-0 card-hover">
                <img src="assets/bapteme-img-menu.jpg" class="card-img-top rounded-top" alt="Baptême">
                <div class="card-body">
                    <h5 class="fw-bold">Menu Baptême</h5>
                    <p class="text-muted small">Douceur et partage pour célébrer en famille.</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-cheddar">55€ / pers.</span>
                        <button class="btn btn-outline-dark btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#modalBapteme">Détails</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main><footer class="bg-dark text-white py-5 mt-5">
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

<?php 
$menus_details = [
    'Noel' => [
        'titre' => 'Menu de Noël gourmet',
        'galerie' => ['assets/noel1-img-details.jpg', 'assets/noel2-img-detail.jpg', 'assets/noel3-img-detail.jpeg'],
        'plats' => ['Entrée: Foi gras', 'plat: Chapon aux marrons', 'Dessert: Bûche'],
        'prix_pers'   => 45,
        'pers_min'    => 6,
        'description' => 'Un festin traditionnel et chaleureux pour vos fêtes de fin d\'année.',
        'conditions'  => 'Nécessite de commander 10 jours avant le réveillon.',
        'allergene' => 'Gluten, Fruits à coque',
        'stock' => 5
    ],
    'Paques' => [
        'titre' => 'Menu de Pâques',
        'galerie' => ['assets/paque1-img-detail.webp', 'assets/paque2-img-detail.jpg', 'assets/paque3-img-detail.webp'],
        'plats' => ['Entrée: Asperges', 'plat: Agneau pascale', 'Dessert: Gateau avec sa poule en chocolat'],
        'prix_pers'   => 38,
        'pers_min'    => 4,
        'description' => 'Célébrez le printemps avec des saveurs authentiques et de saison.',
        'conditions'  => 'Commande possible jusqu\'à 5 jours avant.',
        'allergene' => 'Lactose, oeufs',
        'stock' => 12
    ],
    'Halloween' => [
        'titre' => 'Menu d\'halloween',
        'galerie' => ['assets/halloween1-img-detail.jpg', 'assets/halloween2-img-detail.jpg', 'assets/halloween3-img-detail.png'],
        'plats' => ['Entrée: velouté de courge', 'plat: citrouille farcis', 'Dessert: citrouille avec son coulis mystère'],
        'prix_pers'   => 40,
        'pers_min'    => 6,
        'description' => 'Célébrez halloween en famille ou entre amis avec des saveurs mystérieuse.',
        'conditions'  => 'Commande possible jusqu\'à 8 jours avant.',
        'allergene' => 'neant',
        'stock' => 8
    ],
     'Classique' => [
        'titre' => 'Menu classique',
        'galerie' => ['assets/classique1-img-detail.jpeg', 'assets/classique2-img-detail.webp', 'assets/classique3-img-detail.webp'],
        'plats' => ['Entrée: salade et tomates cerises', 'plat: tranches de boeuf avec pomme de terre', 'Dessert: gateaux aux noix'],
        'prix_pers'   => 25,
        'pers_min'    => 2,
        'description' => 'Une repas équilibré qaund vous n\'avez pas eu le temps de cuisiner.',
        'conditions'  => 'Commande possible jusqu\'à 3 jours avant.',
        'allergene' => 'arachide, noix',
        'stock' => 17
    ],
     'Mariage' => [
        'titre' => 'Menu de mariage',
        'galerie' => ['assets/mariage1-img-detail.png', 'assets/mariage2-img-detail.jpeg', 'assets/mariage3-img-detail.jpg'],
        'plats' => ['Entrée: jambon sec/crevette rose', 'plat: roulés au jambon, galette de légumes, rôti', 'Dessert: pièce montée'],
        'prix_pers'   => 60,
        'pers_min'    => 20,
        'description' => 'Une repas digne du plus beau jour de votre vie.',
        'conditions'  => 'Commande possible jusqu\'à 1 mois avant.',
        'allergene' => 'crustacés',
        'stock' => 20
    ],
    'Bapteme' => [
        'titre' => 'Menu de bapteme',
        'galerie' => ['assets/bapteme1-img-detail.jpg', 'assets/bapteme2-img-detail.jpg', 'assets/bapteme3-img-detail.webp'],
        'plats' => ['Entrée: saumons sur toast', 'plat: velouté de tomate et roulés aux lard avec ses oeufs', 'Dessert: Cupcake'],
        'prix_pers'   => 30,
        'pers_min'    => 15,
        'description' => 'Un jour important pour vous et votre enfant, laissez nous préparer votre repas pour profiter pleinement de ce jour si particulier.',
        'conditions'  => 'Commande possible jusqu\'à 3 semaines avant.',
        'allergene' => 'oeufs, saumon',
        'stock' => 10
    ],
]
?>


<?php
foreach ($menus_details as $id => $info) : ?>
<div class="modal fade" id="modal<?php echo $id; ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-mimolette border-0">
                <h5 class="modal-title fw-bold"><?php echo $info['titre']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">
    <div class="row">
        <div class="col-md-6">
            <div class="main-img-container mb-3">
                <img src="<?php echo $info['galerie'][0]; ?>" id="mainImg<?php echo $id; ?>" class="img-fluid rounded-4 shadow-sm w-100" style="height: 250px; object-fit: cover;">
            </div>
            <div class="d-flex gap-2">
                <?php foreach($info['galerie'] as $img) : ?>
                    <img src="<?php echo $img; ?>" class="img-thumbnail rounded-3 thumb-gallery" style="width: 60px; height: 45px; object-fit: cover; cursor: pointer;" onclick="changeImg('<?php echo $id; ?>', '<?php echo $img; ?>')">
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-6">
            <h6 class="fw-bold">Description :</h6>
            <p class="small text-muted"><?php echo $info['description']; ?></p>
            
            <h6 class="fw-bold mt-3">Composition :</h6>
            <ul class="small mb-3">
                <?php foreach($info['plats'] as $plat): ?>
                    <li><?php echo $plat; ?></li>
                <?php endforeach; ?>
            </ul>

            <div class="bg-light p-3 rounded-3">
                <p class="small mb-1"><strong>👥 Personnes minimum :</strong> <?php echo $info['pers_min']; ?></p>
                <p class="small mb-1"><strong>🕒 Conditions :</strong> <?php echo $info['conditions']; ?></p>
                <p class="small mb-0"><strong>⚠️ Allergènes :</strong> <span class="text-danger"><?php echo $info['allergene']; ?></span></p>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer border-0 bg-mimolette-light py-3">
    <div class="d-flex justify-content-between align-items-center w-100 px-3">
        <div>
            <span class="text-muted small">Total pour <?php echo $info['pers_min']; ?> pers. min :</span>
            <h4 class="fw-bold text-cheddar mb-0">
                <?php echo ($info['prix_pers'] * $info['pers_min']); ?> €
            </h4>
        </div>
        <button type="button" class="btn btn-cheddar rounded-pill px-4 fw-bold shadow-sm">
            Réserver ce menu
        </button>
    </div>
</div>
<?php endforeach; ?>

  
    <script src="styles/script/nos-menus.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>