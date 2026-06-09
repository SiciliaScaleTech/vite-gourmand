<?php
session_start();
require_once '../backend/config.php';

// Sécurité : Si l'utilisateur n'est pas connecté, on le renvoie à la page de connexion
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = "";
$message_avis = "";

// 1. TRAITEMENT DU FORMULAIRE DE MISE À JOUR DU PROFIL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']); 
    $code_postal = trim($_POST['code_postal']);
    $ville = trim($_POST['ville']);

    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ?, email = ?, telephone = ?, adresse = ?, code_postal = ?, ville = ? WHERE id = ?");
        $stmt->execute([$nom, $prenom, $email, $telephone, $adresse, $code_postal, $ville, $user_id]);
        
        $_SESSION['user_prenom'] = $prenom;
        $message = "<div class='alert alert-success'>Informations mises à jour avec succès !</div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erreur : L'adresse email est déjà utilisée par un autre compte.</div>";
    }
}

// 2. TRAITEMENT DE L'ENVOI DE L'AVIS CLIENT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_avis'])) {
    $note = intval($_POST['note'] ?? 5);
    $commentaire = trim($_POST['commentaire'] ?? '');

    if (!empty($commentaire)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO avis (id_utilisateur, commentaire, note, statut, date_avis) 
                                   VALUES (?, ?, ?, 'en attente', NOW())");
            $stmt->execute([$user_id, $commentaire, $note]);
            $message_avis = "<div class='alert alert-success fw-bold'>✨ Merci ! Votre avis a été envoyé avec succès. Il sera publié après validation par notre équipe.</div>";
        } catch (PDOException $e) {
            $message_avis = "<div class='alert alert-danger'>Erreur lors de l'envoi de l'avis : " . $e->getMessage() . "</div>";
        }
    } else {
        $message_avis = "<div class='alert alert-warning'>⚠️ Veuillez rédiger un commentaire avant d'envoyer.</div>";
    }
}

// 3. RÉCUPÉRATION DES INFOS EN BDD POUR AFFICHAGE
$stmt = $pdo->prepare("SELECT nom, prenom, email, telephone, adresse, code_postal, ville, role FROM utilisateurs WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 mb-4">
                <div class="card-body p-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <div>
                            <h2 class="mb-0">Mon Profil</h2>
                            <p class="text-muted mb-0">Bienvenue, <?= htmlspecialchars($user['prenom']) ?> !</p>
                        </div>
                        <span class="badge bg-secondary px-3 py-2 text-uppercase rounded-pill fs-6 shadow-sm">
                            Statut : <?= htmlspecialchars($user['role']) ?>
                        </span>
                    </div>

                    <?= $message ?>

                    <!-- FORMULAIRE 1 : MODIFICATION DU PROFIL -->
                    <form method="POST" action="mon-profil.php">
    
                        <h4 class="mb-3 text-muted">Informations personnelles</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Prénom</label>
                                <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nom</label>
                                <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Adresse Email</label>
                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Numéro de téléphone</label>
                                <input type="tel" name="telephone" class="form-control" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>" placeholder="0612345678" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h4 class="mb-3 text-muted">Adresse de livraison</h4>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Adresse (Numéro et rue)</label>
                            <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($user['adresse'] ?? '') ?>" placeholder="12 rue de la Paix" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-4">
                                <label class="form-label fw-bold">Code Postal</label>
                                <input type="text" name="code_postal" class="form-control" value="<?= htmlspecialchars($user['code_postal'] ?? '') ?>" placeholder="75000" required>
                            </div>
                            <div class="col-md-8 mb-4">
                                <label class="form-label fw-bold">Ville</label>
                                <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($user['ville'] ?? '') ?>" placeholder="Paris" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="update_profile" class="btn btn-primary rounded-pill fw-bold">Enregistrer les modifications</button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- FORMULAIRE 2 : LAISSER UN AVIS (Complètement séparé pour éviter le conflit HTML) -->
            <div class="card shadow border-0 mt-4">
                <div class="card-body p-4">
                    <h4 class="mb-3 text-secondary">Laisser un avis sur notre service</h4>
                    
                    <?= $message_avis ?>
                    
                    <form action="mon-profil.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Votre note :</label>
                            <select name="note" class="form-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ (5/5 - Excellent)</option>
                                <option value="4">⭐⭐⭐⭐ (4/5 - Très bien)</option>
                                <option value="3">⭐⭐⭐ (3/5 - Moyen)</option>
                                <option value="2">⭐⭐ (2/5 - Médiocre)</option>
                                <option value="1">⭐ (1/5 - À fuir)</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Votre commentaire :</label>
                            <textarea name="commentaire" class="form-control" rows="4" placeholder="Partagez votre expérience avec notre équipe..." required></textarea>
                        </div>
                        
                        <button type="submit" name="submit_avis" class="btn btn-primary rounded-pill w-100 fw-bold">Envoyer mon avis pour validation</button>
                    </form>
                </div>
            </div>

            <!-- BOUTONS D'ACTIONS DE REDIRECTION -->
            <div class="mt-4 text-center">
                <?php if (in_array($user['role'] ?? '', ['employe', 'admin'])): ?>
                    <a href="employe/employe-dashboard.php" class="btn btn-dark rounded-pill fw-bold shadow-sm w-100 py-2">
                        Accéder au Tableau de Bord Professionnel
                    </a>
                <?php endif; ?>

                <?php if (!in_array($user['role'] ?? '', ['employe', 'admin'])): ?>
                    <hr class="my-3">
                    <a href="contact.php" class="btn btn-outline-dark rounded-pill">
                        Un problème ? Nous contacter directement
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>