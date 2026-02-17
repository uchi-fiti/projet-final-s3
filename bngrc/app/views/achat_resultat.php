<?php $baseUrl = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Résultat de simulation</title>
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
                <span class="fw-semibold">Résultat de simulation</span>
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
                <h1>Résultat de la simulation</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/besoins-restants">Besoins restants</a></li>
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/achat/<?= $simulation['besoin_id'] ?>">Achat</a></li>
                        <li class="breadcrumb-item active">Résultat</li>
                    </ol>
                </nav>
            </div>

            <div class="row g-4">
                <!-- Infos besoin -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Besoin concerné</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Ville</th>
                                    <td><?= htmlspecialchars($simulation['besoin']['ville_nom']) ?></td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td><?= htmlspecialchars($simulation['besoin']['type_nom']) ?></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td><?= htmlspecialchars($simulation['besoin']['description']) ?></td>
                                </tr>
                                <tr>
                                    <th>Prix unitaire</th>
                                    <td><?= number_format($simulation['besoin']['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                                <tr>
                                    <th>Quantité restante</th>
                                    <td><span class="badge bg-warning text-dark"><?= $simulation['besoin']['quantite_restante'] ?></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Résultat simulation -->
                <div class="col-lg-7">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Simulation réussie</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-bordered mb-0">
                                <tr>
                                    <th>Quantité à acheter</th>
                                    <td><?= $simulation['quantite'] ?></td>
                                </tr>
                                <tr>
                                    <th>Prix unitaire</th>
                                    <td><?= number_format($simulation['montant_unitaire'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                                <tr>
                                    <th>Montant de base</th>
                                    <td><?= number_format($simulation['montant_base'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                                <tr>
                                    <th>Frais (<?= $simulation['frais_pourcentage'] ?>%)</th>
                                    <td><?= number_format($simulation['montant_frais'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                                <tr class="table-primary">
                                    <th>Montant total</th>
                                    <td><strong><?= number_format($simulation['montant_total'], 0, ',', ' ') ?> Ar</strong></td>
                                </tr>
                                <tr>
                                    <th>Fonds disponibles</th>
                                    <td><?= number_format($simulation['fonds_disponibles'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                                <tr>
                                    <th>Fonds après achat</th>
                                    <td><?= number_format($simulation['fonds_apres_achat'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer d-flex gap-2">
                            <a href="<?= $baseUrl ?>/achat/<?= $simulation['besoin_id'] ?>" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Retour
                            </a>
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
</body>
</html>
