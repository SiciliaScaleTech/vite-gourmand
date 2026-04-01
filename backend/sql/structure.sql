CREATE TABLE Utilisateurs(
   id_user INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   email VARCHAR(100) NOT NULL,
   password VARCHAR(255) NOT NULL,
   gsm VARCHAR(20) NOT NULL,
   adresse TEXT NOT NULL,
   ville VARCHAR(50) NOT NULL,
   role ENUM('visiteur', 'utilisateur', 'employe', 'admin') DEFAULT 'utilisateur',
   is_active BOOLEAN DEFAULT TRUE,
   PRIMARY KEY(id_user),
   UNIQUE(email)
);

CREATE TABLE menu(
   id_menu INT AUTO_INCREMENT,
   titre VARCHAR(100) NOT NULL,
   description TEXT NOT NULL,
   theme VARCHAR(50) NOT NULL,
   regime VARCHAR(50) NOT NULL,
   prix_min DECIMAL(10,2) NOT NULL,
   nb_pers_min INT NOT NULL,
   conditions TEXT NOT NULL,
   stock INT NOT NULL,
   PRIMARY KEY(id_menu)
);

CREATE TABLE plats(
   id_plat INT AUTO_INCREMENT,
   nom VARCHAR(100) NOT NULL,
   type ENUM('entrée', 'plat', 'dessert') NOT NULL,
   PRIMARY KEY(id_plat)
);

CREATE TABLE allergene(
   id_allergene INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_allergene)
);

CREATE TABLE Commande(
   id_commande INT AUTO_INCREMENT,
   id_user INT NOT NULL,
   id_menu INT NOT NULL,
   nb_personnes INT NOT NULL,
   date_prestation DATE NOT NULL,
   heure_prestation TIME NOT NULL,
   lieu_livraison TEXT NOT NULL,
   statut ENUM('en attente', 'accepté', 'en préparation', 'en livraison', 'livré', 'annulé') DEFAULT 'en attente',
   prix_total DECIMAL(10,2) NOT NULL,
   materiel_prete BOOLEAN DEFAULT FALSE,
   PRIMARY KEY(id_commande),
   FOREIGN KEY(id_user) REFERENCES Utilisateurs(id_user),
   FOREIGN KEY(id_menu) REFERENCES menu(id_menu)
);

CREATE TABLE composer(
   id_menu INT,
   id_plat INT,
   PRIMARY KEY(id_menu, id_plat),
   FOREIGN KEY(id_menu) REFERENCES menu(id_menu) ON DELETE CASCADE,
   FOREIGN KEY(id_plat) REFERENCES plats(id_plat) ON DELETE CASCADE
);