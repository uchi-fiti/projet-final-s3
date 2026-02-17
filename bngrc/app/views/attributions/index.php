<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Attributions — Simulation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">BNGRC</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="/">Tableau</a></li>
                <li class="nav-item"><a class="nav-link" href="/besoins">Besoins</a></li>
                <li class="nav-item"><a class="nav-link" href="/dons">Dons</a></li>
                <li class="nav-item"><a class="nav-link active" href="/attributions">Attributions</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1><i class="bi bi-arrow-left-right"></i> Attributions / Simulation</h1>
        <div>
            <label class="me-2">Frais d'achat (%)</label>
            <input id="feePct" type="number" step="0.1" value="10" style="width:100px" class="form-control d-inline-block me-2">
            <button id="btnSimulate" class="btn btn-outline-primary me-2"><i class="bi bi-play-circle"></i> Simuler</button>
            <button id="btnValidate" class="btn btn-success" disabled><i class="bi bi-check-circle"></i> Valider</button>
        </div>
    </div>

    <div id="alertArea"></div>

    <div id="results" style="display:none;">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Besoins totaux (montant)</strong>
                        <div id="needsTotal">—</div>
                    </div>
                    <div class="col-md-4">
                        <strong>Montant attribué (simulation)</strong>
                        <div id="allocatedAmount">—</div>
                    </div>
                    <div class="col-md-4">
                        <strong>Besoins restants</strong>
                        <div id="needsRemaining">—</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Don</th>
                        <th>Ville</th>
                        <th>Besoin</th>
                        <th class="text-end">Quantité attribuée</th>
                        <th class="text-end">Montant attribué</th>
                    </tr>
                </thead>
                <tbody id="simTableBody"></tbody>
            </table>
        </div>
    </div>

    <div id="noResults" class="alert alert-info" style="display:none;">Aucune attribution possible avec les données actuelles.</div>
</div>

<footer class="text-center mt-4 mb-4">Projet BNGRC — Simulation des attributions</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const elSimulate = document.getElementById('btnSimulate');
    const elValidate = document.getElementById('btnValidate');
    const feeInput = document.getElementById('feePct');

    function showAlert(type, html) {
        const area = document.getElementById('alertArea');
        area.innerHTML = `<div class="alert alert-${type} alert-dismissible">${html}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
    }

    elSimulate.addEventListener('click', async function () {
        elSimulate.disabled = true;
        elValidate.disabled = true;
        showAlert('info', '<i class="bi bi-arrow-repeat"></i> Simulation en cours...');

        const fee = parseFloat(feeInput.value) || 0;
        try {
            const res = await fetch('/attributions/simulate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `fee_pct=${encodeURIComponent(fee)}`
            });
            const data = await res.json();
            if (!data.ok) throw new Error(data.error || 'Erreur simulation');

            const result = data.result;
            const allocations = result.allocations || [];

            document.getElementById('needsTotal').innerText = (result.totals.needs_total_amount || 0).toLocaleString() + ' Ar';
            document.getElementById('allocatedAmount').innerText = (result.totals.allocated_amount || 0).toLocaleString() + ' Ar';
            document.getElementById('needsRemaining').innerText = (result.totals.needs_remaining_amount || 0).toLocaleString() + ' Ar';

            const tbody = document.getElementById('simTableBody');
            tbody.innerHTML = '';

            if (allocations.length === 0) {
                document.getElementById('results').style.display = 'none';
                document.getElementById('noResults').style.display = 'block';
                showAlert('warning', 'Aucune attribution simulée avec les données actuelles.');
            } else {
                document.getElementById('results').style.display = 'block';
                document.getElementById('noResults').style.display = 'none';

                allocations.forEach((a, idx) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${idx+1}</td>
                        <td>${escapeHtml(a.don_designation || a.don_type)}</td>
                        <td>${escapeHtml(a.don_ville_id ?? '—')}</td>
                        <td>${escapeHtml(a.besoin_designation || a.besoin_type)}</td>
                        <td class="text-end">${Number(a.quantite_attribuee).toLocaleString()}</td>
                        <td class="text-end">${Number(a.montant_attribue).toLocaleString()} Ar</td>
                    `;
                    tbody.appendChild(tr);
                });

                showAlert('success', `<strong>Simulation terminée.</strong> ${allocations.length} attributions calculées.`);
                elValidate.disabled = false;
            }

        } catch (err) {
            showAlert('danger', 'Erreur pendant la simulation : ' + (err.message || err));
        } finally {
            elSimulate.disabled = false;
        }
    });

    elValidate.addEventListener('click', async function () {
        if (!confirm('Confirmer le dispatch réel des dons (insèrera les attributions en base) ?')) return;
        elValidate.disabled = true;
        showAlert('info', '<i class="bi bi-arrow-repeat"></i> Validation en cours...');
        const fee = parseFloat(feeInput.value) || 0;
        try {
            const res = await fetch('/attributions/validate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `fee_pct=${encodeURIComponent(fee)}`
            });
            const data = await res.json();
            if (!data.ok) throw new Error(data.error || 'Erreur lors de la validation');

            showAlert('success', `<strong>Validation réussie.</strong> ${data.inserted} enregistrement(s) ajoutés.`);
            elValidate.disabled = true;
            // actualiser la page / données si souhaité

        } catch (err) {
            showAlert('danger', 'Erreur pendant la validation : ' + (err.message || err));
            elValidate.disabled = false;
        }
    });

    function escapeHtml(s) {
        if (!s && s !== 0) return '';
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
</script>
</body>
</html>