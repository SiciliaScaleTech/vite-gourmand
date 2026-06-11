<?php
session_start();
require_once '../../backend/config.php';

// 1. SÉCURITÉ STRICTE
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['employe', 'admin'])) {
    header('Location: ../connexion.php');
    exit();
}

$commande_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
if (!$commande_id) {
    header('Location: employe-dashboard.php');
    exit();
}

$message = "";
$messageClass = "";

// 2. TRAITEMENT DU FORMULAIRE (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $nouveau_statut = $_POST['statut'];
    $mode_contact = trim($_POST['mode_contact'] ?? '');
    $motif_annulation = trim($_POST['motif_annulation'] ?? '');

    if ($nouveau_statut === 'annulée' && (empty($mode_contact) || empty($motif_annulation))) {
        $message = "⚠️ Erreur : Le mode de contact et le motif sont obligatoires pour une annulation.";
        $messageClass = "alert-danger";
    } else {
        try {
            // Récupération de l'ancien statut avant modification
            $checkOld = $pdo->prepare("SELECT statut FROM commandes WHERE id = ?");
            $checkOld->execute([$commande_id]);
            $old_status = $checkOld->fetchColumn();

            $pdo->beginTransaction();

            if ($nouveau_statut === 'annulée') {
                $stmt = $pdo->prepare("UPDATE commandes SET statut = ?, mode_contact = ?, motif_annulation = ? WHERE id = ?");
                $stmt->execute([$nouveau_statut, $mode_contact, $motif_annulation, $commande_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE commandes SET statut = ?, mode_contact = NULL, motif_annulation = NULL WHERE id = ?");
                $stmt->execute([$nouveau_statut, $commande_id]);

                // Gestion des stocks lors du passage à "accepté"
                if ($nouveau_statut === 'accepté' && $old_status !== 'accepté') {
                    $itemsStmt = $pdo->prepare("SELECT id_menu, quantite FROM details_commandes WHERE id_commande = ?");
                    $itemsStmt->execute([$commande_id]);
                    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

                    $updateStockStmt = $pdo->prepare("UPDATE menu SET stock = stock - ? WHERE id = ?");
                    foreach ($items as $item) {
                        $updateStockStmt->execute([$item['quantite'], $item['id_menu']]);
                    }
                }
            }

            // DÉCLENCHEMENT DU MAIL : En attente du retour de matériel
            if ($nouveau_statut === 'en attente du retour de matériel' && $old_status !== 'en attente du retour de matériel') {
                // On récupère les infos du client pour simuler l'envoi
                $clientStmt = $pdo->prepare("SELECT u.email, u.prenom, u.nom FROM commandes c JOIN utilisateurs u ON c.id_utilisateur = u.id WHERE c.id = ?");
                $clientStmt->execute([$commande_id]);
                $client = $clientStmt->fetch(PDO::FETCH_ASSOC);

                if ($client) {
                    $to = $client['email'];
                    $subject = "Restitution de matériel - Commande #" . $commande_id;
                    $email_content = "Bonjour " . htmlspecialchars($client['prenom']) . ",\n\n"
                                   . "Votre commande est désormais 'En attente du retour de matériel'.\n"
                                   . "Conformément à nos conditions générales de vente, si sous 10 jours ouvrés le matériel n'est pas restitué, vous devrez vous acquitter de 600 euros de frais.\n\n"
                                   . "Pour rendre le matériel, merci de prendre contact avec notre société au plus vite.\n\n"
                                   . "Cordialement,\nL'équipe de Julie";
                    
                    $message .= " Envoi du mail de relance matériel simulé avec succès à " . htmlspecialchars($to) . ".";
                }
            }

            $pdo->commit();
            $message = "Le statut de la commande a été mis à jour avec succès !" . $message;
            $messageClass = "alert-success";
        } catch (PDOException $e) {
            $pdo->rollBack();
            $message = "Erreur BDD : " . $e->getMessage();
            $messageClass = "alert-danger";
        }
    }
}

// 3. CHARGEMENT DES DONNÉES POUR L'AFFICHAGE
try {
    $stmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, u.email, u.telephone 
                           FROM commandes c 
                           JOIN utilisateurs u ON c.id_utilisateur = u.id 
                           WHERE c.id = ?");
    $stmt->execute([$commande_id]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$commande) {
        die("<div class='container py-5'><div class='alert alert-danger'>Commande introuvable.</div></div>");
    }

    // Récupération des détails avec le STOCK restant du menu
    $detailsStmt = $pdo->prepare("SELECT dc.*, m.titre, m.stock AS stock_restant 
                                  FROM details_commandes dc 
                                  JOIN menu m ON dc.id_menu = m.id 
                                  WHERE dc.id_commande = ?");
    $detailsStmt->execute([$commande_id]);
    $liste_plats = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur BDD : " . $e->getMessage());
}

include '../includes/header.php';
?>

<main class="container py-5">
    <a href="employe-dashboard.php" class="btn btn-outline-secondary rounded-pill mb-4">⬅️ Retour au Tableau de Bord</a>
    
    <?php if (!empty($message)): ?>
        <div class="alert <?= $messageClass ?> alert-dismissible fade show fw-bold" role="alert">
            <?= $message ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-dark text-white p-4 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-0 fw-bold">Détails de la Commande #<?= $commande['id'] ?></h3>
                <small class="text-light-50">Client : <?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?></small>
            </div>
            <span class="badge bg-warning text-dark text-uppercase px-3 py-2 fw-bold"><?= htmlspecialchars($commande['statut']) ?></span>
        </div>
        
        <div class="card-body p-4">
            <h5 class="fw-bold text-secondary mb-3">Articles commandés & Stocks</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Menu</th>
                            <th class="text-center">Quantité Commandée</th>
                            <th class="text-center">Stock Actuel Restant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($liste_plats as $plat): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($plat['titre']) ?></td>
                                <td class="text-center fs-5 fw-bold text-primary">x<?= htmlspecialchars($plat['quantite']) ?></td>
                                <td class="text-center">
                                    <span class="badge <?= $plat['stock_restant'] > 5 ? 'bg-success' : 'bg-danger' ?> fs-6">
                                        <?= htmlspecialchars($plat['stock_restant']) ?> en stock
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="bg-light p-3 rounded-3 d-flex justify-content-between align-items-center mb-4 border border-2">
                <span class="fs-5 fw-bold text-dark">Montant Total à régler :</span>
                <span class="fs-3 fw-bold text-success"><?= number_format($commande['total'], 2, ',', ' ') ?> €</span>
            </div>

            <hr class="my-4">

            <h5 class="fw-bold text-secondary mb-3">Mettre à jour le Statut de la Commande</h5>
            <form method="POST" class="row g-3">
                
                <div class="col-12">
                    <label class="form-label fw-bold">Sélectionnez le nouveau statut :</label>
                    <select name="statut" id="statutSelect" class="form-select form-select-lg border-2" onchange="toggleAnnulationBlock()">
                        <option value="reçue" <?= $commande['statut'] === 'reçue' ? 'selected' : '' ?>>Reçue (En attente de validation)</option>
                        <option value="accepté" <?= $commande['statut'] === 'accepté' ? 'selected' : '' ?>>Accepté (Valide la commande & déduit les stocks)</option>
                        <option value="en préparation" <?= $commande['statut'] === 'en préparation' ? 'selected' : '' ?>>En préparation (En cuisine)</option>
                        <option value="en cours de livraison" <?= $commande['statut'] === 'en cours de livraison' ? 'selected' : '' ?>>En cours de livraison (Logistique Julie)</option>
                        <option value="livré" <?= $commande['statut'] === 'livré' ? 'selected' : '' ?>>Livré (Remis au client)</option>
                        <option value="en attente du retour de matériel" <?= $commande['statut'] === 'en attente du retour de matériel' ? 'selected' : '' ?>>En attente du retour de matériel (Alerte mail sous 10j - Frais 600€)</option>
                        <option value="terminée" <?= $commande['statut'] === 'terminée' ? 'selected' : '' ?>>Terminée</option>
                        <option value="annulée" <?= $commande['statut'] === 'annulée' ? 'selected' : '' ?>>Annulée</option>
                    </select>
                </div>

                <div id="blocAnnulation" class="col-12 d-none">
                    <div class="card border-danger bg-light-danger p-3 rounded-3">
                        <h6 class="text-danger fw-bold mb-2">⚠️ Informations d'annulation requises :</h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Mode de contact :</label>
                                <select name="mode_contact" class="form-select border-danger">
                                    <option value="">-- Choisir --</option>
                                    <option value="Appel GSM" <?= ($commande['mode_contact'] ?? '') === 'Appel GSM' ? 'selected' : '' ?>>Appel GSM</option>
                                    <option value="Email direct" <?= ($commande['mode_contact'] ?? '') === 'Email direct' ? 'selected' : '' ?>>Email direct</option>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">Motif de l'annulation :</label>
                                <textarea name="motif_annulation" class="form-control border-danger" rows="2" placeholder="Expliquez pourquoi la commande est annulée..."><?= htmlspecialchars($commande['motif_annulation'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <button type="submit" name="update_status" class="btn btn-dark btn-lg w-100 rounded-pill fw-bold fs-5 shadow-sm">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>

        </div>
    </div>
</main>

<script>
// Fonction JS pour afficher/masquer le bloc d'annulation en temps réel
function toggleAnnulationBlock() {
    const select = document.getElementById('statutSelect');
    const bloc = document.getElementById('blocAnnulation');
    
    if (select.value === 'annulée') {
        bloc.classList.remove('d-none');
    } else {
        bloc.classList.add('d-none');
    }
}

// Lancement automatique au chargement initial de la page si la commande était déjà enregistrée en tant qu'annulée
document.addEventListener("DOMContentLoaded", toggleAnnulationBlock);
</script>

<?php include '../includes/footer.php'; ?>