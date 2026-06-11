<?php
session_start();
require_once '../../backend/config.php'; 

// 1. SÉCURITÉ  Seul l'admin passe
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../connexion.php');
    exit();
}

$message = "";
$messageClass = "";

// 2. ACTION : AJOUTER UN EMPLOYÉ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['creer_employe'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $nom = trim($_POST['nom'] ?? 'Nom'); 
    $prenom = trim($_POST['prenom'] ?? 'Employé');

    if (empty($email) || empty($password)) {
        $message = "L'email et le mot de passe sont obligatoires.";
        $messageClass = "alert-danger";
    } else {
        try {
            $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $check->execute([$email]);
            
            if ($check->rowCount() > 0) {
                $message = "Cet email est déjà utilisé par un autre compte.";
                $messageClass = "alert-danger";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, actif) VALUES (?, ?, ?, ?, 'employe', 1)");
                $stmt->execute([$nom, $prenom, $email, $passwordHash]);

                $message = "🎉 Compte employé créé avec succès !";
                $messageClass = "alert-success";
            }
        } catch (PDOException $e) {
            $message = "Erreur lors de la création : " . $e->getMessage();
            $messageClass = "alert-danger";
        }
    }
}

// 3. ACTION : ACTIVER / DÉSACTIVER UN COMPTE
if (isset($_GET['action']) && isset($_GET['id_user'])) {
    $id_user = (int)$_GET['id_user'];
    $action = $_GET['action'];
    
    if ($id_user === (int)$_SESSION['user_id']) {
        header('Location: admin-dashboard.php?msg=self_error');
        exit();
    }

    $nouvel_etat = ($action === 'desactiver') ? 0 : 1;
    
    try {
        $stmt = $pdo->prepare("UPDATE utilisateurs SET actif = ? WHERE id = ? AND role = 'employe'");
        $stmt->execute([$nouvel_etat, $id_user]);
        header('Location: admin-dashboard.php?msg=status_updated');
        exit();
    } catch (PDOException $e) {
        die("Erreur de mise à jour : " . $e->getMessage());
    }
}

// 4. RÉCUPÉRATION DE LA LISTE DES EMPLOYÉS
try {
    $stmt = $pdo->query("SELECT id, nom, prenom, email, actif FROM utilisateurs WHERE role = 'employe' ORDER BY nom ASC");
    $employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}

// 5. CHARGEMENT INITIAL DES DONNÉES DEPUIS MONGODB (Pour le premier affichage)
$chiffre_affaires = 0;
$labels_graphique = [];
$donnees_graphique = [];

$date_debut = $_GET['date_debut'] ?? '';
$date_fin = $_GET['date_fin'] ?? '';

try {
    $query = new MongoDB\Driver\Query([]);
    $cursor = $managerMongoDB->executeQuery($collectionMenus, $query);

    foreach ($cursor as $menu) {
        $nom_menu = $menu->titre;
        $prix_unitaire = (float)($menu->prix_pers ?? 0);
        $ventes_menu_periode = 0;

        if (isset($menu->stats->dernieres_commandes)) {
            foreach ($menu->stats->dernieres_commandes as $commande) {
                $date_commande = date('Y-m-d', strtotime($commande->date));
                $quantite = $commande->quantite ?? 1;

                if (!empty($date_debut) && $date_commande < $date_debut) continue;
                if (!empty($date_fin) && $date_commande > $date_fin) continue;

                $ventes_menu_periode += $quantite;
                $chiffre_affaires += ($prix_unitaire * $quantite);
            }
        }

        if ($ventes_menu_periode > 0) {
            $labels_graphique[] = $nom_menu;
            $donnees_graphique[] = $ventes_menu_periode;
        }
    }
} catch (MongoDB\Driver\Exception\Exception $e) {
    echo "<div class='alert alert-danger'>Erreur Atlas : " . htmlspecialchars($e->getMessage()) . "</div>";
}

$prefix = "../";
include '../includes/header.php';
?>

