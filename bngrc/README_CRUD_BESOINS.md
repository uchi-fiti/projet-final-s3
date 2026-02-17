# CRUD Besoins - Application BNGRC

## 📋 Description

Ce CRUD permet de gérer les besoins des sinistrés dans le cadre du projet BNGRC (Bureau National de Gestion des Risques et Catastrophes). L'application permet de saisir, modifier, consulter et supprimer les besoins par ville.

## 🎯 Fonctionnalités

### ✅ Create (Créer)
- Formulaire de saisie d'un nouveau besoin
- Sélection de la ville
- Choix du type de besoin (nature, matériau, argent)
- Saisie de la désignation, quantité et prix unitaire
- Calcul automatique du montant total

### 📖 Read (Lire)
- Liste de tous les besoins avec filtres
- Affichage des informations complètes
- Calcul du montant total par besoin
- Total général de tous les besoins

### ✏️ Update (Modifier)
- Formulaire de modification pré-rempli
- Mise à jour des informations
- Historique des modifications

### 🗑️ Delete (Supprimer)
- Suppression avec confirmation
- Protection contre les suppressions accidentelles

## 📦 Installation

### 1. Configuration de la base de données

Créez la base de données :

```bash
mysql -u root -p
```

```sql
CREATE DATABASE bngrc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bngrc;
```

Puis importez le schéma :

```bash
mysql -u root -p bngrc < database_schema.sql
```

### 2. Configuration de l'application

Copiez et modifiez le fichier de configuration :

```bash
cp app/config/config_sample.php app/config/config.php
```

Éditez `app/config/config.php` et configurez la base de données :

```php
'database' => [
    'host'     => '127.0.0.1',
    'dbname'   => 'bngrc',
    'user'     => 'root',
    'password' => 'votre_mot_de_passe',
],
```

### 3. Installation des fichiers du CRUD

Copiez les fichiers dans votre projet :

```bash
# Contrôleur
cp BesoinController.php app/controllers/

# Vues
mkdir -p app/views/besoins
cp besoins_index.php app/views/besoins/index.php
cp besoins_create.php app/views/besoins/create.php
cp besoins_edit.php app/views/besoins/edit.php

# Routes
cp routes_updated.php app/config/routes.php

# Services (base de données activée)
cp services_updated.php app/config/services.php
```

### 4. Structure de la base de données

#### Table `villes`
```sql
- id (SERIAL PRIMARY KEY)
- nom (VARCHAR)
- region (VARCHAR)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### Table `besoins`
```sql
- id (SERIAL PRIMARY KEY)
- ville_id (INT) → FK vers villes
- type_besoin (VARCHAR) → 'nature', 'materiau', 'argent'
- designation (VARCHAR)
- quantite (DECIMAL)
- prix_unitaire (DECIMAL)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

## 🚀 Utilisation

### Accéder à la liste des besoins
```
http://localhost/besoins
```

### Créer un nouveau besoin
1. Cliquez sur "Nouveau Besoin"
2. Remplissez le formulaire :
   - Sélectionnez une ville
   - Choisissez le type de besoin
   - Saisissez la désignation (ex: Riz, Tôle, etc.)
   - Entrez la quantité
   - Entrez le prix unitaire
3. Le montant total se calcule automatiquement
4. Cliquez sur "Enregistrer"

### Modifier un besoin
1. Dans la liste, cliquez sur l'icône crayon (✏️)
2. Modifiez les informations souhaitées
3. Cliquez sur "Mettre à jour"

### Supprimer un besoin
1. Dans la liste, cliquez sur l'icône corbeille (🗑️)
2. Confirmez la suppression dans la popup
3. Le besoin est définitivement supprimé

## 🎨 Interface

L'interface utilise Bootstrap 5 pour un design moderne et responsive :
- Navigation claire
- Tableaux responsives
- Formulaires avec validation
- Messages de notification
- Modals de confirmation
- Icônes Bootstrap Icons

## 📱 Responsive Design

L'application est entièrement responsive et fonctionne sur :
- 💻 Desktop
- 📱 Tablettes
- 📱 Smartphones

## 🛠️ Technologies utilisées

- **Backend**: PHP 8+ avec FlightPHP
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework CSS**: Bootstrap 5
- **Base de données**: MySQL/MariaDB
- **Icônes**: Bootstrap Icons

## 📈 Améliorations possibles

- [ ] Pagination de la liste
- [ ] Filtres avancés (par ville, type, montant)
- [ ] Export Excel/PDF
- [ ] Graphiques statistiques
- [ ] Historique des modifications
- [ ] Validation côté client améliorée
- [ ] API REST pour intégration mobile
- [ ] Système de notifications
- [ ] Gestion des permissions utilisateurs

## 👥 Auteurs

Projet réalisé dans le cadre du cours S3 - ITU

## 📄 Licence

Ce projet est développé à des fins éducatives.
