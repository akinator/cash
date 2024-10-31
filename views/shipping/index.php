<?php ob_start(); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Suivi des expéditions</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Commandes</li>
                    <li class="breadcrumb-item active">Expéditions</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Statistiques rapides -->
        <div class="row">
            <div class="col-lg-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $stats['en_cours']; ?></h3>
                        <p>Commandes en cours</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $stats['expedie_today']; ?></h3>
                        <p>Expédiées aujourd'hui</p>
                        <small><?php echo date('d/m/Y'); ?></small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <a href="#" class="small-box-footer" id="showTodayShipped">
                        Voir le détail <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Liste des commandes en cours -->
        <div class="card">
<!-- Modifier la section card-header -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title">Commandes à expédier</h3>
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
                    <table class="table table-striped table-hover" id="shippingTable">
                        <thead>
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="checkAll">
                                        <label class="custom-control-label" for="checkAll"></label>
                                    </div>
                                </th>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Date commande</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandes as $commande): 
                                if ($commande['statut'] === OrderStatus::EN_COURS): ?>
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input commande-check" 
                                                   id="check_<?php echo $commande['id']; ?>"
                                                   data-id="<?php echo $commande['id']; ?>"
                                                   data-reference="<?php echo $commande['reference']; ?>">
                                            <label class="custom-control-label" for="check_<?php echo $commande['id']; ?>"></label>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($commande['reference']); ?></td>
                                    <td><?php echo htmlspecialchars($commande['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($commande['telephone']); ?></td>
                                    <td><?php echo htmlspecialchars($commande['adresse']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($commande['date_creation'])); ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-xs btn-success mark-shipped" 
                                                    data-id="<?php echo $commande['id']; ?>"
                                                    data-reference="<?php echo $commande['reference']; ?>"
                                                    title="Marquer comme expédié">
                                                <i class="fas fa-truck"></i>
                                            </button>
                                            <a href="index.php?action=commandes/view&id=<?php echo $commande['id']; ?>" 
                                               class="btn btn-xs btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-success" id="markSelectedShipped" disabled>
                    <i class="fas fa-truck mr-1"></i> Marquer la sélection comme expédié
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Modal pour les expéditions du jour -->
<div class="modal fade" id="todayShippedModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white">
                    <i class="fas fa-truck mr-2"></i>
                    Commandes expédiées aujourd'hui
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0" id="todayShippedTable">
                        <thead>
                            <tr>
                                <th>Heure</th>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Expédié par</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($expeditions_today as $expedition): ?>
                                <tr>
                                    <td><?php echo date('H:i', strtotime($expedition['date_expedition'])); ?></td>
                                    <td><?php echo htmlspecialchars($expedition['reference']); ?></td>
                                    <td><?php echo htmlspecialchars($expedition['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($expedition['telephone']); ?></td>
                                    <td><?php echo htmlspecialchars($expedition['username']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {

    // Initialisation des DataTables

     const table = $('#shippingTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 25,
        "order": [[5, 'desc']],
        "columnDefs": [
            {
                "targets": [0, 6],
                "orderable": false
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                className: 'btn btn-sm btn-success d-none',
                title: 'Liste des commandes à expédier',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf mr-1"></i> PDF',
                className: 'btn btn-sm btn-danger d-none',
                title: 'Liste des commandes à expédier',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                },
                customize: function(doc) {
                    doc.content.splice(0, 1, {
                        text: 'Liste des commandes à expédier',
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
                className: 'btn btn-sm btn-info d-none',
                title: '',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                },
                customize: function(win) {
                    $(win.document.body)
                        .css('font-size', '10pt')
                        .prepend(
                            '<div class="text-center mb-4">' +
                            '<h3>Liste des commandes à expédier</h3>' +
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

    // Ajouter les gestionnaires d'événements pour les boutons d'export
    $('#exportExcel').on('click', function() {
        table.button('.buttons-excel').trigger();
    });

    $('#exportPdf').on('click', function() {
        table.button('.buttons-pdf').trigger();
    });

    $('#printList').on('click', function() {
        table.button('.buttons-print').trigger();
    });

    

    if ($.fn.DataTable.isDataTable('#todayShippedTable')) {
        $('#todayShippedTable').DataTable().destroy();
    }

    $('#todayShippedTable').DataTable({
        "language": {
            "emptyTable": "Aucune expédition aujourd'hui",
            "info": "Affichage de _START_ à _END_ sur _TOTAL_ expéditions",
            "infoEmpty": "Aucune expédition à afficher",
            "search": "Rechercher :",
            "zeroRecords": "Aucune expédition trouvée"
        },
        "order": [[0, 'desc']],
        "pageLength": 10
    });

    // Gestion du modal des expéditions du jour
    $('#showTodayShipped').on('click', function(e) {
        e.preventDefault();
        $('#todayShippedModal').modal('show');
    });

    // Gestion des checkboxes
    $('#checkAll').on('change', function() {
        $('.commande-check').prop('checked', this.checked);
        updateMarkSelectedButton();
    });

    $('.commande-check').on('change', function() {
        updateMarkSelectedButton();
    });

    function updateMarkSelectedButton() {
        const checkedCount = $('.commande-check:checked').length;
        $('#markSelectedShipped').prop('disabled', checkedCount === 0)
            .html(`<i class="fas fa-truck mr-1"></i> Marquer ${checkedCount} commande${checkedCount > 1 ? 's' : ''} comme expédié`);
    }

    // Gestion des actions d'expédition
    $('.mark-shipped').on('click', function() {
        const id = $(this).data('id');
        const reference = $(this).data('reference');
        showShippingConfirmation([{id, reference}]);
    });

    $('#markSelectedShipped').on('click', function() {
        const selectedCommandes = $('.commande-check:checked').map(function() {
            return {
                id: $(this).data('id'),
                reference: $(this).data('reference')
            };
        }).get();
        showShippingConfirmation(selectedCommandes);
    });

    function showShippingConfirmation(commandes) {
        const modal = createConfirmationModal(commandes);
        document.body.appendChild(modal);

        document.getElementById('confirmShipping').addEventListener('click', function() {
            updateCommandesStatus(commandes, modal);
        });

        document.getElementById('cancelShipping').addEventListener('click', function() {
            document.body.removeChild(modal);
        });

        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    function createConfirmationModal(commandes) {
        const modal = document.createElement('div');
        modal.className = 'modal fade show';
        modal.style.display = 'block';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-truck fa-4x text-success"></i>
                        </div>
                        <h3 class="modal-title mb-3">Confirmation d'expédition</h3>
                        <p>Voulez-vous vraiment marquer cette/ces commande(s) comme expédiée(s) ?</p>
                        <ul class="list-unstyled text-left bg-light p-3 mb-4">
                            ${commandes.map(c => `<li><i class="fas fa-check mr-2"></i>${c.reference}</li>`).join('')}
                        </ul>
                        <div class="mt-4">
                            <button type="button" class="btn btn-success mr-2" id="confirmShipping">
                                <i class="fas fa-check mr-1"></i> Oui, confirmer
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelShipping">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return modal;
    }

    function updateCommandesStatus(commandes, modal) {
        const promises = commandes.map(commande => 
            fetch('index.php?action=commandes/updateStatus', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${commande.id}&status=Expédié`
            }).then(response => response.json())
        );

        Promise.all(promises)
            .then(() => {
                location.reload();
            })
            .catch(error => {
                alert('Une erreur est survenue');
                document.body.removeChild(modal);
            });
    }
});
</script>

<style>
    /* Styles pour les boutons d'export */
.dt-buttons {
    display: none;
}

.card-header .btn-group {
    margin-left: 10px;
}

.card-header .btn-group .btn {
    margin-left: 2px;
}

@media print {
    .btn-group,
    .dataTables_filter,
    .dataTables_info,
    .dataTables_paginate,
    .card-header,
    .card-footer {
        display: none !important;
    }
    
    .table {
        width: 100% !important;
        border-collapse: collapse !important;
    }
    
    .table td,
    .table th {
        background-color: white !important;
        border: 1px solid #dee2e6 !important;
    }
}
.small-box.bg-success small {
    position: absolute;
    right: 10px;
    bottom: 45px;
    z-index: 0;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.8);
}

.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
}

.custom-checkbox {
    min-height: 1.2rem;
}

.table td {
    vertical-align: middle;
}

.modal-lg {
    max-width: 900px;
}

.list-unstyled li {
    padding: 5px 0;
    border-bottom: 1px solid #dee2e6;
}

.list-unstyled li:last-child {
    border-bottom: none;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal.show {
    animation: fadeIn 0.3s ease;
}
</style>

<?php
$content = ob_get_clean();
require('views/layouts/admin.php');
?>