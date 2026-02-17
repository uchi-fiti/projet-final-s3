<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Besoins - BNGRC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">
                <i class="bi bi-house-heart"></i> BNGRC - Gestion des Dons
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/besoins">Besoins</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dons">Dons</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/villes">Villes</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <!-- Messages de notification -->
                <?php if (isset($message)): ?>
                    <?php if ($message === 'success_created'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> Besoin créé avec succès !
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php elseif ($message === 'success_updated'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> Besoin modifié avec succès !
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php elseif ($message === 'success_deleted'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> Besoin supprimé avec succès !
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php elseif ($message === 'error_not_found'): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> Besoin non trouvé !
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- En-tête -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="bi bi-clipboard-check"></i> Gestion des Besoins</h1>
                    <a href="/besoins/create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Nouveau Besoin
                    </a>
                </div>

                <!-- Table des besoins -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($besoins)): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Aucun besoin enregistré pour le moment.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Ville</th>
                                            <th>Type</th>
                                            <th>Désignation</th>
                                            <th class="text-end">Quantité</th>
                                            <th class="text-end">Prix Unitaire</th>
                                            <th class="text-end">Montant Total</th>
                                            <th>Date</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($besoins as $besoin): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($besoin['id']) ?></td>
                                                <td>
                                                    <i class="bi bi-geo-alt"></i>
                                                    <?= htmlspecialchars($besoin['ville_nom'] ?? 'N/A') ?>
                                                </td>
                                                <td>
                                                    <?php if ($besoin['type_besoin'] === 'nature'): ?>
                                                        <span class="badge bg-success">Nature</span>
                                                    <?php elseif ($besoin['type_besoin'] === 'materiau'): ?>
                                                        <span class="badge bg-warning">Matériau</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-info">Argent</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($besoin['designation']) ?></td>
                                                <td class="text-end"><?= number_format($besoin['quantite'], 2, ',', ' ') ?></td>
                                                <td class="text-end"><?= number_format($besoin['prix_unitaire'], 2, ',', ' ') ?> Ar</td>
                                                <td class="text-end">
                                                    <strong><?= number_format($besoin['quantite'] * $besoin['prix_unitaire'], 2, ',', ' ') ?> Ar</strong>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($besoin['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="/besoins/edit/<?= $besoin['id'] ?>" 
                                                           class="btn btn-sm btn-warning" 
                                                           title="Modifier">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                onclick="confirmDelete(<?= $besoin['id'] ?>)"
                                                                title="Supprimer">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="6" class="text-end">Total Général :</th>
                                            <th class="text-end">
                                                <?php 
                                                    $total = array_sum(array_map(function($b) {
                                                        return $b['quantite'] * $b['prix_unitaire'];
                                                    }, $besoins));
                                                    echo number_format($total, 2, ',', ' ') . ' Ar';
                                                ?>
                                            </th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle"></i> Confirmation de suppression
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer ce besoin ? Cette action est irréversible.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id) {
            const form = document.getElementById('deleteForm');
            form.action = '/besoins/delete/' + id;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }
    </script>
</body>
</html>
