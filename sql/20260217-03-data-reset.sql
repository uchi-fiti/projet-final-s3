-- ============================================================
-- DATA RESET : Suppression et insertion des données de test
-- Basé sur data.txt
-- Date : 2026-02-17
-- ============================================================

USE bngrc;

-- ======================
-- VIDER LES TABLES (dans l'ordre des FK)
-- ======================
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE bngrc_achats;
TRUNCATE TABLE bngrc_attributions;
TRUNCATE TABLE bngrc_dons;
TRUNCATE TABLE bngrc_besoins;
TRUNCATE TABLE bngrc_villes;
TRUNCATE TABLE bngrc_regions;
TRUNCATE TABLE bngrc_types_besoins;

SET FOREIGN_KEY_CHECKS = 1;

-- ======================
-- TYPES DE BESOINS
-- 1=Nature, 2=Materiel, 3=Argent
-- ======================
INSERT INTO bngrc_types_besoins (id, nom) VALUES
(1, 'Nature'),
(2, 'Materiel'),
(3, 'Argent');

-- ======================
-- REGIONS
-- ======================
INSERT INTO bngrc_regions (id, nom) VALUES
(1, 'Atsinanana'),
(2, 'Vatovavy'),
(3, 'Atsimo-Atsinanana'),
(4, 'Diana'),
(5, 'Menabe');

-- ======================
-- VILLES
-- ======================
INSERT INTO bngrc_villes (id, nom, region_id) VALUES
(1, 'Toamasina', 1),
(2, 'Mananjary', 2),
(3, 'Farafangana', 3),
(4, 'Nosy Be', 4),
(5, 'Morondava', 5);

-- ======================
-- BESOINS (ordonnés par colonne "Ordre" de data.txt)
-- Nature/Materiel → quantite, quantite_restante
-- Argent → montant, montant_restant
-- ======================

-- Ordre 1: Toamasina, materiel, Bâche
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (1, 2, 'Bâche', 15000.00, 200, 200, NULL, NULL, '2026-02-15 00:00:01');

-- Ordre 2: Nosy Be, materiel, Tôle
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (4, 2, 'Tôle', 25000.00, 40, 40, NULL, NULL, '2026-02-15 00:00:02');

-- Ordre 3: Mananjary, argent, Argent
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (2, 3, 'Argent', NULL, NULL, NULL, 6000000.00, 6000000.00, '2026-02-15 00:00:03');

-- Ordre 4: Toamasina, nature, Eau (L)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (1, 1, 'Eau (L)', 1000.00, 1500, 1500, NULL, NULL, '2026-02-15 00:00:04');

-- Ordre 5: Nosy Be, nature, Riz (kg)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (4, 1, 'Riz (kg)', 3000.00, 300, 300, NULL, NULL, '2026-02-15 00:00:05');

-- Ordre 6: Mananjary, materiel, Tôle
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (2, 2, 'Tôle', 25000.00, 80, 80, NULL, NULL, '2026-02-15 00:00:06');

-- Ordre 7: Nosy Be, argent, Argent
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (4, 3, 'Argent', NULL, NULL, NULL, 4000000.00, 4000000.00, '2026-02-15 00:00:07');

-- Ordre 8: Farafangana, materiel, Bâche
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (3, 2, 'Bâche', 15000.00, 150, 150, NULL, NULL, '2026-02-16 00:00:08');

-- Ordre 9: Mananjary, nature, Riz (kg)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (2, 1, 'Riz (kg)', 3000.00, 500, 500, NULL, NULL, '2026-02-15 00:00:09');

-- Ordre 10: Farafangana, argent, Argent
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (3, 3, 'Argent', NULL, NULL, NULL, 8000000.00, 8000000.00, '2026-02-16 00:00:10');

-- Ordre 11: Morondava, nature, Riz (kg)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (5, 1, 'Riz (kg)', 3000.00, 700, 700, NULL, NULL, '2026-02-16 00:00:11');

-- Ordre 12: Toamasina, argent, Argent
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (1, 3, 'Argent', NULL, NULL, NULL, 12000000.00, 12000000.00, '2026-02-16 00:00:12');

-- Ordre 13: Morondava, argent, Argent
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (5, 3, 'Argent', NULL, NULL, NULL, 10000000.00, 10000000.00, '2026-02-16 00:00:13');

-- Ordre 14: Farafangana, nature, Eau (L)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (3, 1, 'Eau (L)', 1000.00, 1000, 1000, NULL, NULL, '2026-02-15 00:00:14');

-- Ordre 15: Morondava, materiel, Bâche
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (5, 2, 'Bâche', 15000.00, 180, 180, NULL, NULL, '2026-02-16 00:00:15');

-- Ordre 16: Toamasina, materiel, groupe
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (1, 2, 'Groupe électrogène', 6750000.00, 3, 3, NULL, NULL, '2026-02-15 00:00:16');

-- Ordre 17: Toamasina, nature, Riz (kg)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (1, 1, 'Riz (kg)', 3000.00, 800, 800, NULL, NULL, '2026-02-16 00:00:17');

-- Ordre 18: Nosy Be, nature, Haricots
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (4, 1, 'Haricots', 4000.00, 200, 200, NULL, NULL, '2026-02-16 00:00:18');

-- Ordre 19: Mananjary, materiel, Clous (kg)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (2, 2, 'Clous (kg)', 8000.00, 60, 60, NULL, NULL, '2026-02-16 00:00:19');

-- Ordre 20: Morondava, nature, Eau (L)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (5, 1, 'Eau (L)', 1000.00, 1200, 1200, NULL, NULL, '2026-02-15 00:00:20');

-- Ordre 21: Farafangana, nature, Riz (kg)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (3, 1, 'Riz (kg)', 3000.00, 600, 600, NULL, NULL, '2026-02-16 00:00:21');

-- Ordre 22: Morondava, materiel, Bois
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (5, 2, 'Bois', 10000.00, 150, 150, NULL, NULL, '2026-02-15 00:00:22');

-- Ordre 23: Toamasina, materiel, Tôle
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (1, 2, 'Tôle', 25000.00, 120, 120, NULL, NULL, '2026-02-16 00:00:23');

-- Ordre 24: Nosy Be, materiel, Clous (kg)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (4, 2, 'Clous (kg)', 8000.00, 30, 30, NULL, NULL, '2026-02-16 00:00:24');

-- Ordre 25: Mananjary, nature, Huile (L)
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (2, 1, 'Huile (L)', 6000.00, 120, 120, NULL, NULL, '2026-02-16 00:00:25');

-- Ordre 26: Farafangana, materiel, Bois
INSERT INTO bngrc_besoins (ville_id, type_id, description, prix_unitaire, quantite, quantite_restante, montant, montant_restant, date_saisie)
VALUES (3, 2, 'Bois', 10000.00, 100, 100, NULL, NULL, '2026-02-15 00:00:26');

-- ============================================================
-- DONS (basé sur data2.txt)
-- Nature (type_id=1), Materiel (type_id=2) → quantite/quantite_restante
-- Argent (type_id=3) → montant/montant_restant
-- ============================================================

-- Dons Argent (type_id=3)
INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (3, 'Argent', 5000000.00, 5000000.00, NULL, NULL, '2026-02-16 00:00:01');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (3, 'Argent', 3000000.00, 3000000.00, NULL, NULL, '2026-02-16 00:00:02');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (3, 'Argent', 4000000.00, 4000000.00, NULL, NULL, '2026-02-17 00:00:03');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (3, 'Argent', 1500000.00, 1500000.00, NULL, NULL, '2026-02-17 00:00:04');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (3, 'Argent', 6000000.00, 6000000.00, NULL, NULL, '2026-02-17 00:00:05');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (3, 'Argent', 20000000.00, 20000000.00, NULL, NULL, '2026-02-19 00:00:14');

-- Dons Nature (type_id=1)
INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (1, 'Riz (kg)', NULL, NULL, 400, 400, '2026-02-16 00:00:06');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (1, 'Eau (L)', NULL, NULL, 600, 600, '2026-02-16 00:00:07');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (1, 'Haricots', NULL, NULL, 100, 100, '2026-02-17 00:00:10');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (1, 'Riz (kg)', NULL, NULL, 2000, 2000, '2026-02-18 00:00:11');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (1, 'Eau (L)', NULL, NULL, 5000, 5000, '2026-02-18 00:00:13');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (1, 'Haricots', NULL, NULL, 88, 88, '2026-02-17 00:00:16');

-- Dons Materiel (type_id=2)
INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (2, 'Tôle', NULL, NULL, 50, 50, '2026-02-17 00:00:08');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (2, 'Bâche', NULL, NULL, 70, 70, '2026-02-17 00:00:09');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (2, 'Tôle', NULL, NULL, 300, 300, '2026-02-18 00:00:12');

INSERT INTO bngrc_dons (type_id, description, montant, montant_restant, quantite, quantite_restante, date_saisie)
VALUES (2, 'Bâche', NULL, NULL, 500, 500, '2026-02-19 00:00:15');

-- ============================================================
-- FIN DU SCRIPT
-- Total: 26 besoins, 16 dons insérés
-- ============================================================
