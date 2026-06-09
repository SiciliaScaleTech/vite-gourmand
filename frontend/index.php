<?php
// Inclure ta config si ce n'est pas déjà fait sur ton index
require_once '../backend/config.php'; 

// Récupérer les 5 derniers avis VALIDÉS uniquement
$stmt = $pdo->prepare("SELECT a.*, u.prenom, u.nom 
                        FROM avis a 
                        JOIN utilisateurs u ON a.id_utilisateur = u.id 
                        WHERE a.statut = 'valide' 
                        ORDER BY a.date_avis DESC LIMIT 5");
$stmt->execute();
$avis_valides = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'includes/header.php'; ?>

    <main>
        <section class="hero-banner text-center">
            <div class="container">
                <h1 class="display-3 fw-bold">Vite & Gourmand</h1>
                <p class="fs-4">L'excellence culinaire à votre porte.</p>
                <a href="nos-menus.php" class="btn btn-cheddar btn-lg px-5 py-3 rounded-pill fw-bold">Commander maintenant</a>
            </div>
        </section>

        <section class="py-5 bg-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2 class="fw-bold mb-4">Une passion, deux talents</h2>
                        <p class="lead text-muted">Vite & Gourmand, c'est l'alliance parfaite entre la passion de Julie pour la gastronomie et le sens du service de José.</p>
                        <p>Notre concept est simple : vous proposer des repas sains, cuisinés chaque matin avec des produits de saison, et vous les livrer en un temps record. Nous croyons que "bien manger" ne devrait jamais être sacrifié par manque de temps.</p>
                    </div>
                    <div class="col-md-6 text-center">
                        <img src="https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&w=600" class="img-fluid rounded-4 shadow" alt="Cuisine de Julie">
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-light">
            <div class="container text-center">
                <h2 class="fw-bold mb-5">Notre expertise à votre service</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                            <div class="fs-1 mb-3"></div>
                            <h4 class="fw-bold">Julie</h4>
                            <p class="text-muted">Chef de formation, elle imagine et prépare des menus équilibrés qui revisitent les classiques de la gastronomie.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                            <div class="fs-1 mb-3"></div>
                            <h4 class="fw-bold">José</h4>
                            <p class="text-muted">Expert en logistique urbaine, il garantit une livraison éclair tout en préservant la température de vos plats.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                            <div class="fs-1 mb-3"></div>
                            <h4 class="fw-bold">Engagement</h4>
                            <p class="text-muted">Nous travaillons exclusivement avec des producteurs locaux pour garantir fraîcheur et traçabilité irréprochable.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ================= SECTION AVIS CLIENTS ================= -->
<section class="py-5 bg-light text-center">
    <div class="container">
        <h2 class="mb-4 fw-bold">Ce que disent nos clients </h2>
        
        <?php if (empty($avis_valides)): ?>
            <!-- Message de secours si Julie n'a pas encore validé d'avis -->
            <p class="text-muted fs-5">Aucun avis disponible pour le moment. Soyez le premier à en laisser un !</p>
        <?php else: ?>
            
            <!-- Carrousel Bootstrap pour le défilement des avis -->
            <div id="carouselAvis" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner justify-content-center">
                    
                    <?php foreach ($avis_valides as $index => $av): ?>
                        <!-- La classe 'active' est obligatoire sur le TOUT PREMIER élément pour que le carrousel démarre -->
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>" data-bs-interval="4500">
                            <div class="row justify-content-center">
                                <div class="col-md-8 bg-white shadow-sm p-4 rounded-4 my-3 border border-light">
                                    
                                    <!-- Affichage des étoiles de la note -->
                                    <div class="text-warning mb-2 fs-4">
                                        <?= str_repeat('★', $av['note']) ?><?= str_repeat('☆', 5 - $av['note']) ?>
                                    </div>
                                    
                                    <!-- Commentaire du client -->
                                    <p class="fs-5 text-dark px-4 font-italic mb-3">
                                        " <?= htmlspecialchars($av['commentaire']) ?> "
                                    </p>
                                    
                                    <!-- Signature (Prénom et Première lettre du Nom) -->
                                    <h6 class="fw-bold text-secondary mb-0">
                                        - <?= htmlspecialchars($av['prenom'] . ' ' . strtoupper(substr($av['nom'], 0, 1)) . '.') ?>
                                    </h6>
                                    
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>

                <!-- Boutons de navigation Flèches (Précédent / Suivant) -->
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselAvis" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselAvis" data-bs-slide="next">
                    <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>
                
            </div>
        <?php endif; ?>
        
    </div>
</section>
    </main>

    
    <?php include 'includes/footer.php'; ?>