<?php
// 1. Connexion à la base de donnée 
require_once 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Récupération et nettoyage des données
    $nom     = htmlspecialchars(trim($_POST['nom']));
    $prenom  = htmlspecialchars(trim($_POST['prenom']));
    $email   = htmlspecialchars(trim($_POST['email']));
    $sujet   = htmlspecialchars(trim($_POST['sujet']));
    $message = htmlspecialchars(trim($_POST['message']));

    // 3. Vérification des champs obligatoires
    if (!empty($nom) && !empty($email) && !empty($message)) {
        
        try {
            // 4. Préparation de la requête SQL
            $sql = "INSERT INTO contact_messages (nom, prenom, email, sujet, message) 
                    VALUES (:nom, :prenom, :email, :sujet, :message)";
            
            $stmt = $pdo->prepare($sql);
            
            // 5. Exécution de la requête
            $stmt->execute([
                ':nom'    => $nom,
                ':prenom' => $prenom,
                ':email'  => $email,
                ':sujet'  => $sujet,
                ':message' => $message
            ]);

            // --- NOTIFICATION MAIL ---
            $destinataire = "sicilia.scaletech@gmail.com"; 
            $sujet_mail = "Nouveau message de contact : " . $sujet;
            $corps_mail = "Nom : $nom $prenom\nEmail : $email\nSujet : $sujet\nMessage :\n$message";
            $headers = "From: webmaster@vite-gourmand.fr\r\nReply-To: $email";
            
            @mail($destinataire, $sujet_mail, $corps_mail, $headers);

            // 6. SUCCÈS : Redirection vers frontend/contact.php
            header('Location: ../frontend/contact.php?success=1');
            exit();

        } catch (PDOException $e) {
            die("Erreur lors de l'enregistrement : " . $e->getMessage());
        }

    } else {
        // 7. ERREUR : Champs vides
        header('Location: ../frontend/contact.php?error=empty');
        exit();
    }

} else {
    // Tentative d'accès direct au fichier
    header('Location: ../frontend/index.php');
    exit();
}