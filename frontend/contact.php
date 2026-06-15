
<?php include 'includes/header.php'; ?>

       <section class="hero-banner text-center py-5">
    <div class="container my-auto">
        <h1 class="display-5 display-md-3 fw-bold mb-3">Vite & Gourmand</h1>
        
        <p class="fs-5 fs-md-4 mb-4">Nous sommes disponibles pour toutes questions</p>
        
        <a href="nos-menus.php" class="btn btn-cheddar btn-lg px-4 px-md-5 py-2 py-md-3 rounded-pill fw-bold w-100 w-sm-auto">
            Commander maintenant
        </a>
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
                            <p class="fw-bold mb-0">tourysicili@gmail.com</p>
                        </div>
                    </div>
                </div>
            </div>

            

            <div class="col-lg-8">
            <?php if (isset($_GET['success'])): ?>
                <div class="card border-0 shadow-sm p-5 rounded-4 text-center bg-light">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h2 class="fw-bold text-dark">Message envoyé !</h2>
                    <p class="text-muted fs-5">Merci Julie et José ont bien reçu votre message. Ils vous répondront très vite.</p>
                    <div class="mt-4">
                        <a href="../frontend/index.php" class="btn btn-cheddar rounded-pill px-4 fw-bold">Retour à l'accueil</a>
                    </div>
                </div>

            <?php else: ?>
                <div class="card border-0 shadow-sm p-4 rounded-4">
                    <form action="../backend/traiter-contact.php" method="POST" novalidate>
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
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
