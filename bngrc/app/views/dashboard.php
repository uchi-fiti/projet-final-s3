<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Dashboard</title>
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
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
                <a class="nav-link active" href="/dashboard">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" href="/regions">
                    <i class="bi bi-map"></i> Régions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/villes">
                    <i class="bi bi-building"></i> Villes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/types">
                    <i class="bi bi-tags"></i> Types de besoins
                </a>
            </li> -->
            <li class="nav-item">
                <a class="nav-link" href="/besoins">
                    <i class="bi bi-clipboard-data"></i> Besoins
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/crud/dons">
                    <i class="bi bi-gift"></i> Dons
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/attributions">
                    <i class="bi bi-arrow-left-right"></i> Attributions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/besoins-restants">
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
                <span class="fw-semibold">Tableau de bord</span>
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
                <h1>Dashboard</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </nav>
            </div>

            <!-- Dispatch Status Message -->
            <?php if (isset($dispatch_ok)): ?>
                <?php if ($dispatch_ok): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i><?= htmlspecialchars($dispatch_message ?? 'La simulation de dispatch a été effectuée avec succès.') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>Erreur lors de la simulation : <?= htmlspecialchars($dispatch_error ?? 'Erreur inconnue') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Stat Cards -->
            <div class="row g-3 mb-4">
                <!-- Total besoins -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-accent me-3">
                                <i class="bi bi-clipboard-data"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?= number_format($stats['total_besoins'] ?? 0, 0, ',', ' ') ?> Ar</div>
                                <div class="stat-label">Total besoins</div>
                            </div> 
                        </div>
                    </div>
                </div>
                <!-- Total dons -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-info-subtle me-3">
                                <i class="bi bi-gift"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?= number_format($stats['total_dons'] ?? 0, 0, ',', ' ') ?> Ar</div>
                                <div class="stat-label">Total dons</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Total distribué -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-warning-subtle me-3">
                                <i class="bi bi-box-seam"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?= number_format($stats['total_distribue'] ?? 0, 0, ',', ' ') ?> Ar</div>
                                <div class="stat-label">Total distribué</div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Taux de couverture -->
                <div class="col-sm-6 col-xl-3">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-success-subtle me-3">
                                <i class="bi bi-pie-chart"></i>
                            </div>
                            <div>
                                <div class="stat-value"><?= $stats['taux_couverture'] ?? 0 ?>%</div>
                                <div class="stat-label">Taux de couverture</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <a href="/besoins-restants" class="btn btn-outline-primary">
                    <i class="bi bi-cart-plus me-1"></i>Voir les besoins restants / Achats
                </a>
            </div>

            <!-- Table: Liste des villes -->
            <div class="table-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="table-title mb-0">Liste des villes</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Ville</th>
                                <th>Besoin total</th>
                                <th>Don attribué</th>
                                <th>Restant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($villes)): ?>
                                <?php foreach ($villes as $ville): ?>
                                    <?php
                                        $restant = $ville['besoin_total'] - $ville['total_attribue'];
                                        if ($ville['besoin_total'] == 0) {
                                            $badgeClass = 'badge-couvert';
                                            $statut = 'Aucun besoin';
                                        } elseif ($restant <= 0) {
                                            $badgeClass = 'badge-couvert';
                                            $statut = 'Couvert';
                                        } elseif ($ville['total_attribue'] > 0) {
                                            $badgeClass = 'badge-partiel';
                                            $statut = 'Partiel';
                                        } else {
                                            $badgeClass = 'badge-non-couvert';
                                            $statut = 'Non couvert';
                                        }
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ville['ville']) ?></td>
                                        <td><?= number_format($ville['besoin_total'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format($ville['total_attribue'], 0, ',', ' ') ?> Ar</td>
                                        <td><?= number_format(max(0, $restant), 0, ',', ' ') ?> Ar</td>
                                        <td><span class="badge <?= $badgeClass ?>"><?= $statut ?></span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Aucune ville trouvée</td>
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

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>
