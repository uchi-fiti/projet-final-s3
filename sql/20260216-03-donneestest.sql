insert into bngrc_types_besoins (nom) values ("Nature"), ("Materiaux"), ("Argent");
-- ======================
-- bngrc_regions
-- ======================
INSERT INTO bngrc_regions (nom) VALUES
('Analamanga'),
('Atsinanana'),
('Boeny'),
('Vakinankaratra'),
('Atsimo-Andrefana'),
('Haute Matsiatra');

-- ======================
-- VILLES
-- ======================
INSERT INTO bngrc_villes (nom, region_id) VALUES
('Antananarivo', 1),
('Ambohidratrimo', 1),
('Toamasina', 2),
('Brickaville', 2),
('Mahajanga', 3),
('Antsirabe', 4),
('Ambatolampy', 4),
('Toliara', 5),
('Fianarantsoa', 6),
('Ambalavao', 6);

-- ======================
-- TYPES DE BESOINS
-- (déjà insérés dans le script précédent :
--  1=Nourriture, 2=Vetements, 3=Materiaux)
-- ======================

-- ======================
-- BESOINS
-- ======================
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante) VALUES
(1, 1, 'Riz 50kg',            25000.00, 200, 200),
(1, 2, 'Couvertures',          15000.00, 100, 100),
(1, 3, 'Tôles ondulées',       45000.00,  50,  50);

INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante) VALUES
(2, 1, 'Riz 50kg',            25000.00,  80,  80),
(2, 3, 'Bâches plastiques',   12000.00, 120, 120),
(3, 1, 'Huile alimentaire 5L', 18000.00, 150, 150),
(3, 2, 'Vêtements enfants',    8000.00, 200, 200),
(3, 3, 'Planches de bois',    30000.00,  60,  60);
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante) VALUES

(4, 1, 'Farine de blé 25kg',  20000.00, 100, 100),
(4, 2, 'Chaussures',          12000.00,  80,  80),

(5, 1, 'Conserves alimentaires', 5000.00, 300, 300),
(5, 3, 'Ciment (sac 50kg)',    35000.00,  40,  40),

(6, 1, 'Riz 50kg',            25000.00, 120, 120),
(6, 2, 'Couvertures',          15000.00,  90,  90),

(7, 1, 'Sucre 25kg',          15000.00,  60,  60),
(7, 3, 'Clous et vis',         8000.00, 100, 100),

(8, 1, 'Riz 50kg',            25000.00, 180, 180),
(8, 2, 'Vêtements adultes',   10000.00, 150, 150),
(8, 3, 'Tôles ondulées',       45000.00,  70,  70),

(9, 1, 'Haricots secs 25kg',  18000.00, 100, 100),
(9, 2, 'Couvertures',          15000.00,  60,  60),

(10, 1, 'Riz 50kg',           25000.00,  50,  50),
(10, 3, 'Bâches plastiques',  12000.00,  40,  40);

-- ======================
-- DONS
-- ======================
INSERT INTO bngrc_dons (type_id, description, montant_total, quantite, quantite_restante) VALUES
-- Dons de nourriture
(1, 'Don de riz - Croix Rouge',           5000000.00, 200, 200),
(1, 'Don de conserves - ONG Care',        1500000.00, 300, 300),
(1, 'Don alimentaire - Gouvernement',     3600000.00, 180, 180),
(1, 'Don de farine - PAM',               2000000.00, 100, 100),

-- Dons de vêtements
(2, 'Don de couvertures - UNICEF',        3750000.00, 250, 250),
(2, 'Don de vêtements - Secours Populaire', 2400000.00, 200, 200),
(2, 'Don de chaussures - Association locale', 960000.00, 80, 80),

-- Dons de matériaux
(3, 'Don de tôles - Entreprise HOLCIM',   5400000.00, 120, 120),
(3, 'Don de bâches - BNGRC stock',        1920000.00, 160, 160),
(3, 'Don de ciment - LafargeHolcim',      1400000.00,  40,  40);

-- ======================
-- ATTRIBUTIONS
-- ======================

