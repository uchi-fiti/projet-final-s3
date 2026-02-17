<?php $baseUrl = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Dons</title>
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
                <a class="nav-link" href="<?= $baseUrl ?>/besoins">
                    <i class="bi bi-clipboard-data"></i> Besoins
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $baseUrl ?>/crud/dons">
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
            <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/recap">
                    <i class="bi bi-calculator"></i> Récapitulation
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
                <span class="fw-semibold">Dons</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-muted me-2" style="font-size:0.82rem;">Hello</span>
                <i class="bi bi-person-circle" style="font-size:1.3rem;"></i>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">
            
            <!-- Success/Error Messages -->
            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
                <h1>Dons</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Dons</li>
                    </ol>
                </nav>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="table-title mb-0">Liste des dons</h6>
                    <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#modalDon">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter don
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Quantité / Montant</th>
                                <th>Qté restante</th>
                                <th>Montant restant</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($dons) && is_array($dons) && !empty($dons)): ?>
                                <?php foreach ($dons as $don): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($don['id']) ?></td>
                                        <td><?= htmlspecialchars($don['type_nom'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($don['description']) ?></td>
                                        <td>
                                            <?php if (!empty($don['montant'])): ?>
                                                <?= number_format($don['montant'], 0, ',', ' ') ?> Ar
                                            <?php endif; ?>
                                            <?php if (!empty($don['quantite'])): ?>
                                                <?php if (!empty($don['montant'])): ?><br><?php endif; ?>
                                                <?= htmlspecialchars($don['quantite']) ?> unités
                                            <?php endif; ?>
                                        </td>
                                        <td><?= ($don['quantite_restante'] !== null) ? (int)$don['quantite_restante'] : '—' ?></td>
                                        <td><?= ($don['montant_restant'] !== null) ? number_format((float)$don['montant_restant'], 0, ',', ' ') . ' Ar' : '—' ?></td>
                                        <td><?= date('Y-m-d', strtotime($don['date_saisie'])) ?></td>
                                        <td>
                                            <a href="<?= $baseUrl ?>/crud/dons?edit=<?= htmlspecialchars($don['id']) ?>" 
                                               class="btn btn-sm btn-outline-secondary btn-action me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="<?= $baseUrl ?>/dons/<?= htmlspecialchars($don['id']) ?>/delete" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce don ?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Aucun don trouvé</td>
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

    <!-- Modal: Ajouter / Modifier Don -->
    <div class="modal fade" id="modalDon" tabindex="-1" aria-labelledby="modalDonLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDonLabel">
                        <?= isset($editDon) && $editDon ? 'Modifier un don' : 'Ajouter un don' ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form id="formDon" method="POST" action="<?= BASE_URL . isset($editDon) && $editDon ? '/dons/' . htmlspecialchars($editDon['id']) . '/update' : '/dons/create' ?>">
                    <input type="hidden" id="donId" name="don_id" value="<?= isset($editDon) && $editDon ? htmlspecialchars($editDon['id']) : '' ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="typeDon" class="form-label">Type</label>
                            <select class="form-select" id="typeDon" name="type_id" required>
                                <option value="" disabled>Sélectionner un type</option>
                                <?php if (isset($types) && is_array($types)): ?>
                                    <?php foreach ($types as $type): ?>
                                        <option value="<?= htmlspecialchars($type['id']) ?>"
                                                <?= (isset($editDon) && $editDon && $editDon['type_id'] == $type['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($type['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="descriptionDon" class="form-label">Description</label>
                            <input type="text" class="form-control" id="descriptionDon" name="description" 
                                   placeholder="Ex: Riz, Aide financière..." 
                                   value="<?= isset($editDon) && $editDon ? htmlspecialchars($editDon['description']) : '' ?>"
                                   required>
                        </div>
                        <div class="mb-3">
                            <label for="montantTotal" class="form-label">Montant Total (Ar)</label>
                            <input type="number" class="form-control" id="montantTotal" name="montant" 
                                   placeholder="Ex: 5000000" step="0.01" min="0"
                                   value="<?= isset($editDon) && $editDon && $editDon['montant'] ? htmlspecialchars($editDon['montant']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="quantiteDon" class="form-label">Quantité</label>
                            <input type="number" class="form-control" id="quantiteDon" name="quantite" 
                                   placeholder="Ex: 100" min="0"
                                   value="<?= isset($editDon) && $editDon && $editDon['quantite'] ? htmlspecialchars($editDon['quantite']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label for="dateDon" class="form-label">Date</label>
                            <input type="date" class="form-control" id="dateDon" name="date_don" 
                                   value="<?= isset($editDon) && $editDon ? date('Y-m-d', strtotime($editDon['date_saisie'])) : date('Y-m-d') ?>"
                                   required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-accent btn-sm">
                            <?= isset($editDon) && $editDon ? 'Modifier' : 'Enregistrer' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>/js/app.js"></script>
    <script>
        // Auto-show modal if we're editing a don
        <?php if (isset($editDon) && $editDon): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const modal = new bootstrap.Modal(document.getElementById('modalDon'));
                modal.show();
            });
        <?php endif; ?>
        
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                    bsAlert.close();
                }, 5000);
            });
        });
    </script>
</body>
</html>
