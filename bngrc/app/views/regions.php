<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Régions</title>
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
                <a class="nav-link" href="/dashboard">
                    <i class="bi bi-grid-1x2"></i> Dashboard
                </a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link active" href="/regions">
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
                <span class="fw-semibold">Régions</span>
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
                <h1>Régions</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Régions</li>
                    </ol>
                </nav>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="table-title mb-0">Liste des régions</h6>
                    <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#modalRegion">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter région
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom de la région</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($regions)): ?>
                                <?php foreach ($regions as $i => $region): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($region['nom']) ?></td>
                                        <td>
                                            <a href="/regions?edit=<?= $region['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="/regions/<?= $region['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Supprimer cette région ?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucune région trouvée</td>
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

    <!-- Modal: Ajouter Région -->
    <div class="modal fade" id="modalRegion" tabindex="-1" aria-labelledby="modalRegionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRegionLabel">Ajouter une région</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="/regions/create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomRegion" class="form-label">Nom de la région</label>
                            <input type="text" class="form-control" id="nomRegion" name="nom" placeholder="Ex: Analamanga" required>
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

    <?php if (isset($_GET['edit'])): ?>
    <?php
        $editRegion = null;
        foreach ($regions as $r) { if ($r['id'] == $_GET['edit']) { $editRegion = $r; break; } }
    ?>
    <?php if ($editRegion): ?>
    <!-- Modal: Modifier Région (auto-open) -->
    <div class="modal fade" id="modalRegionEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la région</h5>
                    <a href="/regions" class="btn-close" aria-label="Fermer"></a>
                </div>
                <form method="POST" action="/regions/<?= $editRegion['id'] ?>/update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomRegionEdit" class="form-label">Nom de la région</label>
                            <input type="text" class="form-control" id="nomRegionEdit" name="nom" value="<?= htmlspecialchars($editRegion['nom']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="/regions" class="btn btn-secondary btn-sm">Annuler</a>
                        <button type="submit" class="btn btn-accent btn-sm">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('modalRegionEdit')).show(); });</script>
    <?php endif; ?>
    <?php endif; ?>

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>
