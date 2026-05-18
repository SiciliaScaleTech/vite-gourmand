<?php
// 1. On appelle le header (qui fait le session_start)
include 'includes/header.php';

// 2. On appelle la config BDD
require_once '../backend/config.php';

// 3. On récupère les menus UNIQUEMENT pour cette page
try {
    $query = $pdo->query("SELECT id, nom_technique, titre, description, galerie, prix_pers, pers_min FROM menu ORDER BY id ASC");
    $menus = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

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

    <div class="container my-5">
    <h1 class="text-center mb-5">Nos Menus</h1>

    <div class="row g-4" id="menu-container">
        <?php foreach ($menus as $menu): 
            $galerie = explode('|', $menu['galerie']);
            $imageVignette = $galerie[0];
        ?>
        
        <div class="col-md-4 menu-item" 
             data-theme="<?= $menu['nom_technique'] ?>" 
             data-prix="<?= $menu['prix_pers'] ?>"
             data-pers-min="<?= $menu['pers_min'] ?>">

            <div class="card h-100 shadow-sm border-0">
                <img src="<?= $imageVignette ?>" class="card-img-top" alt="<?= $menu['titre'] ?>" style="height: 220px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold"><?= $menu['titre'] ?></h5>
                    <p class="card-text text-muted flex-grow-1">
                        <?= mb_strimwidth($menu['description'], 0, 100, "...") ?>
                    </p>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <span class="h5 mb-0 text-primary"><?= $menu['prix_pers'] ?>€ <small class="text-muted fs-6">/ pers</small></span>
                        <a href="details-menu.php?id=<?= $menu['id'] ?>" class="btn btn-outline-primary">Voir le détail</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div id="no-result-message" class="text-center py-5" style="display: none;">
        <div class="display-1">🍽️</div>
        <h3 class="mt-3 fw-bold text-muted">Aucun menu ne correspond à vos critères</h3>
        <p class="text-secondary">Essayez de modifier vos filtres pour voir plus de délices !</p>
    </div>
</div>
    
</div>
    
</main>
    <script src="styles/script/nos-menus.js"></script>
    <?php include 'includes/footer.php'; ?>


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

  
    