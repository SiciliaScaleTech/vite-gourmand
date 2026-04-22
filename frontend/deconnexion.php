<?php
session_start(); // On doit démarrer la session pour pouvoir la détruire

// On vide toutes les variables de session
session_unset();

// On détruit la session sur le serveur
session_destroy();

// On redirige l'utilisateur vers l'accueil (ou la page de connexion)
header("Location: index.php");
exit();
?>