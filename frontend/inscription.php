<?php 
include 'includes/header.php'; 
require_once '../backend/config.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $adresse = trim($_POST['adresse']);
    $code_postal = trim($_POST['code_postal']);
    $ville = trim($_POST['ville']);
    $password_brut = $_POST['password'];

    //  MOT DE PASSE (10 car. min, 1 maj, 1 min, 1 chiffre, 1 car. spécial)
    $regex_password = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&_#])[A-Za-z\d@$!%*?&_#]{10,}$/';

    if (!preg_match($regex_password, $password_brut)) {
        $message = "<div class='alert alert-danger'>Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial (@$!%*?&_#).</div>";
    } else {
        $password_hashed = password_hash($password_brut, PASSWORD_DEFAULT);

        try {
            // Requête SQL complète avec TOUTES les informations d'adresse
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, telephone, adresse, code_postal, ville, mot_de_passe, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'utilisateur')");
            $stmt->execute([$nom, $prenom, $email, $telephone, $adresse, $code_postal, $ville, $password_hashed]);
            
            $message = "<div class='alert alert-success'>Compte créé avec succès ! <a href='connexion.php' class='fw-bold text-success'>Connectez-vous ici</a></div>";
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur : L'email existe déjà.</div>";
        }
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
                        
                        <h5 class="text-muted mb-3">Identité</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prénom</label>
                                <input type="text" name="prenom" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom</label>
                                <input type="text" name="nom" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" name="telephone" class="form-control" placeholder="0612345678" required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h5 class="text-muted mb-3">Coordonnées de livraison</h5>

                        <div class="mb-3">
                            <label class="form-label">Adresse (Numéro et rue)</label>
                            <input type="text" name="adresse" class="form-control" placeholder="12 rue de la Paix" required>
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

                        <hr class="my-4">
                        <h5 class="text-muted mb-3">Sécurité</h5>

                        <div class="mb-3">
                            <label class="form-label">Mot de passe</label>
                            <input type="password" name="password" class="form-control" placeholder="10 caractères min. requis" required>
                            <small class="text-muted d-block mt-1">Doit contenir : 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial (@$!%*?&_#).</small>
                        </div>

                        <button type="submit" class="btn btn-dark w-100 rounded-pill mt-3">S'inscrire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>