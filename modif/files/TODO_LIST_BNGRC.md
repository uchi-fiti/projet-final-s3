# ✅ TODO LIST – Projet BNGRC - Gestion des Dons aux Sinistrés

---

## 🎯 Projet Final S3 - Février 2026
**Durée**: 26h  
**Début**: 16 fév 26 à 13h  
**Fin**: 17 fév 26 à 16h  
**Groupe**: 3 personnes

---

## 📊 BASE DE DONNÉES

### 🗄️ Création des tables
- [x] Créer la base de données `bngrc`
- [x] Table `villes` (id, nom, region, created_at, updated_at)
- [x] Table `besoins` (id, ville_id, type_besoin, designation, quantite, prix_unitaire, created_at, updated_at)
- [ ] Table `dons` (id, ville_id, type_don, designation, quantite, prix_unitaire, donateur, date_saisie, created_at, updated_at)
- [ ] Table `attributions` (id, besoin_id, don_id, quantite_attribuee, date_attribution)

### 📝 Données de test
- [x] Insérer 8 villes de Madagascar
- [x] Créer 8 besoins de test (nature, matériau, argent)
- [ ] Créer des dons de test
- [ ] Simuler quelques attributions

---

## 🔧 BACKEND - FlightPHP

### ✅ CRUD BESOINS (COMPLET)

#### CREATE - Créer un besoin
- [x] Route GET `/besoins/create`
- [x] Méthode `create()` dans BesoinController
- [x] Vue formulaire de création (`besoins/create.php`)
- [x] Liste déroulante des villes
- [x] Sélection du type de besoin (nature/materiau/argent)
- [x] Champ désignation
- [x] Champ quantité (avec validation > 0)
- [x] Champ prix unitaire (avec validation > 0)
- [x] Calcul automatique du montant total (JavaScript)
- [x] Route POST `/besoins/store`
- [x] Méthode `store()` dans BesoinController
- [x] Validation des données
- [x] Insertion en base de données
- [x] Redirection avec message de succès

#### READ - Lire/Afficher les besoins
- [x] Route GET `/besoins`
- [x] Méthode `index()` dans BesoinController
- [x] Vue liste des besoins (`besoins/index.php`)
- [x] Affichage en tableau responsive
- [x] Affichage ville avec nom
- [x] Badge coloré pour le type de besoin
- [x] Calcul et affichage du montant total par ligne
- [x] Total général de tous les besoins
- [x] Messages de notification (succès/erreur)
- [x] Bouton "Nouveau Besoin"
- [x] Icônes pour actions (modifier/supprimer)

#### UPDATE - Modifier un besoin
- [x] Route GET `/besoins/edit/{id}`
- [x] Méthode `edit()` dans BesoinController
- [x] Vue formulaire de modification (`besoins/edit.php`)
- [x] Pré-remplissage des champs
- [x] Calcul automatique du montant total
- [x] Route POST `/besoins/update/{id}`
- [x] Méthode `update()` dans BesoinController
- [x] Validation des données
- [x] Mise à jour en base
- [x] Gestion du timestamp `updated_at`
- [x] Redirection avec message de succès

#### DELETE - Supprimer un besoin
- [x] Route POST `/besoins/delete/{id}`
- [x] Méthode `delete()` dans BesoinController
- [x] Modal de confirmation Bootstrap
- [x] Suppression en base de données
- [x] Redirection avec message de succès
- [x] Protection contre suppression accidentelle

#### Fichiers créés
- [x] `app/controllers/BesoinController.php`
- [x] `app/views/besoins/index.php`
- [x] `app/views/besoins/create.php`
- [x] `app/views/besoins/edit.php`
- [x] Mise à jour `app/config/routes.php`
- [x] Mise à jour `app/config/services.php`
- [x] Script SQL `database_schema.sql`

---

### 🎁 CRUD DONS (À FAIRE)

#### CREATE - Créer un don
- [ ] Route GET `/dons/create`
- [ ] Méthode `create()` dans DonController
- [ ] Vue formulaire de création
- [ ] Sélection ville (optionnelle)
- [ ] Type de don
- [ ] Désignation
- [ ] Quantité
- [ ] Prix unitaire
- [ ] Nom du donateur
- [ ] Route POST `/dons/store`
- [ ] Validation et insertion

#### READ - Lire/Afficher les dons
- [ ] Route GET `/dons`
- [ ] Méthode `index()` dans DonController
- [ ] Vue liste des dons
- [ ] Affichage en tableau
- [ ] Total des dons

#### UPDATE - Modifier un don
- [ ] Route GET `/dons/edit/{id}`
- [ ] Route POST `/dons/update/{id}`
- [ ] Formulaire de modification

#### DELETE - Supprimer un don
- [ ] Route POST `/dons/delete/{id}`
- [ ] Confirmation de suppression

---

### 🔄 SYSTÈME D'ATTRIBUTION (À FAIRE)

