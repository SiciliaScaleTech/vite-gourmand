<?php
session_start();
require_once '../../backend/config.php';

// 🛡️ SÉCURITÉ : Accès réservé aux employés et admins
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['employe', 'admin'])) {
    header('Location: ../index.php');
    exit();
}

$message = "";
$commande_id = $_GET['id'] ?? null;

if (!$commande_id) {
    header('Location: employe-dashboard.php');
    exit();
}

// 1. TRAITEMENT DE LA MISE À JOUR DU STATUT (Formulaire soumis)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $nouveau_statut = $_POST['statut'];
    $mode_contact = trim($_POST['mode_contact'] ?? '');
    $motif_annulation = trim($_POST['motif_annulation'] ?? '');

    // Exigence ECF : Bloquer l'annulation si le contact ou le motif est manquant
    if ($nouveau_statut === 'annulée' && (empty($mode_contact) || empty($motif_annulation))) {
        $message = "<div class='alert alert-danger fw-bold'>⚠️ Erreur : Impossible d'annuler la commande sans spécifier le mode de contact (GSM/Mail) et le motif d'annulation.</div>";
    } else {
        try {
            // Si c'est une annulation, on enregistre le motif et le mode de contact
            if ($nouveau_statut === 'annulée') {
                $stmt = $pdo->prepare("UPDATE commandes SET statut = ?, mode_contact = ?, motif_annulation = ? WHERE id = ?");
                $stmt->execute([$nouveau_statut, $mode_contact, $motif_annulation, $commande_id]);
                $message = "<div class='alert alert-warning fw-bold'>La commande a été annulée. Client contacté par " . htmlspecialchars($mode_contact) . ".</div>";
            } else {
                // Statut classique (ex: accepté, en préparation, livré...)
                $stmt = $pdo->prepare("UPDATE commandes SET statut = ?, mode_contact = NULL, motif_annulation = NULL WHERE id = ?");
                $stmt->execute([$nouveau_statut, $commande_id]);
                $message = "<div class='alert alert-success fw-bold'>Le statut de la commande a été mis à jour avec succès !</div>";
                
                // Exigence ECF : Alerte automatique si "en attente du retour de matériel"
                if ($nouveau_statut === 'en attente du retour de matériel') {
                    $message .= "<div class='alert alert-danger fw-bold mt-2'>🚨 ALERTE MATÉRIEL : Une notification par mail automatique a été simulée. Le client dispose de 10 jours ouvrés pour restituer le matériel sous peine d'une pénalité de 600€ (mentionné dans les CGV).</div>";
                }
            }
        } catch (PDOException $e) {
            $message = "<div class='alert alert-danger'>Erreur lors de la mise à jour : " . $e->getMessage() . "</div>";
        }
    }
}

