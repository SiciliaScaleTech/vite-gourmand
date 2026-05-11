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

        <section class="hero-banner text-center">
            <div class="container">
                <h1 class="display-3 fw-bold">Vite & Gourmand</h1>
                <p class="fs-4">nous sommes disponibles pour toutes questions</p>
                <a href="#menus" class="btn btn-cheddar btn-lg px-5 py-3 rounded-pill fw-bold">Commander maintenant</a>
            </div>
        </section>

    <main class="container py-5">
        <div class="row g-5">
            <div class="col-lg-4">
                <h2 class="fw-bold text-cheddar mb-4">Contactez-nous</h2>
                <p class="text-muted">Une question sur un menu ? Un événement spécial ? Julie et José vous répondent avec le sourire.</p>
                
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-mimolette p-3 rounded-circle me-3">
                            <i class="bi bi-telephone-fill text-dark"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-muted">Téléphone</p>
                            <p class="fw-bold mb-0">06.69.25.58.47 (José)</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-mimolette p-3 rounded-circle me-3">
                            <i class="bi bi-envelope-fill text-dark"></i>
                        </div>
                        <div>
                            <p class="mb-0 small text-muted">Email</p>
                            <p class="fw-bold mb-0">julie@vite-gourmand.fr</p>
                        </div>
                    </div>
                </div>
            </div>

            

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <form action="contact_process.php" method="POST" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom</label>
                                <input type="text" name="nom" class="form-control rounded-pill" placeholder="Votre nom" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Prénom</label>
                                <input type="text" name="prenom" class="form-control rounded-pill" placeholder="Votre prénom" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Email</label>
                                <input type="email" name="email" class="form-control rounded-pill" placeholder="nom@exemple.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Sujet</label>
                                <select name="sujet" class="form-select rounded-pill">
                                    <option value="devis">Demande de devis</option>
                                    <option value="question">Question sur un menu</option>
                                    <option value="autre">Autre demande</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Votre message</label>
                                <textarea name="message" class="form-control rounded-4" rows="5" placeholder="Comment pouvons-nous vous aider ?" required></textarea>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-cheddar px-5 py-2 rounded-pill fw-bold shadow-sm">
                                    Envoyer le message <i class="bi bi-send ms-2"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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
  
    <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] == 'success'): ?>
                    <div class="alert alert-success rounded-pill border-0 shadow-sm mb-4">
                        <i class="bi bi-check-circle-fill me-2"></i> Merci ! Votre message a bien été envoyé à Julie et José.
                    </div>
                <?php elseif ($_GET['status'] == 'error'): ?>
                    <div class="alert alert-danger rounded-pill border-0 shadow-sm mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Oups ! Veuillez remplir tous les champs correctement.
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            
    <script src="styles/script/nos-menus.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>