<?php
include 'includes/header.php';
require_once '../backend/config.php';

// Sécurité : Rediriger si pas connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: connexion.php");
    exit();
}

// Récupérer les infos fraîches de l'utilisateur
$stmt = $pdo->prepare("SELECT nom, prenom, adresse, code_postal,ville, email, date_inscription FROM utilisateurs WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 rounded-4 p-4">
                <h2 class="text-center mb-4">Mon Profil 👤</h2>
                <hr>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Prénom :</label>
                    <p class="fs-5"><?= htmlspecialchars($user['prenom']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Nom :</label>
                    <p class="fs-5"><?= htmlspecialchars($user['nom']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Adresse :</label>
                    <p class="fs-5"><?= htmlspecialchars($user['adresse']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Code postal :</label>
                    <p class="fs-5"><?= htmlspecialchars($user['code_postal']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Ville :</label>
                    <p class="fs-5"><?= htmlspecialchars($user['ville']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Email :</label>
                    <p class="fs-5"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold text-muted">Membre depuis le :</label>
                    <p class="fs-6 text-secondary"><?= date('d/m/Y', strtotime($user['date_inscription'])) ?></p>
                </div>
                <div class="text-center mt-4">
                    <a href="deconnexion.php" class="btn btn-danger rounded-pill px-4">Déconnexion</a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>