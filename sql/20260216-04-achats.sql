
UPDATE dons SET montant_restant = montant_total;

-- ======================
-- TABLE ACHATS
-- (Achats effectués avec des dons de type Argent)
-- ======================
CREATE TABLE achats (
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
        REFERENCES besoins(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ======================
-- DONNEES DE TEST : Don en argent
-- ======================
INSERT INTO dons (type_id, description, montant, quantite, quantite_restante, montant_restant)
VALUES (
    (SELECT id FROM types_besoins WHERE nom = 'Argent'),
    'Don monétaire - Banque Mondiale',
    50000000.00,
    1,
    1,
    50000000.00
);
