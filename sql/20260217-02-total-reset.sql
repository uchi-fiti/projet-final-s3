-- ============================================================
-- TOTAL RESET : recréation complète de la base BNGRC
-- Date : 2026-02-17
-- Usage : exécuter ce script pour repartir de zéro
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS bngrc_achats;
DROP TABLE IF EXISTS bngrc_attributions;
DROP TABLE IF EXISTS bngrc_dons;
DROP TABLE IF EXISTS bngrc_besoins;
DROP TABLE IF EXISTS bngrc_types_besoins;
DROP TABLE IF EXISTS bngrc_villes;
DROP TABLE IF EXISTS bngrc_regions;

SET FOREIGN_KEY_CHECKS = 1;

-- ======================
-- TABLE REGIONS
-- ======================
CREATE TABLE bngrc_regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ======================
-- TABLE VILLES
-- ======================
CREATE TABLE bngrc_villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    region_id INT NOT NULL,
    CONSTRAINT fk_ville_region
        FOREIGN KEY (region_id)
        REFERENCES bngrc_regions(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ======================
-- TABLE TYPES_BESOINS
-- ======================
CREATE TABLE bngrc_types_besoins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ======================
-- TABLE BESOINS
-- quantite / quantite_restante : pour types Nature & Materiaux
-- montant / montant_restant   : pour type Argent
-- ======================
CREATE TABLE bngrc_besoins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    type_id INT NOT NULL,
    description VARCHAR(150),
    prix_unitaire DECIMAL(10,2) NULL,
    quantite INT NULL,
    quantite_restante INT NULL,
    montant DECIMAL(12,2) NULL,
    montant_restant DECIMAL(12,2) NULL,
    ordre INT DEFAULT 0,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_besoin_ville
        FOREIGN KEY (ville_id)
        REFERENCES bngrc_villes(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_besoin_type
        FOREIGN KEY (type_id)
        REFERENCES bngrc_types_besoins(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ======================
-- TABLE DONS
-- quantite / quantite_restante : pour types Nature & Materiaux
-- montant / montant_restant   : pour type Argent
-- ======================
CREATE TABLE bngrc_dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_id INT NOT NULL,
    description VARCHAR(150),
    montant DECIMAL(12,2) NULL,
    montant_restant DECIMAL(12,2) NULL,
    quantite INT NULL,
    quantite_restante INT NULL,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_don_type
        FOREIGN KEY (type_id)
        REFERENCES bngrc_types_besoins(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ======================
-- TABLE ATTRIBUTIONS
-- quantite_attribuee : pour types Nature & Materiaux
-- montant_attribue   : pour type Argent
-- ======================
CREATE TABLE bngrc_attributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite_attribuee INT NULL,
    montant_attribue DECIMAL(12,2) NULL,
    date_attribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attr_besoin
        FOREIGN KEY (besoin_id)
        REFERENCES bngrc_besoins(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_attr_don
        FOREIGN KEY (don_id)
        REFERENCES bngrc_dons(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ======================
-- TABLE ACHATS
-- (Achats effectués avec des dons de type Argent)
-- ======================
CREATE TABLE bngrc_achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    quantite INT NOT NULL,
    montant_unitaire DECIMAL(12,2) NOT NULL,
    frais_pourcentage DECIMAL(5,2) NOT NULL DEFAULT 0,
    montant_base DECIMAL(12,2) NOT NULL,
    montant_frais DECIMAL(12,2) NOT NULL DEFAULT 0,
    montant_total DECIMAL(12,2) NOT NULL,
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_achat_besoin
        FOREIGN KEY (besoin_id)
        REFERENCES bngrc_besoins(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- DONNEES DE TEST
-- ============================================================

-- Types de besoins : 1=Nature, 2=Materiaux, 3=Argent
INSERT INTO bngrc_types_besoins (nom) VALUES
('Nature'),
('Materiaux'),
('Argent');

-- ======================
-- REGIONS
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
-- BESOINS  (Nature & Materiaux → quantite ; Argent → montant)
-- ======================

-- Type Nature (id=1) : quantite + quantite_restante, pas de montant
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante) VALUES
(1, 1, 'Riz 50kg',               25000.00, 200, 200),
(2, 1, 'Riz 50kg',               25000.00,  80,  80),
(3, 1, 'Huile alimentaire 5L',   18000.00, 150, 150),
(4, 1, 'Farine de blé 25kg',     20000.00, 100, 100),
(5, 1, 'Conserves alimentaires',   5000.00, 300, 300),
(6, 1, 'Riz 50kg',               25000.00, 120, 120),
(7, 1, 'Sucre 25kg',             15000.00,  60,  60),
(8, 1, 'Riz 50kg',               25000.00, 180, 180),
(9, 1, 'Haricots secs 25kg',     18000.00, 100, 100),
(10, 1, 'Riz 50kg',              25000.00,  50,  50);

-- Type Materiaux (id=2) : quantite + quantite_restante, pas de montant
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante) VALUES
(1, 2, 'Couvertures',            15000.00, 100, 100),
(3, 2, 'Vêtements enfants',       8000.00, 200, 200),
(4, 2, 'Chaussures',             12000.00,  80,  80),
(6, 2, 'Couvertures',            15000.00,  90,  90),
(8, 2, 'Vêtements adultes',      10000.00, 150, 150),
(9, 2, 'Couvertures',            15000.00,  60,  60);

-- Type Argent (id=3) : montant + montant_restant, pas de quantite
INSERT INTO bngrc_besoins (ville_id, type_id, description, montant, montant_restant) VALUES
(1, 3, 'Reconstruction école',            15000000.00, 15000000.00),
(2, 3, 'Réhabilitation route',             8000000.00,  8000000.00),
(3, 3, 'Construction abris temporaires',   12000000.00, 12000000.00),
(5, 3, 'Achat médicaments',                5000000.00,  5000000.00),
(7, 3, 'Réparation pont',                  6000000.00,  6000000.00),
(8, 3, 'Reconstruction maisons',          20000000.00, 20000000.00),
(10, 3, 'Installation puits',              3500000.00,  3500000.00);

-- ======================
-- DONS  (Nature & Materiaux → quantite ; Argent → montant)
-- ======================

-- Dons de type Nature (id=1) : quantite + quantite_restante
INSERT INTO bngrc_dons (type_id, description, quantite, quantite_restante) VALUES
(1, 'Don de riz - Croix Rouge',           200, 200),
(1, 'Don de conserves - ONG Care',        300, 300),
(1, 'Don alimentaire - Gouvernement',     180, 180),
(1, 'Don de farine - PAM',               100, 100);

-- Dons de type Materiaux (id=2) : quantite + quantite_restante
INSERT INTO bngrc_dons (type_id, description, quantite, quantite_restante) VALUES
(2, 'Don de couvertures - UNICEF',         250, 250),
(2, 'Don de vêtements - Secours Populaire', 200, 200),
(2, 'Don de chaussures - Association locale', 80, 80);

-- Dons de type Argent (id=3) : montant + montant_restant, pas de quantite
INSERT INTO bngrc_dons (type_id, description, montant, montant_restant) VALUES
(3, 'Don monétaire - Banque Mondiale',     50000000.00, 50000000.00),
(3, 'Don monétaire - Union Européenne',    25000000.00, 25000000.00),
(3, 'Don monétaire - Gouvernement',        10000000.00, 10000000.00);
