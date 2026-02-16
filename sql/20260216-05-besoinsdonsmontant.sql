ALTER TABLE dons
CHANGE montant_total montant DECIMAL(12,2) NULL;
ALTER TABLE dons
ADD COLUMN montant_restant DECIMAL(12,2) NULL AFTER montant;
UPDATE dons
SET montant_restant = montant
WHERE montant IS NOT NULL;

ALTER TABLE besoins
ADD COLUMN montant DECIMAL(12,2) NULL AFTER quantite;
ALTER TABLE besoins
ADD COLUMN montant_restant DECIMAL(12,2) NULL AFTER montant;
UPDATE besoins
SET montant = quantite * prix_unitaire,
montant_restant = quantite_restante * prix_unitaire
where type_id = 3;

