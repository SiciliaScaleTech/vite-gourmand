<?php
/**
 * Script de traitement sécurisé du formulaire de contact
 * Projet : Vite & Gourmand
 */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. Récupération et Nettoyage (Sanitization)
    // On utilise htmlspecialchars avec ENT_QUOTES pour une sécurité maximale
    $nom     = htmlspecialchars(trim($_POST['nom']), ENT_QUOTES, 'UTF-8');
    $prenom  = htmlspecialchars(trim($_POST['prenom']), ENT_QUOTES, 'UTF-8');
    $email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $sujet   = htmlspecialchars(trim($_POST['sujet']), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message']), ENT_QUOTES, 'UTF-8');

    // 2. Validation (Check si les champs sont vides ou l'email mal formé)
    if (empty($nom) || empty($prenom) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // On redirige vers le formulaire avec un code erreur
        header("Location: contact.php?status=error");
        exit;
    }

    // 3. Configuration de l'envoi (Julie & José)
    $to      = "julie@vite-gourmand.fr";
    $subject = "Site Web - Nouveau message de : " . $nom . " " . $prenom;
    
    // On construit le corps du mail proprement
    $body    = "--- Nouveau message reçu ---\n\n";
    $body   .= "Client : " . $nom . " " . $prenom . "\n";
    $body   .= "Email : " . $email . "\n";
    $body   .= "Sujet : " . $sujet . "\n\n";
    $body   .= "Message :\n" . $message . "\n\n";
    $body   .= "------------------------------";

    // Headers pour éviter que le mail finisse en SPAM
    $headers  = "From: no-reply@vite-gourmand.fr\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

    // 4. Envoi réel
    // Note : mail() renvoie true si le serveur accepte le mail pour envoi
    if (mail($to, $subject, $body, $headers)) {
        header("Location: contact.php?status=success");
    } else {
        header("Location: contact.php?status=server_error");
    }
    exit;

} else {
    // Si accès direct au fichier sans formulaire, on dégage vers l'accueil contact
    header("Location: contact.php");
    exit;
}