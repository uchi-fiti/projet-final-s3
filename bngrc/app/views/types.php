<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BNGRC – Types de besoins</title>
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
                <a class="nav-link" href="/villes">
                    <i class="bi bi-building"></i> Villes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/types">
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
                <span class="fw-semibold">Types de besoins</span>
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
                <h1>Types de besoins</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/dashboard">Accueil</a></li>
                        <li class="breadcrumb-item active">Types de besoins</li>
                    </ol>
                </nav>
            </div>

            <!-- Table -->
            <div class="table-container">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="table-title mb-0">Liste des types</h6>
                    <button class="btn btn-accent btn-sm" data-bs-toggle="modal" data-bs-target="#modalType">
                        <i class="bi bi-plus-lg me-1"></i> Ajouter type
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nom du type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($types)): ?>
                                <?php foreach ($types as $i => $type): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= htmlspecialchars($type['nom']) ?></td>
                                        <td>
                                            <a href="/types?edit=<?= $type['id'] ?>" class="btn btn-sm btn-outline-secondary btn-action me-1">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="/types/<?= $type['id'] ?>/delete" style="display:inline;" onsubmit="return confirm('Supprimer ce type ?');">
                                                <button type="submit" class="btn btn-sm btn-outline-danger btn-action">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Aucun type trouvé</td>
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

    <!-- Modal: Ajouter Type -->
    <div class="modal fade" id="modalType" tabindex="-1" aria-labelledby="modalTypeLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTypeLabel">Ajouter un type de besoin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <form method="POST" action="/types/create">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomType" class="form-label">Nom du type</label>
                            <input type="text" class="form-control" id="nomType" name="nom" placeholder="Ex: Nourriture" required>
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
        $editType = null;
        foreach ($types as $t) { if ($t['id'] == $_GET['edit']) { $editType = $t; break; } }
    ?>
    <?php if ($editType): ?>
    <div class="modal fade" id="modalTypeEdit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier le type</h5>
                    <a href="/types" class="btn-close" aria-label="Fermer"></a>
                </div>
                <form method="POST" action="/types/<?= $editType['id'] ?>/update">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nomTypeEdit" class="form-label">Nom du type</label>
                            <input type="text" class="form-control" id="nomTypeEdit" name="nom" value="<?= htmlspecialchars($editType['nom']) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="/types" class="btn btn-secondary btn-sm">Annuler</a>
                        <button type="submit" class="btn btn-accent btn-sm">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>document.addEventListener('DOMContentLoaded', function() { new bootstrap.Modal(document.getElementById('modalTypeEdit')).show(); });</script>
    <?php endif; ?>
    <?php endif; ?>

    <script src="/js/bootstrap.bundle.min.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>
