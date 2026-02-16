<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Villes</title>
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
                <a class="nav-link" href="/regions">
                    <i class="bi bi-map"></i> Régions
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/villes">
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
                <span class="fw-semibold">Villes</span>
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
                <h1>Villes</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Villes</li>
                    </ol>
                </nav>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="table-title mb-0">Liste des villes</h6>
                    <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#modalVille">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter ville
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom ville</th>
                                <th>Région</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($villes)): ?>
                                <?php foreach ($villes as $i => $ville): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($ville['nom']) ?></td>
                                        <td><?= htmlspecialchars($ville['region_nom'] ?? '—') ?></td>
                                        <td>
                                            <a href="/villes?edit=<?= $ville['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="/villes/<?= $ville['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Supprimer cette ville ?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Aucune ville trouvée</td>
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

    <!-- Modal: Ajouter Ville -->
    <div class="modal fade" id="modalVille" tabindex="-1" aria-labelledby="modalVilleLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVilleLabel">Ajouter une ville</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="/villes/create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomVille" class="form-label">Nom de la ville</label>
                            <input type="text" class="form-control" id="nomVille" name="nom" placeholder="Ex: Antananarivo" required>
                        </div>
                        <div class="mb-3">
                            <label for="regionVille" class="form-label">Région</label>
                            <select class="form-select" id="regionVille" name="region_id" required>
                                <option value="" selected disabled>Sélectionner une région</option>
                                <?php if (!empty($regions)): ?>
                                    <?php foreach ($regions as $region): ?>
                                        <option value="<?= $region['id'] ?>"><?= htmlspecialchars($region['nom']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
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
        $editVille = null;
        foreach ($villes as $v) { if ($v['id'] == $_GET['edit']) { $editVille = $v; break; } }
    ?>
    <?php if ($editVille): ?>
    <div class="modal fade" id="modalVilleEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la ville</h5>
                    <a href="/villes" class="btn-close" aria-label="Fermer"></a>
                </div>
                <form method="POST" action="/villes/<?= $editVille['id'] ?>/update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomVilleEdit" class="form-label">Nom de la ville</label>
                            <input type="text" class="form-control" id="nomVilleEdit" name="nom" value="<?= htmlspecialchars($editVille['nom']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="regionVilleEdit" class="form-label">Région</label>
                            <select class="form-select" id="regionVilleEdit" name="region_id" required>
                                <option value="" disabled>Sélectionner une région</option>
                                <?php if (!empty($regions)): ?>
                                    <?php foreach ($regions as $region): ?>
                                        <option value="<?= $region['id'] ?>" <?= $editVille['region_id'] == $region['id'] ? 'selected' : '' ?>><?= htmlspecialchars($region['nom']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="/villes" class="btn btn-secondary btn-sm">Annuler</a>
                        <button type="submit" class="btn btn-accent btn-sm">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('modalVilleEdit')).show(); });</script>
    <?php endif; ?>
    <?php endif; ?>

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>
