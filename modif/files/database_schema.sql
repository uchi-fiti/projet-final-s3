-- ============================================
-- BNGRC - Base de données de gestion des dons
-- ============================================

-- Table des villes
CREATE TABLE IF NOT EXISTS villes (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT NULL
);

-- Table des besoins
CREATE TABLE IF NOT EXISTS besoins (
    id SERIAL PRIMARY KEY,
    ville_id INT NOT NULL,
    type_besoin VARCHAR(20) NOT NULL, -- 'nature', 'materiau', 'argent'
    designation VARCHAR(150) NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT NULL,
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE CASCADE
);

-- Table des dons
CREATE TABLE IF NOT EXISTS dons (
    id SERIAL PRIMARY KEY,
    ville_id INT,
    type_don VARCHAR(20) NOT NULL, -- 'nature', 'materiau', 'argent'
    designation VARCHAR(150) NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    donateur VARCHAR(200),
    date_saisie TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT NULL,
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE SET NULL
);

-- Table des attributions (dispatch)
CREATE TABLE IF NOT EXISTS attributions (
    id SERIAL PRIMARY KEY,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite_attribuee DECIMAL(10,2) NOT NULL,
    date_attribution TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE CASCADE,
    FOREIGN KEY (don_id) REFERENCES dons(id) ON DELETE CASCADE
);

-- Index pour optimiser les requêtes
CREATE INDEX IF NOT EXISTS idx_besoins_ville ON besoins(ville_id);
CREATE INDEX IF NOT EXISTS idx_dons_ville ON dons(ville_id);
CREATE INDEX IF NOT EXISTS idx_attributions_besoin ON attributions(besoin_id);
CREATE INDEX IF NOT EXISTS idx_attributions_don ON attributions(don_id);

-- ============================================
-- Données de test
-- ============================================

-- Insertion des villes
INSERT INTO villes (nom, region) VALUES
('Antananarivo', 'Analamanga'),
('Toamasina', 'Atsinanana'),
('Antsirabe', 'Vakinankaratra'),
('Mahajanga', 'Boeny'),
('Toliara', 'Atsimo-Andrefana'),
('Fianarantsoa', 'Haute Matsiatra'),
('Antsiranana', 'Diana'),
('Morondava', 'Menabe');

-- Insertion de quelques besoins de test
INSERT INTO besoins (ville_id, type_besoin, designation, quantite, prix_unitaire) VALUES
(1, 'nature', 'Riz', 500.00, 3000.00),
(1, 'nature', 'Huile', 100.00, 8000.00),
(1, 'materiau', 'Tôle', 200.00, 25000.00),
(2, 'nature', 'Riz', 800.00, 3000.00),
(2, 'materiau', 'Clou', 50.00, 500.00),
(3, 'nature', 'Haricot', 300.00, 4000.00),
(3, 'materiau', 'Bois', 100.00, 15000.00),
(4, 'argent', 'Aide financière', 1.00, 5000000.00);

-- Insertion de quelques dons de test
INSERT INTO dons (ville_id, type_don, designation, quantite, prix_unitaire, donateur) VALUES
(1, 'nature', 'Riz', 300.00, 3000.00, 'Association Caritas'),
(1, 'nature', 'Huile', 50.00, 8000.00, 'ONG Médecins du Monde'),
(2, 'materiau', 'Tôle', 100.00, 25000.00, 'Entreprise BTP Madagascar'),
(NULL, 'argent', 'Don en espèces', 1.00, 3000000.00, 'Donateur anonyme');

-- ============================================
-- Commentaires sur la structure
-- ============================================

/*
BESOINS:
- ville_id: Ville concernée par le besoin
- type_besoin: Type (nature/materiau/argent)
- designation: Description du besoin
- quantite: Quantité nécessaire
- prix_unitaire: Prix unitaire (fixe, ne change jamais)

DONS:
- ville_id: Ville bénéficiaire (peut être NULL si pas encore attribué)
- type_don: Type de don
- designation: Description du don
- quantite: Quantité donnée
- prix_unitaire: Valeur unitaire
- donateur: Nom du donateur
- date_saisie: Date de saisie du don

ATTRIBUTIONS:
- Gère la distribution des dons aux besoins
- Un don peut être partiellement attribué à plusieurs besoins
- Un besoin peut recevoir des dons de plusieurs sources
*/
