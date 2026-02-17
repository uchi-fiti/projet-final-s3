DELETE FROM bngrc_attributions;

-- Reset besoins : quantite_restante pour Nature/Materiaux, montant_restant pour Argent
UPDATE bngrc_besoins SET quantite_restante = quantite WHERE quantite IS NOT NULL;
UPDATE bngrc_besoins SET montant_restant = montant WHERE montant IS NOT NULL;

-- Reset dons : quantite_restante pour Nature/Materiaux, montant_restant pour Argent
UPDATE bngrc_dons SET quantite_restante = quantite WHERE quantite IS NOT NULL;
UPDATE bngrc_dons SET montant_restant = montant WHERE montant IS NOT NULL;
