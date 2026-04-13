<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand | Accueil</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/css/style.css">
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
                    <a href="connexion.php" class="btn btn-dark rounded-pill px-4">Connexion 👤</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero-banner text-center">
            <div class="container">
                <h1 class="display-3 fw-bold">Vite & Gourmand</h1>
                <p class="fs-4">L'excellence culinaire à votre porte.</p>
                <a href="#menus" class="btn btn-cheddar btn-lg px-5 py-3 rounded-pill fw-bold">Commander maintenant</a>
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
                            <div class="fs-1 mb-3">👩‍🍳</div>
                            <h4 class="fw-bold">Julie</h4>
                            <p class="text-muted">Chef de formation, elle imagine et prépare des menus équilibrés qui revisitent les classiques de la gastronomie.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                            <div class="fs-1 mb-3">🚴‍♂️</div>
                            <h4 class="fw-bold">José</h4>
                            <p class="text-muted">Expert en logistique urbaine, il garantit une livraison éclair tout en préservant la température de vos plats.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                            <div class="fs-1 mb-3">🌿</div>
                            <h4 class="fw-bold">Engagement</h4>
                            <p class="text-muted">Nous travaillons exclusivement avec des producteurs locaux pour garantir fraîcheur et traçabilité irréprochable.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-white">
            <div class="container text-center">
                <h2 class="fw-bold mb-4">Ils nous font confiance</h2>
                <div class="testimonial-scroll">
                    <div class="card testimonial-card shadow-sm p-4 bg-mimolette-light border-0">
                        <p class="fst-italic mb-3">"Un service incroyable. Les plats de Julie sont dignes d'un restaurant étoilé et José est d'une ponctualité rare."</p>
                        <h6 class="fw-bold mb-1">Amélie R.</h6>
                        <span class="badge bg-success">Avis vérifié</span>
                    </div>
                    <div class="card testimonial-card shadow-sm p-4 bg-mimolette-light border-0">
                        <p class="fst-italic mb-3">"Enfin une alternative saine pour mes déjeuners au bureau. Je recommande le menu du marché !"</p>
                        <h6 class="fw-bold mb-1">Thomas B.</h6>
                        <span class="badge bg-success">Avis vérifié</span>
                    </div>
                    <div class="card testimonial-card shadow-sm p-4 bg-mimolette-light border-0">
                        <p class="fst-italic mb-3">"La livraison est vraiment rapide et les emballages sont éco-responsables. Top !"</p>
                        <h6 class="fw-bold mb-1">Sarah K.</h6>
                        <span class="badge bg-success">Avis vérifié</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

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