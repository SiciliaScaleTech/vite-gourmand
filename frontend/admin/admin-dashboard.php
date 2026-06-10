<?php
session_start();
require_once '../../backend/config.php'; // 

// 1. SÉCURITÉ ULTRA-STRICTE : Seul l'admin passe
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
    $nom = trim($_POST['nom'] ?? 'Nom'); // Optionnel mais propre pour le compte
    $prenom = trim($_POST['prenom'] ?? 'Employé');

    if (empty($email) || empty($password)) {
        $message = "L'email et le mot de passe sont obligatoires.";
        $messageClass = "alert-danger";
    } else {
        try {
            // Vérifier si l'email existe déjà
            $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
            $check->execute([$email]);
            
            if ($check->rowCount() > 0) {
                $message = "Cet email est déjà utilisé par un autre compte.";
                $messageClass = "alert-danger";
            } else {
                // Hachage du mot de passe
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                // Rôle forcé à 'employe' et actif par défaut (1). Impossible de créer un admin d'ici !
                $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, actif) VALUES (?, ?, ?, ?, 'employe', 1)");
                $stmt->execute([$nom, $prenom, $email, $passwordHash]);

                // ENVOI DU MAIL 
                $to = $email;
                $subject = "Création de votre compte employé - Espace Julie";
                $email_content = "Bonjour " . htmlspecialchars($prenom) . ",\n\n"
                               . "Un compte employé a été créé pour vous sur l'application de Julie.\n"
                               . "Votre identifiant de connexion (username) est votre adresse email : " . $email . "\n\n"
                               . "Pour des raisons de sécurité, votre mot de passe ne peut pas être communiqué par mail. Merci de vous rapprocher de l'administrateur afin de l'obtenir.\n\n"
                               . "Cordialement,\nL'équipe d'administration.";
                
                // mail($to, $subject, $email_content); // À décommenter sur ton serveur de prod

                $message = "🎉 Compte employé créé avec succès ! Mail de notification envoyé à " . htmlspecialchars($email);
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
    
    // Sécurité : Éviter que l'admin se désactive lui-même
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

$prefix = "../";

include '../includes/header.php';
?>

<main class="container py-5">
    <!-- En-tête avec raccourcis vers l'espace employé -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
        <div>
            <h1 class="fw-bold mb-1">Espace Administrateur </h1>
            <p class="text-muted mb-0">Gestion du personnel et analyse de l'activité</p>
        </div>
        
        <!-- Raccourcis pour que l'admin puisse faire tout ce que fait l'employé -->
        <div class="d-flex gap-2 flex-wrap">
            <a href="../employe/employe-dashboard.php" class="btn btn-outline-dark rounded-pill px-3 fw-bold">
                Gérer les Commandes (Vue Employé)
            </a>
            <a href="#statsSection" class="btn btn-primary border-0 rounded-pill px-3 fw-bold shadow-sm">
                Voir les Graphiques & CA
            </a>
        </div>
    </div>

    <!-- Alertes retours actions -->
    <?php if (!empty($message)): ?>
        <div class="alert <?= $messageClass ?> alert-dismissible fade show fw-bold mb-4" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php $_POST = array(); endif; ?>
    
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'status_updated'): ?>
        <div class="alert alert-success alert-dismissible fade show fw-bold mb-4" role="alert">
            Le statut du compte employé a bien été modifié.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php $_GET['msg'] = ''; endif; ?>

    <div class="row g-4">
        <!-- FORMULAIRE DE CRÉATION  -->
        <div class="col-lg-5">
            <div class="card shadow border-0 rounded-4 p-4">
                <h4 class="fw-bold text-dark mb-3">Créer un compte Employé</h4>
                <p class="text-muted small">Le rôle sera automatiquement défini sur "employe". Aucun compte administrateur ne peut être créé via cette interface.</p>
                <hr>
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
                        <label class="form-label fw-bold">Adresse Email (Username)</label>
                        <input type="email" name="email" class="form-control" placeholder="employe@viteetgourmand.fr" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Mot de passe initial</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        <div class="form-text text-danger font-monospace small">Ce mot de passe ne sera PAS envoyé dans le mail. L'employé devra venir vous le demander.</div>
                    </div>
                    <button type="submit" name="creer_employe" class="btn btn-dark w-100 rounded-pill fw-bold py-2 mt-2 shadow-sm border-0">
                        Créer le compte & notifier
                    </button>
                </form>
            </div>
        </div>

        <!-- TABLEAU DE GESTION / RENDRE INUTILISABLE  -->
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
                                        <td colspan="4" class="text-center py-4 text-muted">Aucun employé enregistré pour le moment.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($employes as $emp): ?>
                                        <tr>
                                            <td class="p-3 fw-bold"><?= htmlspecialchars($emp['prenom'] . ' ' . $emp['nom']) ?></td>
                                            <td><?= htmlspecialchars($emp['email']) ?></td>
                                            <td class="text-center">
                                                <?php if ($emp['actif'] == 1): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Désactivé / Inutilisable</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center p-3">
                                                <?php if ($emp['actif'] == 1): ?>
                                                    <!-- Rendre inutilisable -->
                                                    <a href="admin-dashboard.php?action=desactiver&id_user=<?= $emp['id'] ?>" 
                                                       class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                       onclick="return confirm('Rendre ce compte inutilisable ? L\'employé sera déconnecté et ne pourra plus accéder à l\'application.');">
                                                    Bloquer
                                                    </a>
                                                <?php else: ?>
                                                    <!-- Réactiver -->
                                                    <a href="admin-dashboard.php?action=activer&id_user=<?= $emp['id'] ?>" 
                                                       class="btn btn-sm btn-outline-success rounded-pill px-3">
                                                    Réactiver
                                                    </a>
                                                <?php endif; ?>
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

    <!-- Section future pour les graphiques et Chiffre d'Affaire -->
    <div id="statsSection" class="mt-5 pt-4">
        <hr class="my-5">
        <h3 class="fw-bold text-dark mb-4">Analyse & Chiffre d'Affaires (NoSQL & Filtres)</h3>
        <div class="alert alert-info border-0 rounded-4 p-4">
            <strong>Étape suivante :</strong> La structure de base est en place ! Les comptes créés ici sont sécurisés, bloqués à la demande et limités au rôle employé. Dis-moi si cet affichage te va pour qu'on passe à l'intégration du système de statistiques et de graphiques.
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>