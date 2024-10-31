<?php ob_start(); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Commandes En Cours Expédié</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=commandes">Commandes</a></li>
                    <li class="breadcrumb-item active">En Cours Expédié</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filtres -->
        <div class="card collapsed-card">
            <div class="card-header">
                <h3 class="card-title">Filtres avancés</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="index.php" method="GET" id="filterForm">
                    <input type="hidden" name="action" value="commandes/confirmed">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="nom">Client</label>
                                <input type="text" class="form-control" id="nom" name="nom" 
                                       value="<?php echo htmlspecialchars($filtres['nom'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="telephone">Téléphone</label>
                                <input type="text" class="form-control" id="telephone" name="telephone" 
                                       value="<?php echo htmlspecialchars($filtres['telephone'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="periode">Période</label>
                                <select class="form-control" id="periode" name="periode">
                                    <option value="">Toutes les périodes</option>
                                    <option value="today" <?php echo ($filtres['periode'] ?? '') === 'today' ? 'selected' : ''; ?>>Aujourd'hui</option>
                                    <option value="week" <?php echo ($filtres['periode'] ?? '') === 'week' ? 'selected' : ''; ?>>Cette semaine</option>
                                    <option value="month" <?php echo ($filtres['periode'] ?? '') === 'month' ? 'selected' : ''; ?>>Ce mois</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="tri">Trier par</label>
                                <select class="form-control" id="tri" name="tri">
                                    <option value="date_desc" <?php echo ($filtres['tri'] ?? '') === 'date_desc' ? 'selected' : ''; ?>>Date (Plus récent)</option>
                                    <option value="date_asc" <?php echo ($filtres['tri'] ?? '') === 'date_asc' ? 'selected' : ''; ?>>Date (Plus ancien)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter mr-1"></i> Appliquer les filtres
                            </button>
                            <a href="index.php?action=commandes/confirmed" class="btn btn-secondary">
                                <i class="fas fa-undo mr-1"></i> Réinitialiser
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tableau des commandes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Liste des commandes confirmées</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" onclick="exportTableToExcel()">
                        <i class="fas fa-file-excel mr-1"></i> Export Excel
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                            <table class="table table-striped table-hover" id="confirmedOrdersTable">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Produit</th>
                                <th>Téléphone</th>
                                <th>Montant TTC</th>
                                <th>Date</th>
                        
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($commandes as $commande): ?>
<tr>
    <td><?php echo htmlspecialchars($commande['reference']); ?></td>
    <td><?php echo htmlspecialchars($commande['nom']); ?></td>
    <td><?php echo htmlspecialchars($commande['produit']); ?></td>
    <td><?php echo htmlspecialchars($commande['telephone']); ?></td>
    <td><?php echo number_format($commande['montant'], 2); ?> €</td>
    <td><?php echo date('d/m/Y H:i', strtotime($commande['date_creation'])); ?></td>
<td>
    <div class="btn-group">
        <button type="button" class="btn btn-sm dropdown-toggle status-badge 
                <?php echo ($commande['statut'] === OrderStatus::EN_COURS) ? 'btn-info' : 'btn-success'; ?>"
                data-toggle="dropdown">
            <?php 
            if ($commande['statut'] === OrderStatus::EN_COURS) {
                echo '<i class="fas fa-clock mr-1"></i> En cours';
            } else {
                echo '<i class="fas fa-truck mr-1"></i> Expédié';
            }
            ?>
        </button>
        <div class="dropdown-menu">
            <?php foreach (OrderStatus::getTrackingStatuses() as $status): ?>
                <?php if ($status !== $commande['statut']): ?>
                    <a class="dropdown-item update-status" href="#" 
                       data-id="<?php echo $commande['id']; ?>"
                       data-reference="<?php echo $commande['reference']; ?>"
                       data-current="<?php echo $commande['statut']; ?>"
                       data-status="<?php echo $status; ?>">
                        <i class="fas fa-<?php echo OrderStatus::getStatusIcon($status); ?> mr-2"></i>
                        Marquer comme <?php echo $status; ?>
                    </a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</td>
