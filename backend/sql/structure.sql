-- --------------------------------------------------------
-- Structure de la base de données réelle : viteetgourmand
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS utilisateurs (
   id INT AUTO_INCREMENT,
   nom VARCHAR(50) NOT NULL,
   prenom VARCHAR(50) NOT NULL,
   email VARCHAR(100) NOT NULL,
   telephone VARCHAR(20) DEFAULT NULL,
   mot_de_passe VARCHAR(255) NOT NULL,
   role VARCHAR(50) DEFAULT 'utilisateur',
   date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP,
   adresse TEXT DEFAULT NULL,
   ville VARCHAR(50) DEFAULT NULL,
   code_postal VARCHAR(10) DEFAULT NULL,
   actif INT DEFAULT 1,
   PRIMARY KEY(id),
   UNIQUE(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS menu (
   id INT AUTO_INCREMENT,
   nom_technique VARCHAR(100) NOT NULL,
   categorie VARCHAR(50) NOT NULL,
   titre VARCHAR(100) NOT NULL,
   description TEXT NOT NULL,
   prix_pers DECIMAL(10,2) NOT NULL,
   pers_min INT NOT NULL,
   conditions TEXT NOT NULL,
   allergene TEXT DEFAULT NULL,
   stock INT NOT NULL,
   galerie VARCHAR(255) DEFAULT NULL,
   plats TEXT DEFAULT NULL,
   PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS commandes (
   id INT AUTO_INCREMENT,
   id_utilisateur INT NOT NULL,
   total DECIMAL(10,2) NOT NULL,
   date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
   statut VARCHAR(100) DEFAULT 'en attente',
   mode_contact VARCHAR(50) DEFAULT NULL,
   motif_annulation TEXT DEFAULT NULL,
   PRIMARY KEY(id),
   FOREIGN KEY(id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS details_commandes (
   id INT AUTO_INCREMENT,
   id_commande INT NOT NULL,
   id_menu INT NOT NULL,
   quantite INT NOT NULL DEFAULT 1,
   prix_unitaire DECIMAL(10,2) NOT NULL,
   PRIMARY KEY(id),
   FOREIGN KEY(id_commande) REFERENCES commandes(id) ON DELETE CASCADE,
   FOREIGN KEY(id_menu) REFERENCES menu(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS avis (
   id INT AUTO_INCREMENT,
   id_utilisateur INT NOT NULL,
   note INT NOT NULL,
   commentaire TEXT NOT NULL,
   statut VARCHAR(50) DEFAULT 'en attente',
   date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id),
   FOREIGN KEY(id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS contact_messages (
   id INT AUTO_INCREMENT,
   nom VARCHAR(100) NOT NULL,
   prenom VARCHAR(100) DEFAULT NULL,
   email VARCHAR(150) NOT NULL,
   sujet VARCHAR(255) DEFAULT NULL,
   message TEXT NOT NULL,
   date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
   PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;