<?php ob_start(); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-check-circle mr-2"></i>Commandes expédiées
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Expédition</li>
                    <li class="breadcrumb-item active">Expédiées</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Statistiques -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $stats['expedie_today']; ?></h3>
                        <p>Aujourd'hui</p>
                        <small><?php echo date('d/m/Y'); ?></small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $stats['expedie_week']; ?></h3>
                        <p>Cette semaine</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $stats['expedie_month']; ?></h3>
                        <p>Ce mois</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary">
                    <div class="inner">
                        <h3><?php echo $stats['en_cours']; ?></h3>
                        <p>En cours</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <a href="index.php?action=shipping" class="small-box-footer">
                        Voir les commandes <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-filter mr-2"></i>Filtres</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" action="index.php">
                    <input type="hidden" name="action" value="shipping/shipped">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date début</label>
                                <input type="date" class="form-control" name="date_debut" 
                                       value="<?php echo $_GET['date_debut'] ?? date('Y-m-d', strtotime('-30 days')); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Date fin</label>
                                <input type="date" class="form-control" name="date_fin" 
                                       value="<?php echo $_GET['date_fin'] ?? date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i>Filtrer
                                    </button>
                                    <a href="index.php?action=shipping/shipped" class="btn btn-secondary">
                                        <i class="fas fa-undo mr-1"></i>Réinitialiser
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des commandes expédiées -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>Liste des commandes expédiées
                </h3>
                <div class="card-tools">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success btn-sm" id="exportExcel">
                            <i class="fas fa-file-excel mr-1"></i>Excel
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" id="exportPdf">
                            <i class="fas fa-file-pdf mr-1"></i>PDF
                        </button>
                        <button type="button" class="btn btn-info btn-sm" id="printList">
                            <i class="fas fa-print mr-1"></i>Imprimer
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="shippedTable">
                        <thead>
                            <tr>
                                <th>Date d'expédition</th>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Expédié par</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($commandes)): ?>
                                <?php foreach ($commandes as $commande): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i', strtotime($commande['date_expedition'])); ?></td>
                                        <td><?php echo htmlspecialchars($commande['reference']); ?></td>
                                        <td><?php echo htmlspecialchars($commande['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($commande['telephone']); ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo htmlspecialchars($commande['username']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="index.php?action=commandes/view&id=<?php echo $commande['id']; ?>" 
                                                   class="btn btn-xs btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-xs btn-warning return-shipping" 
                                                        data-id="<?php echo $commande['id']; ?>"
                                                        data-reference="<?php echo $commande['reference']; ?>"
                                                        title="Remettre en cours"
                                                        data-status="<?php echo OrderStatus::EN_COURS; ?>">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Aucune commande expédiée pour cette période</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de DataTable
    const table = $('#shippedTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[0, 'desc']],
        "columnDefs": [
            {
                "targets": [5],
                "orderable": false
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                className: 'btn btn-sm btn-success',
                title: 'Liste des commandes expédiées',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                className: 'btn btn-sm btn-danger',
                title: 'Liste des commandes expédiées',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: 'Liste des commandes expédiées',
                        fontSize: 14,
                        alignment: 'center',
                        margin: [0, 0, 0, 12]
                    });
                    doc.content.splice(1, 0, {
                        text: 'Date d\'impression: ' + new Date().toLocaleDateString('fr-FR'),
                        fontSize: 10,
                        alignment: 'center',
                        margin: [0, 0, 0, 12]
                    });
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-1"></i> Imprimer',
                className: 'btn btn-sm btn-info',
                title: '',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4]
                },
                customize: function(win) {
                    $(win.document.body)
                        .css('font-size', '10pt')
                        .prepend(
                            '<div class="text-center mb-4">' +
                            '<h3>Liste des commandes expédiées</h3>' +
                            '<p>Période du : ' + 
                            $('input[name="date_debut"]').val() + ' au ' + 
                            $('input[name="date_fin"]').val() + '</p>' +
                            '<p>Date d\'impression: ' + new Date().toLocaleDateString('fr-FR') + '</p>' +
                            '</div>'
                        );

                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ]
    });

    // Boutons d'exportation personnalisés
    $('#exportExcel').on('click', function() {
        table.button('.buttons-excel').trigger();
    });

    $('#exportPdf').on('click', function() {
        table.button('.buttons-pdf').trigger();
    });

    $('#printList').on('click', function() {
        table.button('.buttons-print').trigger();
    });

    // Remettre en cours
    $('.return-shipping').on('click', function() {
        const id = $(this).data('id');
        const reference = $(this).data('reference');
        const status = $(this).data('status');

        const modal = createConfirmationModal(reference);
        document.body.appendChild(modal);

        document.getElementById('confirmReturn').addEventListener('click', function() {
            updateStatus(id, status, modal);
        });

        document.getElementById('cancelReturn').addEventListener('click', function() {
            document.body.removeChild(modal);
        });

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    });

    function createConfirmationModal(reference) {
        const modal = document.createElement('div');
        modal.className = 'modal fade show';
        modal.style.display = 'block';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-undo fa-4x text-warning"></i>
                        </div>
                        <h3 class="modal-title mb-3">Confirmation</h3>
                        <p>Voulez-vous remettre la commande<br><strong>${reference}</strong><br>en cours ?</p>
                        <div class="mt-4">
                            <button type="button" class="btn btn-warning mr-2" id="confirmReturn">
                                <i class="fas fa-undo mr-1"></i> Oui, remettre en cours
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelReturn">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return modal;
    }

    function updateStatus(id, status, modal) {
        fetch('index.php?action=shipping/updateStatus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}&status=${encodeURIComponent(status)}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert('Erreur : ' + (data.error || 'Une erreur est survenue'));
            }
            document.body.removeChild(modal);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
            document.body.removeChild(modal);
        });
    }
});
</script>


