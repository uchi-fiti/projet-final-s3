
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
-- ======================
CREATE TABLE bngrc_besoins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    type_id INT NOT NULL,
    description VARCHAR(150),
    prix_unitaire DECIMAL(10,2) NOT NULL,
    quantite INT NOT NULL,
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
-- ======================
CREATE TABLE bngrc_dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_id INT NOT NULL,
    description VARCHAR(150),
    montant_total DECIMAL(12,2),
    quantite INT,
    date_saisie DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_don_type
        FOREIGN KEY (type_id)
        REFERENCES bngrc_types_besoins(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ======================
-- TABLE ATTRIBUTIONS
-- ======================
CREATE TABLE bngrc_attributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL, 
    don_id INT NOT NULL,
    quantite_attribuee INT,
    montant_attribue DECIMAL(12,2),
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