#### Règles de dispatch
- [ ] Attribution par ordre de date de saisie
- [ ] Un don peut couvrir plusieurs besoins
- [ ] Un besoin peut être couvert par plusieurs dons
- [ ] Calcul de la quantité restante

#### Fonctionnalités
- [ ] Algorithme d'attribution automatique
- [ ] Page de visualisation des attributions
- [ ] Historique des attributions
- [ ] Annulation d'une attribution

---

### 📈 TABLEAU DE BORD (À FAIRE)

- [ ] Page d'accueil `/`
- [ ] Liste des villes avec besoins
- [ ] Montant total des besoins par ville
- [ ] Montant total des dons par ville
- [ ] Dons attribués par ville
- [ ] Besoins restants par ville
- [ ] Taux de couverture (%)
- [ ] Graphiques statistiques (optionnel)

---

## 🎨 FRONTEND - Interface

### Navigation
- [x] Barre de navigation Bootstrap
- [x] Logo BNGRC
- [x] Menu principal (Tableau de bord, Besoins, Dons, Villes)
- [x] Design responsive

### Design
- [x] Utilisation de Bootstrap 5
- [x] Icônes Bootstrap Icons
- [x] Thème cohérent (couleurs BNGRC)
- [x] Messages de notification
- [x] Modals de confirmation
- [x] Formulaires validés

### Pages CRUD Besoins
- [x] Liste avec tableau responsive
- [x] Badges colorés par type
- [x] Calculs automatiques
- [x] Formulaires avec validation
- [x] Messages de feedback

### Pages à créer
- [ ] Page tableau de bord
- [ ] Pages CRUD dons
- [ ] Page gestion villes
- [ ] Page attributions

---

## 🔐 SÉCURITÉ & QUALITÉ

- [x] Validation des données (besoins)
- [ ] Protection CSRF
- [x] Requêtes préparées (PDO)
- [x] Échappement XSS (htmlspecialchars)
- [ ] Gestion des erreurs
- [ ] Logs d'activité

---

## 📦 LIVRABLES

### Code
- [x] CRUD Besoins fonctionnel
- [ ] CRUD Dons
- [ ] Système d'attribution
- [ ] Tableau de bord
- [ ] Code propre et commenté
- [ ] Respect du MVC

### Base de données
- [x] Schéma SQL
- [x] Données de test
- [ ] Documentation des relations

### Documentation
- [x] README CRUD Besoins
- [ ] README général du projet
- [ ] Guide d'installation
- [ ] Guide utilisateur

### Déploiement
- [ ] Configuration serveur ITU
- [ ] Upload des fichiers
- [ ] Configuration base de données
- [ ] Tests de fonctionnement
- [ ] URL de démonstration

---

## 🎯 PRIORITÉS

### Priorité HAUTE (Obligatoire)
1. ✅ CRUD Besoins (TERMINÉ)
2. ⏳ CRUD Dons (EN COURS)
3. ⏳ Système d'attribution
4. ⏳ Tableau de bord

### Priorité MOYENNE
5. ⏳ Gestion des villes
6. ⏳ Design amélioré
7. ⏳ Documentation complète

### Priorité BASSE (Bonus)
8. ⏳ Graphiques statistiques
9. ⏳ Export Excel/PDF
10. ⏳ Système de notifications

---

## 📋 CHECKLIST FINALE

### Tests
- [x] CRUD Besoins fonctionne
- [ ] CRUD Dons fonctionne
- [ ] Attribution fonctionne
- [ ] Tableau de bord affiche les bonnes données
- [ ] Pas d'erreur PHP
- [ ] Pas d'erreur SQL
- [ ] Design responsive

### Validation
- [ ] Tous les besoins peuvent être saisis
- [ ] Tous les dons peuvent être saisis
- [ ] L'attribution se fait correctement
- [ ] Les calculs sont justes
- [ ] Les redirections fonctionnent

### Déploiement
- [ ] Code sur serveur ITU
- [ ] Base de données configurée
- [ ] Application accessible
- [ ] Tests sur le serveur

---

## 👥 RÉPARTITION DES TÂCHES

### Personne 1
- ✅ CRUD Besoins (TERMINÉ)
- [ ] Tests CRUD Besoins

### Personne 2
- [ ] CRUD Dons
- [ ] Tests CRUD Dons

### Personne 3
- [ ] Système d'attribution
- [ ] Tableau de bord

### Tous ensemble
- [ ] Design et CSS
- [ ] Tests finaux
- [ ] Documentation
- [ ] Déploiement

---

## 📅 PLANNING

### Jour 1 (16 fév - 13h → 00h)
- [x] Setup projet
- [x] Base de données
- [x] CRUD Besoins
- [ ] CRUD Dons (50%)

### Jour 2 (17 fév - 08h → 16h)
- [ ] CRUD Dons (complet)
- [ ] Système d'attribution
- [ ] Tableau de bord
- [ ] Tests et corrections
- [ ] Déploiement
- [ ] Validation finale

---

**Note**: Cette TODO list est mise à jour régulièrement. Cochez ✅ les tâches terminées et ajoutez ⏳ pour celles en cours.
