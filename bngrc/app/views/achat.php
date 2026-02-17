<?php $baseUrl = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Achat</title>
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
                <span class="fw-semibold">Achat via dons en argent</span>
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
                <div class="alert alert-<?= $messageType === 'success' ? 'success' : ($messageType === 'danger' ? 'danger' : 'info') ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="page-header d-flex flex-wrap align-items-center justify-content-between">
                <h1>Achat pour un besoin</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item"><a href="<?= $baseUrl ?>/besoins-restants">Besoins restants</a></li>
                        <li class="breadcrumb-item active">Achat</li>
                    </ol>
                </nav>
            </div>

            <div class="row g-4">
                <!-- Infos besoin -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Détails du besoin</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tr>
                                    <th>Ville</th>
                                    <td><?= htmlspecialchars($besoin['ville_nom']) ?></td>
                                </tr>
                                <tr>
                                    <th>Type</th>
                                    <td><?= htmlspecialchars($besoin['type_nom']) ?></td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td><?= htmlspecialchars($besoin['description']) ?></td>
                                </tr>
                                <tr>
                                    <th>Prix unitaire</th>
                                    <td><?= number_format($besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                                <tr>
                                    <th>Quantité totale</th>
                                    <td><?= $besoin['quantite'] ?></td>
                                </tr>
                                <tr>
                                    <th>Quantité restante</th>
                                    <td><span class="badge bg-warning text-dark"><?= $besoin['quantite_restante'] ?></span></td>
                                </tr>
                                <tr>
                                    <th>Montant restant</th>
                                    <td><?= number_format($besoin['quantite_restante'] * $besoin['prix_unitaire'], 0, ',', ' ') ?> Ar</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer text-muted" style="font-size:0.85rem;">
                            <i class="bi bi-cash-stack me-1"></i>
                            Fonds Argent disponibles : <strong><?= number_format($fonds_disponibles ?? 0, 0, ',', ' ') ?> Ar</strong>
                        </div>
                    </div>
                </div>

                <!-- Formulaire d'achat -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-cart-check me-2"></i>Formulaire d'achat</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="formAchat">
                                <input type="hidden" name="besoin_id" value="<?= $besoin['id'] ?>">
                                <div class="mb-3">
                                    <label for="quantite" class="form-label">Quantité à acheter</label>
                                    <input type="number" class="form-control" id="quantite" name="quantite"
                                           min="1" max="<?= $besoin['quantite_restante'] ?>"
                                           required>
                                    <div class="form-text">Maximum : <?= $besoin['quantite_restante'] ?></div>
                                </div>
                                <div class="mb-3">
                                    <label for="frais_pourcentage" class="form-label">Frais supplémentaires (%)</label>
                                    <input type="number" class="form-control" id="frais_pourcentage" name="frais_pourcentage"
                                           min="0" max="100" step="0.01" value="0">
                                    <div class="form-text">Pourcentage de frais additionnels (transport, manutention…)</div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary" onclick="this.form.action='<?= $baseUrl ?>/achat/simulate'">
                                        <i class="bi bi-calculator me-1"></i>Simuler
                                    </button>
                                    <button type="submit" class="btn btn-success" onclick="if(confirm('Confirmer la validation de cet achat ?')){this.form.action='<?= $baseUrl ?>/achat/validate';}else{return false;}">
                                        <i class="bi bi-check-lg me-1"></i>Valider
                                    </button>
                                    <a href="<?= $baseUrl ?>/besoins-restants" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg me-1"></i>Annuler
                                    </a>
                                </div>
                            </form>
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
