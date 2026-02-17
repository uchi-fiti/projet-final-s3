<?php $baseUrl = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Récapitulation</title>
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
                <a class="nav-link" href="<?= $baseUrl ?>/besoins-restants">
                    <i class="bi bi-cart"></i> Achats
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $baseUrl ?>/recap">
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
                <span class="fw-semibold">Récapitulation des besoins</span>
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
                <h1>Récapitulation</h1>
                <div class="d-flex align-items-center gap-2">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/dashboard">Accueil</a></li>
                            <li class="breadcrumb-item active">Récapitulation</li>
                        </ol>
                    </nav>
                    <button id="btn-refresh" class="btn btn-sm btn-accent" type="button">
                        <i class="bi bi-arrow-clockwise me-1"></i>Actualiser
                    </button>
                </div>
            </div>

            <!-- Stat Cards -->
            <div class="row g-3 mb-4">
                <!-- Besoins totaux -->
                <div class="col-sm-6 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-primary-subtle me-3">
                                <i class="bi bi-clipboard-data"></i>
                            </div>
                            <div>
                                <div class="stat-value" id="val-totaux"><?= number_format($besoins_totaux, 0, ',', ' ') ?> Ar</div>
                                <div class="stat-label">Besoins totaux (montant)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Besoins satisfaits -->
                <div class="col-sm-6 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-success-subtle me-3">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div>
                                <div class="stat-value" id="val-satisfaits"><?= number_format($besoins_satisfaits, 0, ',', ' ') ?> Ar</div>
                                <div class="stat-label">Besoins satisfaits (montant)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Besoins restants -->
                <div class="col-sm-6 col-xl-4">
                    <div class="card stat-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="stat-icon bg-warning-subtle me-3">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="stat-value" id="val-restants"><?= number_format($besoins_restants, 0, ',', ' ') ?> Ar</div>
                                <div class="stat-label">Besoins restants (montant)</div>
                            </div>
                        </div>
                    </div>
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
    <script>
    (function () {
        const btn = document.getElementById('btn-refresh');
        const apiUrl = '<?= $baseUrl ?>/api/recap';

        function formatNumber(n) {
            return Math.round(n).toLocaleString('fr-FR').replace(/\u202F/g, ' ') + ' Ar';
        }

        btn.addEventListener('click', function () {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span>Chargement…';

            fetch(apiUrl)
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    document.getElementById('val-totaux').textContent     = formatNumber(data.besoins_totaux);
                    document.getElementById('val-satisfaits').textContent = formatNumber(data.besoins_satisfaits);
                    document.getElementById('val-restants').textContent   = formatNumber(data.besoins_restants);
                })
                .catch(function (err) {
                    console.error('Erreur lors de l\'actualisation', err);
                    alert('Erreur lors de l\'actualisation.');
                })
                .finally(function () {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Actualiser';
                });
        });
    })();
    </script>
</body>
</html>
