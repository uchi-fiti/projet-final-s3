<?php $baseUrl = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Besoin - BNGRC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $baseUrl ?>/">
                <i class="bi bi-house-heart"></i> BNGRC - Gestion des Dons
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= $baseUrl ?>/besoins">Besoins</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/dons">Dons</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>/villes">Villes</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Modifier le Besoin #<?= htmlspecialchars($besoin['id']) ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= $baseUrl ?>/besoins/update/<?= $besoin['id'] ?>" id="besoinForm">
                            <!-- Ville -->
                            <div class="mb-3">
                                <label for="ville_id" class="form-label">
                                    <i class="bi bi-geo-alt"></i> Ville <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="ville_id" name="ville_id" required>
                                    <option value="">-- Sélectionner une ville --</option>
                                    <?php foreach ($villes as $ville): ?>
                                        <option value="<?= $ville['id'] ?>" 
                                                <?= $ville['id'] == $besoin['ville_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($ville['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Type de besoin -->
                            <div class="mb-3">
                                <label for="type_besoin" class="form-label">
                                    <i class="bi bi-tag"></i> Type de besoin <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="type_besoin" name="type_besoin" required>
                                    <option value="">-- Sélectionner un type --</option>
                                    <option value="nature" <?= $besoin['type_besoin'] === 'nature' ? 'selected' : '' ?>>
                                        Nature (riz, huile, ...)
                                    </option>
                                    <option value="materiau" <?= $besoin['type_besoin'] === 'materiau' ? 'selected' : '' ?>>
                                        Matériau (tôle, clou, ...)
                                    </option>
                                    <option value="argent" <?= $besoin['type_besoin'] === 'argent' ? 'selected' : '' ?>>
                                        Argent
                                    </option>
                                </select>
                            </div>

                            <!-- Désignation -->
                            <div class="mb-3">
                                <label for="designation" class="form-label">
                                    <i class="bi bi-pencil"></i> Désignation <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="designation" 
                                       name="designation" 
                                       value="<?= htmlspecialchars($besoin['designation']) ?>"
                                       placeholder="Ex: Riz, Huile, Tôle, etc."
                                       required>
                                <div class="form-text">
                                    Décrivez précisément le besoin
                                </div>
                            </div>

                            <!-- Quantité -->
                            <div class="mb-3">
                                <label for="quantite" class="form-label">
                                    <i class="bi bi-123"></i> Quantité <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="quantite" 
                                       name="quantite" 
                                       value="<?= htmlspecialchars($besoin['quantite']) ?>"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="Ex: 100"
                                       required>
                                <div class="form-text">
                                    Quantité nécessaire (kg, pièces, etc.)
                                </div>
                            </div>

                            <!-- Prix unitaire -->
                            <div class="mb-3">
                                <label for="prix_unitaire" class="form-label">
                                    <i class="bi bi-currency-exchange"></i> Prix unitaire (Ar) <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="prix_unitaire" 
                                       name="prix_unitaire" 
                                       value="<?= htmlspecialchars($besoin['prix_unitaire']) ?>"
                                       step="0.01"
                                       min="0.01"
                                       placeholder="Ex: 5000"
                                       required>
                                <div class="form-text">
                                    Prix unitaire en Ariary (ne change jamais)
                                </div>
                            </div>

                            <!-- Montant total (calculé automatiquement) -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="bi bi-calculator"></i> Montant total estimé
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control bg-light" 
                                           id="montant" 
                                           readonly 
                                           value="<?= number_format($besoin['quantite'] * $besoin['prix_unitaire'], 2, '.', ' ') ?>">
                                    <span class="input-group-text">Ar</span>
                                </div>
                            </div>

                            <hr>

                            <!-- Informations de traçabilité -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-plus"></i> Créé le : 
                                        <?= date('d/m/Y à H:i', strtotime($besoin['created_at'])) ?>
                                    </small>
                                </div>
                                <?php if (!empty($besoin['updated_at'])): ?>
                                    <div class="col-md-6 text-md-end">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-check"></i> Modifié le : 
                                            <?= date('d/m/Y à H:i', strtotime($besoin['updated_at'])) ?>
                                        </small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Boutons d'action -->
                            <div class="d-flex justify-content-between">
                                <a href="<?= $baseUrl ?>/besoins" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Retour
                                </a>
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save"></i> Mettre à jour
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Calcul automatique du montant total
        function calculerMontantTotal() {
            const quantite = parseFloat(document.getElementById('quantite').value) || 0;
            const prixUnitaire = parseFloat(document.getElementById('prix_unitaire').value) || 0;
            const montantTotal = quantite * prixUnitaire;
            
            document.getElementById('montant').value = montantTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
        }

        // Écouter les changements sur les champs quantité et prix unitaire
        document.getElementById('quantite').addEventListener('input', calculerMontantTotal);
        document.getElementById('prix_unitaire').addEventListener('input', calculerMontantTotal);

        // Validation du formulaire
        document.getElementById('besoinForm').addEventListener('submit', function(e) {
            const quantite = parseFloat(document.getElementById('quantite').value);
            const prixUnitaire = parseFloat(document.getElementById('prix_unitaire').value);
            
            if (quantite <= 0) {
                e.preventDefault();
                alert('La quantité doit être supérieure à 0');
                return false;
            }
            
            if (prixUnitaire <= 0) {
                e.preventDefault();
                alert('Le prix unitaire doit être supérieur à 0');
                return false;
            }
        });
    </script>
</body>
</html>