</tr>
<?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scripts spécifiques pour cette page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Détruire l'instance existante si elle existe
    if ($.fn.DataTable.isDataTable('#confirmedOrdersTable')) {
        $('#confirmedOrdersTable').DataTable().destroy();
    }

    // Initialiser une nouvelle instance
    $('#confirmedOrdersTable').DataTable({
        "language": {
            "url": "assets/js/french.json",  // Fichier de traduction locale
            "emptyTable": "Aucune donnée disponible",
            "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            "infoEmpty": "Affichage de 0 à 0 sur 0 entrée",
            "infoFiltered": "(filtré sur _MAX_ entrées totales)",
            "lengthMenu": "Afficher _MENU_ entrées",
            "search": "Rechercher :",
            "zeroRecords": "Aucun résultat trouvé",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "Précédent"
            }
        },
        "pageLength": 10,
        "ordering": true,
        "responsive": true,
        "dom": '<"top"Bf>rt<"bottom"lip><"clear">',
        "buttons": [
            {
                extend: 'excel',
                className: 'btn btn-sm btn-success',
                text: '<i class="fas fa-file-excel mr-1"></i> Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-sm btn-danger',
                text: '<i class="fas fa-file-pdf mr-1"></i> PDF'
            },
            {
                extend: 'print',
                className: 'btn btn-sm btn-info',
                text: '<i class="fas fa-print mr-1"></i> Imprimer'
            }
        ]
    });

    // Gestion du changement de statut
    $('.update-status').on('click', function(e) {
        e.preventDefault();
        
        const id = $(this).data('id');
        const reference = $(this).data('reference');
        const newStatus = $(this).data('status');
        const currentStatus = $(this).data('current');
        
        // Modal de confirmation
        const modal = document.createElement('div');
        modal.className = 'modal fade show';
        modal.style.display = 'block';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-circle fa-4x text-warning"></i>
                        </div>
                        <h3 class="modal-title mb-3">Confirmation de modification</h3>
                        <p>Voulez-vous vraiment modifier le statut de la commande<br>
                        <strong>${reference}</strong><br>
                        de "${currentStatus}" à "${newStatus}" ?</p>
                        <div class="mt-5">
                            <button type="button" class="btn btn-danger mr-2" id="confirmStatus">
                                <i class="fas fa-check mr-1"></i> Oui, modifier
                            </button>
                            <button type="button" class="btn btn-primary" id="cancelStatus">
                                <i class="fas fa-times mr-1"></i> Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Gestion de la confirmation
        $('#confirmStatus').on('click', function() {
            $.ajax({
                url: 'index.php?action=commandes/updateStatus',
                method: 'POST',
                data: {
                    id: id,
                    status: newStatus
                },
                success: function(response) {
                    if(response.success) {
                        location.reload();
                    } else {
                        alert(response.error || 'Une erreur est survenue');
                    }
                    document.body.removeChild(modal);
                },
                error: function() {
                    alert('Une erreur est survenue');
                    document.body.removeChild(modal);
                }
            });
        });

        // Gestion de l'annulation
        $('#cancelStatus').on('click', function() {
            document.body.removeChild(modal);
        });

        // Fermeture au clic en dehors
        $(modal).on('click', function(e) {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    });
});
</script>

<style>
/* Styles pour DataTables */
.dataTables_wrapper .dt-buttons {
    margin-bottom: 1rem;
    float: right;
}

.dataTables_wrapper .dataTables_filter {
    margin-bottom: 1rem;
}

.dt-buttons .btn {
    margin-left: 5px;
}

/* Styles pour les statuts et actions */
.status-badge {
    min-width: 120px;
    text-align: left;
}

.status-badge .fas {
    width: 16px;
}

.dropdown-item {
    cursor: pointer;
    padding: 8px 20px;
}

.dropdown-item .fas {
    width: 16px;
}

/* Styles pour la modal */
.modal-content {
    border: none;
    border-radius: 8px;
}

.btn-group .dropdown-menu {
    margin-top: 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>


<?php
$content = ob_get_clean();
require('views/layouts/admin.php');
?>