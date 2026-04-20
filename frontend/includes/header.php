<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calcul du panier pour le badge 🛒
$total_articles = 0;
if (isset($_SESSION['panier']) && !empty($_SESSION['panier'])) {
    $total_articles = array_sum($_SESSION['panier']);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand | Nos menus</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/css/style.css">
    <link rel="stylesheet" href="../styles/css/nos-menus.css">
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
                        <li class="nav-item"><a class="nav-link" href="../index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="../nos-menus.php">Menus</a></li>
                        <li class="nav-item"><a class="nav-link" href="../contact.php">Contact</a></li>
                    </ul>
                </div>

                <div class="d-flex align-items-center">
                    <a href="panier.php" class="cart-icon me-3 position-relative">🛒

                        <?php if ($total_articles > 0): ?>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small">
                                <?= $total_articles ?>
                            </span>
                        <?php endif; ?>
                        
                    </a>
                    <a href="connexion.php" class="btn btn-dark rounded-pill px-4">👤</a>
                </div>
            </div>
        </nav>
    </header>