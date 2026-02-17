##  Ce qui a été implémenté
- Route `GET /attributions` : page UI de simulation.
- Route `POST /attributions/simulate` : exécute l'algorithme de simulation (retour JSON, **sans** écrire en base).
- Route `POST /attributions/validate` : exécute l'algorithme et **insère** les attributions dans la table `attributions` (transactionnelle).
- Contrôleur : `app/controllers/AttributionController.php` (méthodes `index`, `simulate`, `validate`, `computeAllocations`).
- Vue : `app/views/attributions/index.php` (UI avec `fee_pct`, bouton **Simuler** et **Valider**, affichage des résultats via AJAX).
- Algorithme : attribution FIFO par date_saisie pour dons et besoins ; dons `argent` peuvent acheter besoins `nature`/`materiau` (frais configurable).

## Fichiers modifiés / ajoutés
- `app/config/routes.php` (routes ajoutées)
- `app/controllers/AttributionController.php` (nouveau)
- `app/views/attributions/index.php` (nouveau)

## Vérification / tests manuels réalisés
1. Ouvrir `/attributions` → la page charge correctement.
2. Cliquer **Simuler** (avec différents `fee_pct`) → retour JSON affiché, tableau rempli.
3. Cliquer **Valider** → enregistre les lignes dans `attributions` (DB transactionnelle).

## Tâches restantes / améliorations possibles
- [ ] Afficher noms de villes (au lieu d'IDs) dans le tableau de simulation.
- [ ] Empêcher double-validation ou détecter attributions déjà appliquées (idempotence).
- [ ] Stocker/archiver le `fee_pct` appliqué pour chaque attribution (audit/tracking).
- [ ] Endpoint pour annuler / rollback d'une attribution (undo).
- [ ] Ajouter tests unitaires/fonctionnels pour `computeAllocations()` et endpoints.
- [ ] Verrouillage/concurrence : gérer validations concurrentes (row locking si besoin).
- [ ] Validation serveur plus stricte (CSRF, validation `fee_pct`, sanitization).
- [ ] UI : pagination, filtrage par ville, export CSV des résultats de simulation.

##  Critères d'acceptation (Definition of Done)
- La simulation retourne sans write les attributions attendues.
- La validation insère correctement les enregistrements `attributions` (transaction commit).
- Les données calculées (montants et quantités) respectent les règles de gestion (FIFO, frais appliqués pour l'argent).

## Prochaines actions proposées
1. Prioriser les améliorations listées (préciser 2-3 éléments à livrer en priorité).
2. Ajouter tests automatisés pour l'algorithme et les routes API.
3. Implémenter annulation d'attribution et audit des frais.

---

Si tu veux, je peux :
- ajouter/implémenter immédiatement l'une des tâches listées, ou
- ouvrir une PR avec ces changements et tests.
