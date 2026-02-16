ALTER TABLE bngrc_dons
CHANGE montant_total montant DECIMAL(12,2) NULL;
ALTER TABLE bngrc_dons
ADD COLUMN montant_restant DECIMAL(12,2) NULL AFTER montant;
UPDATE bngrc_dons
SET montant_restant = montant
WHERE montant IS NOT NULL;

ALTER TABLE bngrc_besoins
ADD COLUMN montant DECIMAL(12,2) NULL AFTER quantite;
ALTER TABLE bngrc_besoins
ADD COLUMN montant_restant DECIMAL(12,2) NULL AFTER montant;
UPDATE bngrc_besoins
SET montant = quantite * prix_unitaire,
montant_restant = quantite_restante * prix_unitaire
where type_id = 3;

