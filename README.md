# Vite & Gourmand - Plateforme de Commande

## Description
Application web dynamique de commande de repas à domicile et de gestion pour l'entreprise "Vite & Gourmand". L'application intègre un espace client, un espace employé responsive (gestion des commandes et de la carte) et un tableau de bord administrateur avec analyse des ventes.

##  Technologies utilisées
- **Backend :** PHP (Architecture MVC / Scripts de traitement)
- **Bases de données :** - **Relationnel :** MySQL (Gestion des utilisateurs, commandes, avis)
  - **NoSQL :** MongoDB Atlas Cloud (Statistiques avancées, volumes de ventes des menus)
- **Frontend & Design :** HTML5, CSS3, Bootstrap 5 (Interface 100% Mobile Responsive)
- **Librairies :** Chart.js (Visualisation graphique du Chiffre d'Affaires)
- **Gestion de version :** Git (Méthodologie Git Flow)

## Installation en local

### 1. Clonage du projet
```bash
git clone vite-gourmand
2. Configuration de la base de données MySQL
Lancez votre serveur local (WAMP/MAMP/XAMPP).

Créez une base de données nommée vite_et_gourmand.

Importez les fichiers SQL dans l'ordre :

structure.sql (Création des tables et relations)

data.sql (Jeu de données de test : comptes clients, employés, admin)

3. Configuration de MongoDB NoSQL
L'application est nativement connectée à un cluster distant MongoDB Atlas via la chaîne de connexion sécurisée configurée dans backend/config.php.

Note pour la correction : Assurez-vous que l'extension PHP mongodb est bien activée sur votre environnement local (php.ini) pour l'affichage des graphiques de statistiques.

4. Lancement
Configurez le dossier racine de votre serveur local sur le dossier du projet et accédez-y via http://localhost/[nom-du-projet].

Comptes de test (Jeu de données)
Pour tester les différents espaces de la plateforme, utilisez les identifiants suivants (disponibles dans data.sql) :

Admin : tourysicilia91@gmail.com / admin123

Employé : tourysicilia@gmail.com / 1234

Client : fuse.carole@gmail.com / Abcdefgh123!