// 2. RÉCUPÉRATION DES INFOS DE LA COMMANDE (Corrigé avec id_utilisateur)
$stmt = $pdo->prepare("SELECT c.*, u.nom, u.prenom, u.email, u.telephone 
                        FROM commandes c 
                        JOIN utilisateurs u ON c.id_utilisateur = u.id 
                        WHERE c.id = ?");
$stmt->execute([$commande_id]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    die("Commande introuvable.");
}

include '../includes/header.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <a href="employe-dashboard.php" class="btn btn-outline-secondary rounded-pill mb-4">⬅️ Retour au tableau de bord</a>
            
            <?= $message ?>

            <div class="card shadow border-0 mb-4">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                        <div>
                            <h2 class="mb-0">Gestion de la Commande #<?= $commande['id'] ?></h2>
                            <p class="text-muted mb-0">Passée le <?= date('d/m/Y à H:i', strtotime($commande['date_commande'])) ?></p>
                        </div>
                        <span class="badge bg-dark fs-5 px-3 py-2">Total : <?= number_format($commande['total'], 2, ',', ' ') ?> €</span>
                    </div>

                    <!-- BLOC INFOS CLIENT -->
                    <div class="row mb-4 bg-light p-3 rounded">
                        <div class="col-md-6">
                            <h5 class="text-muted border-bottom pb-1">Client</h5>
                            <p class="mb-1 fw-bold"><?= htmlspecialchars($commande['prenom'] . ' ' . $commande['nom']) ?></p>
                            <p class="mb-1 text-muted">✉️ <?= htmlspecialchars($commande['email']) ?></p>
                            <p class="mb-0 text-muted">📞 <?= htmlspecialchars($commande['telephone'] ?? 'Non renseigné') ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted border-bottom pb-1">Adresse de livraison</h5>
                            <p class="mb-0">
                                <!-- Adapte ces colonnes si les noms diffèrent dans ta table commandes ou utilisateurs -->
                                <?= htmlspecialchars($commande['adresse'] ?? 'À emporter') ?><br>
                                <?= htmlspecialchars($commande['code_postal'] ?? '') ?> <?= htmlspecialchars($commande['ville'] ?? '') ?>
                            </p>
                        </div>
                    </div>

                    <!-- SI LA COMMANDE EST DEJA ANNULÉE, ON AFFICHE LE MOTIF -->
                    <?php if ($commande['statut'] === 'annulée'): ?>
                        <div class="alert alert-dark border-0 shadow-sm p-4 mb-4">
                            <h5 class="alert-heading fw-bold text-danger">❌ Commande Annulée</h5>
                            <p class="mb-1"><strong>Mode de contact utilisé :</strong> <?= htmlspecialchars($commande['mode_contact'] ?? 'Non spécifié') ?></p>
                            <p class="mb-0"><strong>Motif de l'annulation :</strong> <?= htmlspecialchars($commande['motif_annulation'] ?? 'Non spécifié') ?></p>
                        </div>
                    <?php endif; ?>

                    <!-- FORMULAIRE DE MISE À JOUR (Julie) -->
                    <form method="POST" class="mt-4">
                        <h4 class="mb-3 text-secondary">Mettre à jour le flux de livraison</h4>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Sélectionner le nouveau statut :</label>
                            <select name="statut" id="statutSelect" class="form-select form-select-lg" onchange="toggleAnnulationBlock()">
                                <option value="reçue" <?= $commande['statut'] === 'reçue' ? 'selected' : '' ?>>Reçue</option>
                                <option value="accepté" <?= $commande['statut'] === 'accepté' ? 'selected' : '' ?>>Accepté (Validée par l'équipe)</option>
                                <option value="en préparation" <?= $commande['statut'] === 'en préparation' ? 'selected' : '' ?>>En préparation (Cuisine)</option>
                                <option value="en cours de livraison" <?= $commande['statut'] === 'en cours de livraison' ? 'selected' : '' ?>>En cours de livraison (Équipe logistique de Julie)</option>
                                <option value="livré" <?= $commande['statut'] === 'livré' ? 'selected' : '' ?>>Livré (Remis au client)</option>
                                <option value="en attente du retour de matériel" <?= $commande['statut'] === 'en attente du retour de matériel' ? 'selected' : '' ?>>En attente du retour de matériel (⚠️ Alerte CGV 600€)</option>
                                <option value="terminée" <?= $commande['statut'] === 'terminée' ? 'selected' : '' ?>>Terminée</option>
                                <option value="annulée" <?= $commande['statut'] === 'annulée' ? 'selected' : '' ?>>Annulée (⚠️ Justificatif requis)</option>
                            </select>
                        </div>

                        <!-- 📞 BLOC OBLIGATOIRE D'ANNULATION (Masqué par défaut) -->
                        <div id="blocAnnulation" class="card border-danger mb-4 d-none">
                            <div class="card-header bg-danger text-white fw-bold">
                                🔒 Justificatif d'annulation obligatoire (Appel ou Mail obligatoire avant action)
                            </div>
                            <div class="card-body bg-light">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mode de contact utilisé :</label>
                                    <select name="mode_contact" class="form-select">
                                        <option value="">-- Choisir le mode de contact --</option>
                                        <option value="Appel GSM">Appel GSM</option>
                                        <option value="Email direct">Email direct</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Motif précis fourni au client :</label>
                                    <textarea name="motif_annulation" class="form-control" rows="3" placeholder="Ex: Client a changé d'avis / Erreur de date lors de la commande..."></textarea>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="update_status" class="btn btn-dark btn-lg w-100 rounded-pill">Appliquer le nouveau statut</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Script JS pour afficher dynamiquement le bloc d'annulation si "annulée" est sélectionné
function toggleAnnulationBlock() {
    const select = document.getElementById('statutSelect');
    const bloc = document.getElementById('blocAnnulation');
    
    if (select.value === 'annulée') {
        bloc.classList.remove('d-none');
    } else {
        bloc.classList.add('d-none');
    }
}
// Lancement au chargement de la page
document.addEventListener("DOMContentLoaded", toggleAnnulationBlock);
</script>

<?php include '../includes/footer.php'; ?>