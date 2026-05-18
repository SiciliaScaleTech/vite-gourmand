<?php 
include 'includes/header.php'; 
$id_commande = $_GET['id'] ?? 'inconnue';
?>

<main class="container py-5 text-center">
    <div class="card shadow border-0 p-5">
        <h1 class="display-4 text-success">Merci pour votre commande ! 🎉</h1>
        <p class="lead mt-3">Votre commande n°<strong><?= htmlspecialchars($id_commande) ?></strong> a bien été enregistrée.</p>
        <p>Notre équipe prépare vos délicieux menus. Vous recevrez un mail dès qu'ils seront prêts.</p>
        <div class="mt-4">
            <a href="index.php" class="btn btn-dark rounded-pill px-4">Retour à l'accueil</a>
            <a href="mes-commandes.php" class="btn btn-outline-dark rounded-pill px-4">Voir mes commandes</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>