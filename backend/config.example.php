<?php
/**
 * FICHIER EXEMPLE DE CONFIGURATION (config.example.php)
 * * Pour configurer votre environnement local :
 * 1. Copiez ce fichier et renommez-le en 'config.php' dans le même dossier.
 * 2. Remplissez les variables ci-dessous avec vos identifiants locaux.
 * 3. Ne commitez JAMAIS le fichier 'config.php' final sur Git.
 */

$host = 'localhost';
$dbname = 'votre_nom_de_bdd';  // À modifier en local
$username = 'root';            // À modifier en local
$password = '';                // À modifier en local (ex: 'root' sur Mac/MAMP)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Configuration des erreurs PDO pour faciliter le développement
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur critique de connexion à la base de données : " . $e->getMessage());
}