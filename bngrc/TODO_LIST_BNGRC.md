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

... (liste abrégée dans le fichier original)
