<?php
/**
 * Fichier de configuration et de connexion à la base de données
 * Emplacement : /backend/config.php
 */

// 1. Paramètres de connexion
$host    = 'localhost';         // En local, c'est presque toujours localhost
$db      = 'viteetgourmand';     // LE NOM DE TA BASE DE DONNÉES (à vérifier dans phpMyAdmin)
$user    = 'root';              // Identifiant par défaut
$pass    = '';                  // Mot de passe (vide par défaut sur XAMPP/WAMP)
$charset = 'utf8mb4';           // Encodage pour bien gérer les accents

// 2. Création du DSN (Data Source Name)
$dsn = "mysql:host=$host;port=3307;dbname=$db;charset=$charset";

// 3. Options de sécurité et de performance
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Affiche les erreurs SQL précises
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Récupère les données sous forme de tableaux associatifs
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requêtes préparées (protection injection SQL)
];

try {
    // 4. Tentative de connexion
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // 5. En cas d'erreur, on arrête tout et on affiche le message
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}