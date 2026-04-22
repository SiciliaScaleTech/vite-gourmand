<?php 

require_once '../backend/config.php';

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        // Connexion réussie ! On stocke les infos en session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nom'] = $user['prenom'];
        
        header("Location: index.php"); // Redirection vers l'accueil
        exit();
    } else {
        $erreur = "<div class='alert alert-danger'>Email ou mot de passe incorrect.</div>";
    }
}

include 'includes/header.php'; 
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow border-0">
                <div class="card-body p-5 text-center">
                    <h2 class="mb-4">Connexion</h2>
                    <?= $erreur ?>
                    <form method="POST">
                        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
                        <input type="password" name="password" class="form-control mb-4" placeholder="Mot de passe" required>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill mb-3">Se connecter</button>
                    </form>
                    <p class="small text-muted">Pas encore de compte ? <a href="inscription.php">Inscrivez-vous</a></p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>