ALTER TABLE besoins
ADD COLUMN quantite_restante INT NOT NULL;
UPDATE besoins
SET quantite_restante = quantite;
ALTER TABLE dons
ADD COLUMN quantite_restante INT NOT NULL;
UPDATE dons
SET quantite_restante = quantite;
