<?php
/**
 * Script de traitement sécurisé du formulaire de contact
 * Projet : Vite & Gourmand
 */

// 1. On appelle la connexion en tout premier
require_once '../backend/config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 2. Récupération et Nettoyage
    $nom     = htmlspecialchars(trim($_POST['nom']), ENT_QUOTES, 'UTF-8');
    $prenom  = htmlspecialchars(trim($_POST['prenom']), ENT_QUOTES, 'UTF-8');
    $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $sujet   = htmlspecialchars(trim($_POST['sujet']), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8');

    // 3. Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: contact.php?status=error");
        exit;
    }

    // --- NOUVEAUTÉ : ENREGISTREMENT DANS LA BDD ---
    try {
        $sql = "INSERT INTO contact_messages (nom, prenom, email, sujet, message) 
                VALUES (:nom, :prenom, :email, :sujet, :message)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nom'     => $nom,
            ':prenom'  => $prenom,
            ':email'   => $email,
            ':sujet'   => $sujet,
            ':message' => $message
        ]);
        // Si on arrive ici, l'enregistrement est réussi !
    } catch (PDOException $e) {
        // En cas de bug BDD, on s'arrête là pour ne pas envoyer de mail inutilement
        header("Location: contact.php?status=server_error");
        exit;
    }
    // ----------------------------------------------

    // 4. Configuration de l'envoi mail (Optionnel si tu veux juste la BDD)
    $to      = "julie@vite-gourmand.fr";
    $subject = "Site Web - Nouveau message de : $nom $prenom";
    $body    = "Client : $nom $prenom\nEmail : $email\nSujet : $sujet\n\nMessage :\n$message";

    $headers  = "From: no-reply@vite-gourmand.fr\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

    if (mail($to, $subject, $body, $headers)) {
        header("Location: contact.php?status=success");
    } else {
        // Si le mail échoue mais que la BDD a réussi, on peut quand même dire "success" 
        // ou préciser "success_db_only"
        header("Location: contact.php?status=success");
    }
    exit;

} else {
    header("Location: contact.php");
    exit;
}

