ALTER TABLE bngrc_besoins
ADD COLUMN quantite_restante INT NOT NULL;
UPDATE bngrc_besoins
SET quantite_restante = quantite;
ALTER TABLE bngrc_dons
ADD COLUMN quantite_restante INT NOT NULL;
UPDATE bngrc_dons
SET quantite_restante = quantite;