<main class="container py-5">
    <!-- En-tête -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <h1 class="fw-bold mb-1">Espace Administrateur</h1>
            <p class="text-muted mb-0">Gestion du personnel et analyse de l'activité</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="../employe/employe-dashboard.php" class="btn btn-outline-dark rounded-pill px-3 fw-bold">
                Gérer les Commandes (Vue Employé)
            </a>
            <a href="#statsSection" class="btn btn-primary border-0 rounded-pill px-3 fw-bold shadow-sm">
                Voir les Graphiques & CA
            </a>
        </div>
    </div>

    <!-- Alertes -->
    <?php if (!empty($message)): ?>
        <div class="alert <?= $messageClass ?> alert-dismissible fade show fw-bold mb-4" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'status_updated'): ?>
        <div class="alert alert-success alert-dismissible fade show fw-bold mb-4" role="alert">
            Le statut du compte employé a bien été modifié.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Formulaire création -->
        <div class="col-lg-5">
            <div class="card shadow border-0 rounded-4 p-4">
                <h4 class="fw-bold text-dark mb-3">Créer un compte Employé</h4>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Prénom</label>
                        <input type="text" name="prenom" class="form-control" placeholder="Ex: Jean" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom</label>
                        <input type="text" name="nom" class="form-control" placeholder="Ex: Dupont" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Adresse Email</label>
                        <input type="email" name="email" class="form-control" placeholder="employe@viteetgourmand.fr" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mot de passe initial</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" name="creer_employe" class="btn btn-dark w-100 rounded-pill fw-bold py-2 mt-2">
                        Créer le compte & notifier
                    </button>
                </form>
            </div>
        </div>

        <!-- Tableau employés -->
        <div class="col-lg-7">
            <div class="card shadow border-0 rounded-4 overflow-hidden h-100">
                <div class="card-header bg-dark text-white p-3">
                    <h5 class="mb-0 fw-bold py-1">Comptes Employés existants</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="p-3">Employé</th>
                                    <th>Email</th>
                                    <th class="text-center">Statut</th>
                                    <th class="text-center p-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($employes)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">Aucun employé enregistré.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($employes as $emp): ?>
                                        <tr>
                                            <td class="p-3 fw-bold"><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?></td>
                                            <td><?= htmlspecialchars($emp['email']) ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-<?= $emp['actif'] == 1 ? 'success' : 'danger' ?>">
                                                    <?= $emp['actif'] == 1 ? 'Actif' : 'Désactivé' ?>
                                                </span>
                                            </td>
                                            <td class="text-center p-3">
                                                <a href="admin-dashboard.php?action=<?= $emp['actif'] == 1 ? 'desactiver' : 'activer' ?>&id_user=<?= $emp['id'] ?>" 
                                                   class="btn btn-sm btn-outline-<?= $emp['actif'] == 1 ? 'danger' : 'success' ?> rounded-pill px-3"
                                                   onclick="return confirm('Confirmer l\'action sur ce compte ?');">
                                                    <?= $emp['actif'] == 1 ? 'Bloquer' : 'Réactiver' ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STATISTIQUES -->
    <div id="statsSection" class="mt-5 pt-4">
        <hr class="my-5">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1">Analyse des Ventes & Chiffre d'Affaires</h3>
            </div>
            
            <!-- AJOUT DE ID "affichage-ca" POUR LE JAVASCRIPT -->
            <div class="bg-success text-white px-4 py-3 rounded-4 shadow-sm text-center">
                <span class="text-uppercase small fw-bold d-block opacity-75">Chiffre d'Affaires</span>
                <span class="fs-3 fw-bold" id="affichage-ca"><?= number_format($chiffre_affaires, 2, ',', ' ') ?> €</span>
            </div>
        </div>

        <!-- AJOUT DE ID "formFiltre", "date_debut" ET "date_fin" POUR CAPTURER L'ÉVÉNEMENT JS -->
        <div class="card shadow border-0 rounded-4 p-4 mb-4 bg-light">
            <form method="GET" action="admin-dashboard.php#statsSection" id="formFiltre" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= htmlspecialchars($date_debut) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold small text-muted">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= htmlspecialchars($date_fin) ?>">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-pill fw-bold py-2">Filtrer</button>
                    <!--  CORRECTION DE $date_end PAR $date_fin -->
                    <?php if(!empty($date_debut) || !empty($date_fin)): ?>
                        <a href="admin-dashboard.php#statsSection" class="btn btn-outline-secondary rounded-pill fw-bold py-2">Réinitialiser</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-10 col-md-8 mx-auto">
                <div class="card shadow border-0 rounded-4 p-4 text-center">
                    <h5 class="fw-bold text-dark mb-4">Volume des ventes par menu</h5>
                    <div style="position: relative; height:300px; width:100%">
                        <canvas id="chartMenus"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!--  TRANSMISSION DES VARIABLES PHP INITIALES VERS JS -->
<script>
    const labelsMenus = <?= json_encode($labels_graphique) ?>;
    const donneesVentes = <?= json_encode($donnees_graphique) ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../styles/script/admin_dashboard.js"></script>

<?php include '../includes/footer.php'; ?>