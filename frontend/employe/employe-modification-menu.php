<?php
session_start();
require_once '../../backend/config.php';

// 1. SÉCURITÉ : Vérifier si l'utilisateur est connecté et est bien un employé
// (Adapte la clé de session selon ton système de connexion)
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['employe', 'admin'])) {
    header('Location: login.php');
    exit();
}

$message = "";
$messageClass = "";

// 2. RÉCUPÉRATION DU MENU ACTUEL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID du menu manquant.");
}

$menuId = intval($_GET['id']);

try {
    $query = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
    $query->execute([$menuId]);
    $menu = $query->fetch(PDO::FETCH_ASSOC);

    if (!$menu) {
        die("Ce menu n'existe pas.");
    }
} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}

// 3. TRAITEMENT DU FORMULAIRE LORS DE LA SOUMISSION (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et sécurisation des données saisies
    $prix = floatval($_POST['prix_pers']);
    $stock = intval($_POST['stock']);
    $description = trim($_POST['description']);
    $allergene = trim($_POST['allergene']);

    // Validation simple
    if ($prix <= 0 || $stock < 0 || empty($description)) {
        $message = "Veuillez remplir correctement tous les champs obligatoires (prix > 0 et stock >= 0).";
        $messageClass = "alert-danger";
    } else {
        try {
            // Requête de mise à jour (Mise à jour uniquement des champs demandés)
            $update = $pdo->prepare("UPDATE menu SET prix_pers = ?, stock = ?, description = ?, allergene = ? WHERE id = ?");
            $result = $update->execute([$prix, $stock, $description, $allergene, $menuId]);

            if ($result) {
                $message = "Le menu a été mis à jour avec succès !";
                $messageClass = "alert-success";
                
                // On rafraîchit les données locales pour l'affichage à jour dans le formulaire
                $menu['prix_pers'] = $prix;
                $menu['stock'] = $stock;
                $menu['description'] = $description;
                $menu['allergene'] = $allergene;
            } else {
                $message = "Une erreur est survenue lors de la mise à jour.";
                $messageClass = "alert-danger";
            }
        } catch (PDOException $e) {
            $message = "Erreur lors de l'enregistrement : " . $e->getMessage();
            $messageClass = "alert-danger";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            <div class="mb-4">
                <a href="employe-dashboard.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    ← Retour au tableau de bord
                </a>
            </div>

            <div class="card shadow border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-mimolette text-dark p-4">
                    <h2 class="h4 mb-1 fw-bold">Modifier le menu</h2>
                    <p class="mb-0 text-muted small">ID du menu : #<?= $menu['id'] ?> | Nom : <?= htmlspecialchars($menu['titre']) ?></p>
                </div>

                <div class="card-body p-4">
                    
                    <?php if (!empty($message)): ?>
                        <div class="alert <?= $messageClass ?> alert-dismissible fade show" role="alert">
                            <?= $message ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="employe-modification-menu.php?id=<?= $menuId ?>" class="row g-4">
                        
                        <div class="col-12">
                            <label class="form-label fw-bold text-secondary">Titre du menu</label>
                            <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($menu['titre']) ?>" disabled>
                        </div>

                        <div class="col-md-6">
                            <label for="prix_pers" class="form-label fw-bold">Prix par personne (€) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="0" name="prix_pers" id="prix_pers" class="form-control border-secondary-subtle shadow-sm" value="<?= htmlspecialchars($menu['prix_pers']) ?>" required>
                                <span class="input-group_text bg-light border-secondary-subtle">€</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="stock" class="form-label fw-bold">Quantité en stock <span class="text-danger">*</span></label>
                            <input type="number" min="0" name="stock" id="stock" class="form-control border-secondary-subtle shadow-sm" value="<?= htmlspecialchars($menu['stock']) ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="allergene" class="form-label fw-bold">Allergènes présents</label>
                            <input type="text" name="allergene" id="allergene" class="form-control border-secondary-subtle shadow-sm" placeholder="Ex: Gluten, Lactose, Fruits à coque (ou 'aucun')" value="<?= htmlspecialchars($menu['allergene']) ?>">
                            <div class="form-text">Séparez les allergènes par des virgules.</div>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label fw-bold">Description du menu <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="5" class="form-control border-secondary-subtle shadow-sm" required><?= htmlspecialchars($menu['description']) ?></textarea>
                        </div>

                        <div class="col-12 d-flex justify-content-end gap-2 mt-5">
                            <a href="employe-dashboard.php" class="btn btn-light rounded-pill px-4">Annuler</a>
                            <button type="submit" class="btn btn-cheddar rounded-pill px-5 fw-bold shadow-sm">
                                Enregistrer les modifications
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>