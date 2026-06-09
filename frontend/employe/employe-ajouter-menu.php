<?php
session_start();
require_once '../../backend/config.php';

// SÉCURITÉ : Accès réservé aux employés et admins
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['employe', 'admin'])) {
    header('Location: ../index.php');
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $nom_technique = trim($_POST['nom_technique']);
    $categorie = trim($_POST['categorie']);
    $description = trim($_POST['description']);
    $prix_pers = (float)$_POST['prix_pers'];
    $stock = (int)$_POST['stock'];
    $pers_min = (int)$_POST['pers_min'];
    $conditions = trim($_POST['conditions']);
    $allergene = trim($_POST['allergene']);

    // Composition des plats
    $entree  = trim($_POST['composition_entree']);
    $plat    = trim($_POST['composition_plat']);
    $dessert = trim($_POST['composition_dessert']);
    $plats_ordonnes = "Entrée: " . $entree . "|plat: " . $plat . "|Dessert: " . $dessert;

    // GESTION DE L'UPLOAD DE L'IMAGE (Synchronisé avec le name="galerie")
    $image_path = "assets/images/pizza-placeholder.jpg"; 

    if (isset($_FILES['galerie']) && $_FILES['galerie']['error'] === 0) {
        
        $dossier_destination = "../assets/images/";
        
        // Sécurité : on crée le dossier s'il n'existe pas sur le serveur local ou en ligne
        if (!is_dir($dossier_destination)) {
            mkdir($dossier_destination, 0777, true);
        }
        
        $extension = pathinfo($_FILES['galerie']['name'], PATHINFO_EXTENSION);
        $nom_fichier_unique = uniqid("menu_", true) . "." . $extension;
        
        $chemin_physique_final = $dossier_destination . $nom_fichier_unique;

        $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp'];
        $taille_max = 2 * 1024 * 1024; // 2 Mo

        if (!in_array(strtolower($extension), $extensions_autorisees)) {
            $message = "<div class='alert alert-danger fw-bold'>⚠️ Format d'image refusé. Utilisez du JPG, PNG ou WEBP.</div>";
        } elseif ($_FILES['galerie']['size'] > $taille_max) {
            $message = "<div class='alert alert-danger fw-bold'>⚠️ L'image est trop lourde (Maximum 2 Mo).</div>";
        } else {
            if (move_uploaded_file($_FILES['galerie']['tmp_name'], $chemin_physique_final)) {
                $image_path = "assets/images/" . $nom_fichier_unique;
            } else {
                $message = "<div class='alert alert-warning fw-bold'>⚠️ Échec du transfert physique de l'image.</div>";
            }
        }
    }

    // Si aucune erreur de sécurité, on insère en BDD
    if (empty($message) && !empty($titre) && !empty($categorie)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO menu (titre, nom_technique, categorie, description, plats, stock, prix_pers, pers_min, conditions, allergene, galerie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $titre, $nom_technique, $categorie, $description, $plats_ordonnes, $stock, $prix_pers, $pers_min, $conditions, $allergene, $image_path
            ]);

            // Redirection de sécurité anti-doublon avec message flash en session
            $_SESSION['flash_success'] = "✨ Le menu « $titre » a été créé avec succès !";
            header('Location: employe-carte.php');
            exit();

        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger fw-bold'>⚠️ Erreur BDD : " . $e->getMessage() . "</div>";
        }
    }
}

include '../includes/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="d-flex align-items-center mb-4">
                <a href="employe-carte.php" class="btn btn-outline-secondary rounded-pill btn-sm me-3">⬅️ Annuler</a>
                <h2 class="mb-0 fw-bold">Ajouter un nouveau menu</h2>
            </div>

            <?= $message ?>

            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <form action="employe-ajouter-menu.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom / Titre du menu *</label>
                                <input type="text" name="titre" class="form-control rounded-3" placeholder="Ex: Menu Gourmet" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nom technique (Thème pour le filtre)</label>
                                <input type="text" name="nom_technique" class="form-control rounded-3" placeholder="Ex: Classique, Noel, Halloween..." required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Catégorie *</label>
                                <input type="text" name="categorie" class="form-control rounded-3" value="menu" placeholder="Ex: menu" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Image du menu / Plat *</label>
                                <input type="file" name="galerie" class="form-control rounded-3" accept="image/*" required>
                                <small class="text-muted">Formats acceptés : JPG, PNG, WEBP (Max 2 Mo)</small>
                            </div>

                            <hr class="my-3 text-muted">
                            <h5 class="text-primary fw-bold mb-2"> Composition du Menu</h5>
                            
                            <div class="col-12">
                                <label class="form-label fw-semibold">Entrée</label>
                                <input type="text" name="composition_entree" class="form-control rounded-3" placeholder="Ex: Saumons sur toast" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Plat principal</label>
                                <input type="text" name="composition_plat" class="form-control rounded-3" placeholder="Ex: Velouté de tomate" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">Dessert</label>
                                <input type="text" name="composition_dessert" class="form-control rounded-3" placeholder="Ex: Cupcake" required>
                            </div>

                            <hr class="my-3 text-muted">

                            <div class="col-12">
                                <label class="form-label fw-bold">Description du menu</label>
                                <textarea name="description" class="form-control rounded-3" rows="3" placeholder="Ex: Un jour important pour vous..." required></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Prix (€ / pers) *</label>
                                <input type="number" step="0.01" name="prix_pers" class="form-control rounded-3" placeholder="30" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Pers. minimum *</label>
                                <input type="number" name="pers_min" class="form-control rounded-3" placeholder="15" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-bold">Stock *</label>
                                <input type="number" name="stock" class="form-control rounded-3" placeholder="10" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Conditions de commande</label>
                                <input type="text" name="conditions" class="form-control rounded-3" placeholder="Ex: Commande possible jusqu'à 3 semaines avant." required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">Allergènes</label>
                                <input type="text" name="allergene" class="form-control rounded-3" placeholder="Ex: oeufs, saumon ou neant" required>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold px-5 shadow-sm">Enregistrer le menu</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>