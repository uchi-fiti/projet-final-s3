<?php $baseUrl = BASE_URL;
echo $baseUrl; ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Attributions</title>
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
                <a class="nav-link" href="<?= $baseUrl ?>/crud/dons">
                    <i class="bi bi-gift"></i> Dons
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $baseUrl ?>/attributions">
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
                <span class="fw-semibold">Attributions / Simulation</span>
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
                <h1>Attributions</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Attributions</li>
                    </ol>
                </nav>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer" class="mb-3"></div>

            <!-- Simulation Button + Revert -->
            <div class="mb-4 d-flex gap-2 align-items-center">
                <a href="<?= $baseUrl ?>/dispatch/simulate" class="text-decoration-none">
                    <button class="btn btn-accent" id="btnSimulation">
                        <i class="bi bi-play-circle me-1"></i> Lancer simulation
                    </button>
                </a>

                <button class="btn btn-outline-danger" id="btnRevert">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Initialiser données
                </button>
            </div>

            <!-- Table -->
            <div class="table-container" id="simulationResults">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="table-title mb-0">Résultats des attributions</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Don</th>
                                <th>Ville</th>
                                <th>Besoin</th>
                                <th>Quantité attribuée</th>
                                <th>Montant attribué</th>
                                <th>Date attribution</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($attributions)): ?>
                                <?php foreach ($attributions as $i => $attr): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($attr['don_description'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($attr['ville_nom'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($attr['besoin_description'] ?? '—') ?></td>
                                        <td><?= $attr['quantite_attribuee'] ? htmlspecialchars($attr['quantite_attribuee']) : '—' ?></td>
                                        <td><?= $attr['montant_attribue'] ? number_format($attr['montant_attribue'], 0, ',', ' ') . ' Ar' : '—' ?></td>
                                        <td><?= date('Y-m-d', strtotime($attr['date_attribution'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Aucune attribution trouvée. Lancez une simulation.</td>
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
    <script>
    document.getElementById('btnRevert').addEventListener('click', function () {
        if (!confirm('Confirmer : revenir au dernier partage (annuler la dernière simulation) ?')) return;
        fetch('<?= $baseUrl ?>/attributions/revert-last', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            const c = document.getElementById('alertContainer');
            if (data.ok) {
                c.innerHTML = '<div class="alert alert-success">Initialisation effectuée — la page va se recharger.</div>';
                setTimeout(()=> location.reload(), 700);
            } else {
                c.innerHTML = '<div class="alert alert-danger">Erreur : ' + (data.message || 'Échec') + '</div>';
            }
        })
        .catch(()=> {
            document.getElementById('alertContainer').innerHTML = '<div class="alert alert-danger">Erreur réseau.</div>';
        });
    });
    </script>
</body>
</html>
