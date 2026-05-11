<?php 
include 'includes/header.php'; 
require_once '../backend/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $adresse = $_POST['adresse'];
    $code_postal = $_POST['code_postal'];
    $ville = $_POST['ville'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 

    try {
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, adresse, code_postal, ville, email, mot_de_passe) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $adresse, $code_postal, $ville, $email, $password]);
        $message = "<div class='alert alert-success'>Compte créé ! <a href='connexion.php'>Connectez-vous ici</a></div>";
    } catch (PDOException $e) {
        $message = "<div class='alert alert-danger'>Erreur : L'email existe peut-être déjà.</div>";
    }
}
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4">Créer un compte</h2>
                    <?= $message ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label>Prénom</label>
                            <input type="text" name="prenom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Nom</label>
                            <input type="text" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adresse complète</label>
                            <input type="text" name="adresse" class="form-control" placeholder="123 rue des Gourmets" required>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Code Postal</label>
                                <input type="text" name="code_postal" class="form-control" placeholder="75000" required>
                            </div>
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Ville</label>
                                <input type="text" name="ville" class="form-control" placeholder="Paris" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Mot de passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-dark w-100 rounded-pill">S'inscrire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>