INSERT INTO utilisateurs (id, nom, prenom, email, telephone, mot_de_passe, role, date_inscription, adresse, ville, code_postal, actif) VALUES 
-- 1. L'Admin 
(13, 'Santos', 'José', 'jose.santos@gmail.com', NULL, '$2y$10$w3eXvX9A0p2A4e/Tqg2n7eCgC5xclVqB9zX6D2mZ9h7...', 'admin', '2026-06-10 11:05:59', NULL, NULL, NULL, 1),

-- 2. L'Employé 
(1, 'Latoure', 'Julie', 'julie.latoure@gmail.com', '0658569874', '$2y$10$mQKTs4stKFuT6z7dFVVEtekdwzYeU98d4Oakyjs.aGW...', 'employe', NULL, '12 rue de la paix', 'bordeaux', '33000', 1),

-- 3. L'Utilisateur inscrit 
(8, 'fuse', 'carole', 'fuse.carole@gmail.com', '0615457896', '$2y$10$YzpD9IMdL/oO4SGz2aygp.wCAaeOSCkPPkfrwW.A/Ry...', 'utilisateur', '2026-06-08 20:59:43', '12 rue de la paix', 'lieusaint', '77127', 1);



INSERT INTO menu (id, nom_technique, categorie, titre, description, prix_pers, pers_min, conditions, allergene, stock, galerie, plats) VALUES 
(1, 'Noel', 'fetes', 'Menu de Noël gourmet', 'Un festin traditionnel et chaleureux pour vos fête...', 45.00, 6, 'Nécessite de commander 10 jours avant le réveillon...', 'Gluten, Fruits à coque', 0, 'assets/noel1-img-details.jpg|assets/noel2-img-deta...', 'Entrée: Foi gras|plat: Chapon aux marrons|Dessert:...'),

(2, 'Paques', 'fetes', 'Menu de Pâques', 'Célébrez le printemps avec des saveurs authentique...', 38.00, 4, 'Commande possible jusqu''à 5 jours avant.', 'Lactose, oeufs', 8, 'assets/paque1-img-detail.webp|assets/paque2-img-de...', 'Entrée: Asperges|plat: Agneau pascale|Dessert: Gat...'),

(3, 'Halloween', 'fetes', 'Menu d''halloween', 'Célébrez halloween en famille ou entre amis avec d...', 45.00, 6, 'Commande possible jusqu''à 8 jours avant.', 'neant', 6, 'assets/halloween1-img-detail.jpg|assets/halloween2...', 'Entrée: velouté de courge|plat: citrouille farcis|...'),

(4, 'Classique', 'classique', 'Menu classique', 'Une repas équilibré quand vous n''avez pas eu le te...', 27.00, 2, 'Commande possible jusqu''à 3 jours avant.', 'arachide, noix', 17, 'assets/classique1-img-detail.jpeg|assets/classique...', 'Entrée: salade et tomates cerises|plat: tranches d...'),

(5, 'Mariage', 'evenement', 'Menu de mariage', 'Une repas digne du plus beau jour de votre vie.', 60.00, 20, 'Commande possible jusqu''à 1 mois avant.', 'crustacés', 19, 'assets/mariage1-img-detail.png|assets/mariage2-img...', 'Entrée: jambon sec/crevette rose|plat: roulés au j...'),

(6, 'Bapteme', 'evenement', 'Menu de bapteme', 'Un jour important pour vous et votre enfant, laiss...', 30.00, 15, 'Commande possible jusqu''à 3 semaines avant.', 'oeufs, saumon', 8, 'assets/bapteme1-img-detail.jpg|assets/bapteme2-img...', 'Entrée: saumons sur toast|plat: velouté de tomate ...'),

(10, 'Classique', 'menu', 'Menu végétarien', 'test', 25.00, 2, 'commande possible jusqu''à 1 semaine avant', 'Néant', 10, 'assets/images/menu_6a281b44c37cf9.56653532.jpg', 'Entrée: Vérine de tomates|plat: velouté d''avocat, ...'),

(11, 'Classique', 'menu', 'Menu standard', 'super menu gouteux', 15.00, 4, 'commande possible jusqu''à 1 semaine avant', 'Néant', 9, 'assets/images/menu_6a2b087c1ee695.89759487.jpg', 'Entrée: Vérine de tomates|plat: velouté d''avocat, ...');


