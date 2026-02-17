/* ============================================
   BNGRC – App JavaScript
   UI Interactions Only
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Sidebar Toggle (Mobile) ----
    const btnToggle = document.querySelector('.btn-toggle-sidebar');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.querySelector('.sidebar-overlay');

    if (btnToggle && sidebar) {
        btnToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            if (overlay) overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }

    // ---- Active Sidebar Link ----
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('#sidebar .nav-link');

    navLinks.forEach(function (link) {
        const href = link.getAttribute('href');
        if (href === currentPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });

    // ---- Delete Confirmation ----
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                e.preventDefault();
            }
        });
    });

    // ---- Auto-calculate Montant Total (bngrc_besoins) ----
    const prixField = document.getElementById('prixUnitaire');
    const qteField = document.getElementById('quantite');
    const montantDisplay = document.getElementById('montantTotal');

    function calcMontant() {
        if (prixField && qteField && montantDisplay) {
            const prix = parseFloat(prixField.value) || 0;
            const qte = parseFloat(qteField.value) || 0;
            montantDisplay.value = (prix * qte).toLocaleString('fr-FR') + ' Ar';
        }
    }

    if (prixField) prixField.addEventListener('input', calcMontant);
    if (qteField) qteField.addEventListener('input', calcMontant);

    // ---- Tooltips Init ----
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // ---- Modal Reset on Close ----
    document.querySelectorAll('.modal').forEach(function (modal) {
        modal.addEventListener('hidden.bs.modal', function () {
            const form = modal.querySelector('form');
            if (form) form.reset();
        });
    });

    // ---- Simulation Button (Attributions) ----
    const btnSimulation = document.getElementById('btnSimulation');
    if (btnSimulation) {
        btnSimulation.addEventListener('click', function () {
            const alertContainer = document.getElementById('alertContainer');
            if (alertContainer) {
                alertContainer.innerHTML = '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                    '<strong>Simulation terminée.</strong> Les attributions ont été générées avec succès.' +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
            }
            // Show the simulation results table
            const simTable = document.getElementById('simulationResults');
            if (simTable) {
                simTable.style.display = 'block';
            }
        });
    }
});