<!-- Le reste du code HTML reste identique jusqu'aux styles -->

<style>
.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
}

.badge {
    font-size: 0.875rem;
    padding: 0.4em 0.7em;
}

.card-header .card-tools {
    margin-top: -3px;
}

.small-box small {
    position: absolute;
    right: 10px;
    bottom: 45px;
    z-index: 0;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.8);
}

.dt-buttons {
    display: none;
}

.card-tools .btn-group {
    margin-left: 5px;
}

.card-tools .btn {
    margin: 0 2px;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
}

.form-group {
    margin-bottom: 1rem;
}

.btn-tool {
    padding: .25rem .5rem;
}

.card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 400;
}

.table td.actions {
    width: 100px;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal.show {
    animation: fadeIn 0.3s ease;
}

@media print {
    @page {
        size: landscape;
        margin: 1cm;
    }

    body {
        padding: 20px;
        font-size: 12pt;
    }

    .no-print,
    .card-tools,
    .card-header,
    .breadcrumb,
    .actions,
    .sorting:after,
    .sorting_asc:after,
    .sorting_desc:after {
        display: none !important;
    }

    .card {
        border: none !important;
        box-shadow: none !important;
    }

    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    .table td,
    .table th {
        background-color: white !important;
        border: 1px solid #dee2e6 !important;
        padding: 8px !important;
        font-size: 11pt !important;
    }

    .table thead th {
        background-color: #f4f4f4 !important;
        font-weight: bold !important;
    }

    .badge {
        border: 1px solid #ccc !important;
        padding: 0.2em 0.5em !important;
    }

    .text-center.mb-4 {
        margin-bottom: 20px !important;
    }

    .table-responsive {
        overflow: visible !important;
    }
}

/* Styles pour les filtres */
.card.collapsed-card .card-body {
    display: none;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

/* Style pour la période sélectionnée */
.period-info {
    font-size: 0.9rem;
    color: #6c757d;
    margin-top: 5px;
}

/* Styles pour les boutons d'action */
.btn-group .btn:not(:last-child) {
    margin-right: 2px;
}

.return-shipping:hover {
    background-color: #e0a800;
    border-color: #d39e00;
}

/* Amélioration de la réactivité */
@media (max-width: 576px) {
    .card-tools .btn-group {
        margin-top: 10px;
        display: flex;
        width: 100%;
    }

    .card-tools .btn {
        flex: 1;
    }

    .small-box {
        margin-bottom: 15px;
    }
}

/* Style pour le tableau responsive */
.table-responsive {
    min-height: 300px;
}

/* Style pour le message "aucune commande" */
.text-center.no-data {
    padding: 20px;
    color: #6c757d;
    font-style: italic;
}

/* Style pour l'en-tête fixe du tableau */
.table thead th {
    position: sticky;
    top: 0;
    background-color: #fff;
    z-index: 1;
}

/* Style pour le hover des lignes */
.table tbody tr:hover {
    background-color: rgba(0,123,255,.05) !important;
}

/* Animation pour les badges */
.badge {
    transition: all 0.2s ease-in-out;
}

.badge:hover {
    transform: scale(1.1);
}
</style>

<?php 
// Ajout des dépendances requises dans le header
$additional_css = '
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
';

$additional_js = '
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
';

$content = ob_get_clean();
require('views/layouts/admin.php');
?>