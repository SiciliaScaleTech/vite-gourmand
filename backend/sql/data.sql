-- Insertion de Julie (Admin) et José (Employé)
INSERT INTO Utilisateurs (nom, prenom, email, password, gsm, adresse, ville, role) VALUES 
('Julie', 'Gourmande', 'julie@viteetgourmand.fr', 'hash_password_123', '0601020304', '12 rue de la Paix', 'Bordeaux', 'admin'),
('José', 'Vite', 'jose@viteetgourmand.fr', 'hash_password_456', '0605060708', '5 Avenue des Chartrons', 'Bordeaux', 'employe');

-- Insertion de quelques plats
INSERT INTO plats (nom, type) VALUES 
('Saumon Gravlax', 'entrée'),
('Magret de Canard au miel', 'plat'),
('Fondant au chocolat', 'dessert');

-- Insertion d'un Menu
INSERT INTO menu (titre, description, theme, regime, prix_min, nb_pers_min, conditions, stock) VALUES 
('Menu Prestige Noël', 'Un menu d exception pour vos fêtes', 'Noël', 'classique', 45.00, 10, 'Commander 72h à l avance', 50);

-- Lier les plats au menu (Table Pivot)
INSERT INTO composer (id_menu, id_plat) VALUES (1, 1), (1, 2), (1, 3);