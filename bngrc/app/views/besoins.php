<?php $baseUrl = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Besoins</title>
    <link href="<?= $baseUrl ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= $baseUrl ?>/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-brand">
            <h5>BNGRC</h5>
            <small>Suivi des dons</small>
        </div>
        <ul class="nav flex-column mt-2">
            <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/dashboard">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/regions">
                    <i class="bi bi-map"></i> Régions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/villes">
                    <i class="bi bi-building"></i> Villes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/types">
                    <i class="bi bi-tags"></i> Types de besoins
                </a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link active" href="<?= $baseUrl ?>/besoins">
                    <i class="bi bi-clipboard-data"></i> Besoins
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/crud/dons">
                    <i class="bi bi-gift"></i> Dons
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/attributions">
                    <i class="bi bi-arrow-left-right"></i> Attributions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/besoins-restants">
                    <i class="bi bi-cart"></i> Achats
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div id="main-content">

        <!-- Top Navbar -->
        <nav class="top-navbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <button class="btn-toggle-sidebar me-3" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <span class="fw-semibold">Besoins</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-muted me-2" style="font-size:0.82rem;">Hello</span>
                <i class="bi bi-person-circle" style="font-size:1.3rem;"></i>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">

            <!-- Flash Messages -->
            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
                <h1>Besoins</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Besoins</li>
                    </ol>
                </nav>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="table-title mb-0">Liste des besoins</h6>
                    <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#modalBesoin">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter besoin
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ville</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Montant total</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($besoins)): ?>
                                <?php foreach ($besoins as $i => $besoin): ?>
                                    <?php
                                        $montant = $besoin['quantite'] * $besoin['prix_unitaire'];
                                        $attribue = $besoin['total_attribue_qty'] ?? 0;
                                        if ($attribue >= $besoin['quantite']) {
                                            $badgeClass = 'badge-couvert';
                                            $statut = 'Couvert';
                                        } elseif ($attribue > 0) {
                                            $badgeClass = 'badge-partiel';
                                            $statut = 'Partiel';
                                        } else {
                                            $badgeClass = 'badge-non-couvert';
                                            $statut = 'Non couvert';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($besoin['ville_nom'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($besoin['type_nom'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($besoin['description'] ?? '') ?></td>
                                        <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= htmlspecialchars($besoin['quantite']) ?></td>
                                        <td><?= number_format($montant, 0, ',', ' ') ?> Ar</td>
                                        <td><span class="badge <?= $badgeClass ?>"><?= $statut ?></span></td>
                                        <td>
                                            <a href="<?= $baseUrl ?>/besoins?edit=<?= $besoin['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="<?= $baseUrl ?>/besoins/<?= $besoin['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Supprimer ce besoin ?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center text-muted">Aucun besoin trouvé</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Footer -->
        <footer class="main-footer text-center">
             Projet BNGRC - Application de suivi des dons - Créée par ETU004171 - ETU003915 et ETU003968
        </footer>

    </div>

    <!-- Modal: Ajouter Besoin -->
    <div class="modal fade" id="modalBesoin" tabindex="-1" aria-labelledby="modalBesoinLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalBesoinLabel">Ajouter un besoin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="<?= $baseUrl ?>/besoins/create">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="villeBesoin" class="form-label">Ville</label>
                                <select class="form-select" id="villeBesoin" name="ville_id" required>
                                    <option value="" selected disabled>Sélectionner une ville</option>
                                    <?php if (!empty($villes)): ?>
                                        <?php foreach ($villes as $ville): ?>
                                            <option value="<?= $ville['id'] ?>"><?= htmlspecialchars($ville['nom']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="typeBesoin" class="form-label">Type</label>
                                <select class="form-select" id="typeBesoin" name="type_id" required>
                                    <option value="" selected disabled>Sélectionner un type</option>
                                    <?php if (!empty($types)): ?>
                                        <?php foreach ($types as $type): ?>
                                            <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['nom']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label for="descriptionBesoin" class="form-label">Description</label>
                                <input type="text" class="form-control" id="descriptionBesoin" name="description" placeholder="Ex: Riz, Tôles, etc." required>
                            </div>
                            <div class="col-md-6">
                                <label for="prixUnitaire" class="form-label">Prix unitaire (Ar)</label>
                                <input type="number" class="form-control" id="prixUnitaire" name="prix_unitaire" placeholder="0" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="quantite" class="form-label">Quantité</label>
                                <input type="number" class="form-control" id="quantite" name="quantite" placeholder="0" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-accent btn-sm">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php if (isset($_GET['edit']) && !empty($editBesoin)): ?>
    <!-- Modal: Modifier Besoin (auto-open) -->
    <div class="modal fade" id="modalBesoinEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le besoin</h5>
                    <a href="<?= $baseUrl ?>/besoins" class="btn-close" aria-label="Fermer"></a>
                </div>
                <form method="POST" action="<?= $baseUrl ?>/besoins/<?= $editBesoin['id'] ?>/update">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Ville</label>
                                <select class="form-select" name="ville_id" required>
                                    <?php foreach ($villes as $ville): ?>
                                        <option value="<?= $ville['id'] ?>" <?= $editBesoin['ville_id'] == $ville['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ville['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type</label>
                                <select class="form-select" name="type_id" required>
                                    <?php foreach ($types as $type): ?>
                                        <option value="<?= $type['id'] ?>" <?= $editBesoin['type_id'] == $type['id'] ? 'selected' : '' ?>><?= htmlspecialchars($type['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <input type="text" class="form-control" name="description" value="<?= htmlspecialchars($editBesoin['description'] ?? '') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prix unitaire (Ar)</label>
                                <input type="number" class="form-control" name="prix_unitaire" value="<?= $editBesoin['prix_unitaire'] ?>" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantité</label>
                                <input type="number" class="form-control" name="quantite" value="<?= $editBesoin['quantite'] ?>" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="<?= $baseUrl ?>/besoins" class="btn btn-secondary btn-sm">Annuler</a>
                        <button type="submit" class="btn btn-accent btn-sm">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('modalBesoinEdit')).show(); });</script>
    <?php endif; ?>

    <script src="<?= $baseUrl ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>/js/app.js"></script>
</body>
</html>
