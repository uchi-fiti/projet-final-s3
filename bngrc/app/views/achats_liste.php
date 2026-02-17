<?php $baseUrl = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Liste des achats</title>
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
            <li class="nav-item">
                <a class="nav-link" href="<?= $baseUrl ?>/besoins">
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
                <a class="nav-link active" href="<?= $baseUrl ?>/besoins-restants">
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
                <span class="fw-semibold">Liste des achats</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="text-muted me-2" style="font-size:0.82rem;">Hello</span>
                <i class="bi bi-person-circle" style="font-size:1.3rem;"></i>
            </div>
        </nav>

        <!-- Content Area -->
        <div class="content-area">

            <!-- Page Header -->
            <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
                <h1>Liste des achats</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/besoins-restants">Besoins restants</a></li>
                        <li class="breadcrumb-item active">Achats</li>
                    </ol>
                </nav>
            </div>

            <!-- Filtre par ville -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= $baseUrl ?>/achats" class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label for="ville_id" class="form-label mb-0 fw-semibold">Filtrer par ville</label>
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <select name="ville_id" id="ville_id" class="form-select form-select-sm">
                                <option value="">-- Toutes les villes --</option>
                                <?php foreach ($villes as $v): ?>
                                    <option value="<?= $v['id'] ?>" <?= (isset($ville_id) && $ville_id == $v['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($v['nom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-accent">
                                <i class="bi bi-funnel me-1"></i>Valider
                            </button>
                        </div>
                        <?php if (!empty($ville_id)): ?>
                            <div class="col-auto">
                                <a href="<?= $baseUrl ?>/achats" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Réinitialiser
                                </a>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="table-title mb-0">Achats effectués</h6>
                    <a href="<?= $baseUrl ?>/besoins-restants" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i>Retour aux besoins restants
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Ville</th>
                                <th>Type</th>
                                <th>Besoin</th>
                                <th>Quantité</th>
                                <th>Montant unitaire</th>
                                <th>Montant base</th>
                                <th>Frais (%)</th>
                                <th>Montant frais</th>
                                <th>Montant total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($achats)): ?>
                                <?php foreach ($achats as $a): ?>
                                    <tr>
                                        <td><?= $a['id'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($a['date_achat'])) ?></td>
                                        <td><?= htmlspecialchars($a['ville_nom']) ?></td>
                                        <td><?= htmlspecialchars($a['type_nom']) ?></td>
                                        <td><?= htmlspecialchars($a['besoin_description']) ?></td>
                                        <td><?= $a['quantite'] ?></td>
                                        <td><?= number_format($a['montant_unitaire'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format($a['montant_base'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format($a['frais_pourcentage'], 1, ',', ' ') ?>%</td>
                                        <td><?= number_format($a['montant_frais'], 0, ',', ' ') ?> Ar</td>
                                        <td class="fw-semibold"><?= number_format($a['montant_total'], 0, ',', ' ') ?> Ar</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Aucun achat effectué.</td>
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

    <script src="<?= $baseUrl ?>/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $baseUrl ?>/js/app.js"></script>
</body>
</html>
