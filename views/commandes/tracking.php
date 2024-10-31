<?php ob_start(); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Suivi des commandes</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Commandes</li>
                    <li class="breadcrumb-item active">Suivi</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Compteurs rapides -->
        <div class="row">
            <?php foreach (OrderStatus::getAllStatuses() as $status): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-<?php echo OrderStatus::getStatusColor($status); ?>">
                    <div class="inner">
                        <h3><?php echo $statusCounts[$status] ?? 0; ?></h3>
                        <p><?php echo $status; ?></p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-<?php echo OrderStatus::getStatusIcon($status); ?>"></i>
                    </div>
                    <a href="#" class="small-box-footer" data-toggle="modal" data-target="#modal-<?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                        Voir détails <i class="fas fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>



        <!-- Modals pour les détails -->
        <?php foreach (OrderStatus::getAllStatuses() as $status): ?>
        <div class="modal fade" id="modal-<?php echo strtolower(str_replace(' ', '-', $status)); ?>" tabindex="-1">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-<?php echo OrderStatus::getStatusColor($status); ?> text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-<?php echo OrderStatus::getStatusIcon($status); ?> mr-2"></i>
                            Commandes <?php echo $status; ?>
                        </h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover datatable" id="table-<?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                                <thead>
                                    <tr>
                                        <th>Référence</th>
                                        <th>Client</th>
                                        <th>Produit</th>
                                        <th>Téléphone</th>
                                        <th>Montant</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($commandes as $commande): 
                                        if ($commande['statut'] === $status): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($commande['reference']); ?></td>
                                        <td><?php echo htmlspecialchars($commande['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($commande['produit']); ?></td>
                                        <td><?php echo htmlspecialchars($commande['telephone']); ?></td>
                                        <td><?php echo number_format($commande['montant'], 2); ?> €</td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($commande['date_creation'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="index.php?action=commandes/view&id=<?php echo $commande['id']; ?>" 
                                                   class="btn btn-xs btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="index.php?action=commandes/edit&id=<?php echo $commande['id']; ?>" 
                                                   class="btn btn-xs btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-xs btn-danger delete-commande" 
                                                    data-id="<?php echo $commande['id']; ?>"
                                                    data-reference="<?php echo $commande['reference']; ?>"
                                                    title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; endforeach; ?>
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
        <?php endforeach; ?>
    </div>
</section>

<!-- Scripts spécifiques -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des DataTables
    $('.datatable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "pageLength": 10,
        "ordering": true,
        "responsive": true,
        dom: 'Bfrtip',
        buttons: [
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
    document.querySelectorAll('.update-status').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const reference = this.dataset.reference;
            const newStatus = this.dataset.newStatus;
            const oldStatus = this.dataset.oldStatus;

            // Confirmation
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
                            <p>Voulez-vous vraiment déplacer la commande<br>
                            <strong>${reference}</strong><br>
                            de "${oldStatus}" vers "${newStatus}" ?</p>
                            <div class="mt-5">
                                <button type="button" class="btn btn-danger mr-2" id="confirmStatus">Oui, déplacer</button>
                                <button type="button" class="btn btn-primary" id="cancelStatus">Annuler</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Gestion de la confirmation
            document.getElementById('confirmStatus').addEventListener('click', function() {
                fetch('index.php?action=commandes/updateStatus', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&status=${encodeURIComponent(newStatus)}`
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const card = document.getElementById(`commande-${id}`);
                        const newList = document.getElementById(newStatus.toLowerCase().replace(' ', '-'));
                        newList.appendChild(card);
                        document.body.removeChild(modal);
                        location.reload(); // Rafraîchir pour mettre à jour les compteurs
                    } else {
                        alert(data.error || 'Une erreur est survenue');
                        document.body.removeChild(modal);
                    }
                })
                .catch(error => {
                    alert('Une erreur est survenue');
                    document.body.removeChild(modal);
                });
            });

            // Gestion de l'annulation
            document.getElementById('cancelStatus').addEventListener('click', function() {
                document.body.removeChild(modal);
            });

            // Fermeture au clic en dehors
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    document.body.removeChild(modal);
                }
            });
        });
    });

    // Gestion de la suppression
    document.querySelectorAll('.delete-commande').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const reference = this.dataset.reference;

            const modal = document.createElement('div');
            modal.className = 'modal fade show';
            modal.style.display = 'block';
            modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
            modal.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-circle text-warning"></i>
                            </div>
                            <h3 class="modal-title mb-3">Confirmation de suppression</h3>
                            <p>Voulez-vous vraiment supprimer la commande<br>
                            <strong>${reference}</strong> ?</p>
                            <div class="mt-5">
                                <button type="button" class="btn btn-danger mr-2" id="confirmDelete">Oui, supprimer</button>
                                <button type="button" class="btn btn-primary" id="cancelDelete">Annuler</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            // Gestion de la confirmation de suppression
            document.getElementById('confirmDelete').addEventListener('click', function() {
                window.location.href = `index.php?action=commandes/delete&id=${id}`;
            });

            // Gestion de l'annulation
            document.getElementById('cancelDelete').addEventListener('click', function() {
                document.body.removeChild(modal);
            });

            // Fermeture au clic en dehors
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    document.body.removeChild(modal);
                }
            });
        });
    });
});
</script>

<style>
/* Styles généraux */
.kanban-list {
    min-height: 200px;
    max-height: calc(100vh - 400px);
    overflow-y: auto;
    padding: 0.5rem;
}

/* Style des cartes */
.commande-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.commande-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.commande-card .card-header {
    padding: 0.5rem;
}

.commande-card .card-body {
    font-size: 0.9rem;
}

/* Style des compteurs */
.small-box {
    transition: transform 0.3s ease;
}

.small-box:hover {
    transform: translateY(-3px);
}

/*.small-box .icon {
    transition: all 0.3s ease;
}

.small-box:hover .icon {
    transform: scale(1.1);
}*/

/* Style des modals */
.modal-xl {
    max-width: 95%;
}

.datatable {
    width: 100% !important;
}

.dt-buttons {
    margin: 1rem;
}

.dt-button {
    margin-right: 0.5rem !important;
}

/* Style des scrollbars */
.kanban-list::-webkit-scrollbar {
    width: 6px;
}

.kanban-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.kanban-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.kanban-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Responsive */
@media (max-width: 768px) {
    .kanban-list {
        max-height: 400px;
    }

    .modal-xl {
        margin: 0.5rem;
    }

    .btn-group {
        display: flex;
        flex-wrap: wrap;
    }

    .btn-group .btn {
        flex: 1;
        margin: 2px;
    }

    .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .dt-buttons .btn {
        margin: 2px !important;
    }
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.modal.show {
    animation: fadeIn 0.3s ease;
}

.alert {
    animation: fadeIn 0.5s ease;
}

/* Style des badges */
.badge {
    font-size: 0.875rem;
    padding: 0.4em 0.6em;
}

/* Style des tooltips */
.tooltip {
    font-size: 0.875rem;
}

/* Style des dropdown menus */
.dropdown-menu {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: none;
}

.dropdown-item {
    padding: 0.5rem 1rem;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
}

/* Style des boutons d'action */
.btn-xs {
    padding: 0.125rem 0.25rem;
    font-size: 0.75rem;
    line-height: 1.5;
    border-radius: 0.15rem;
}

/* Style des messages d'alerte */
.alert {
    border: none;
    border-radius: 0.25rem;
}

/* Style pour le responsive design */
@media (max-width: 576px) {
    .card-title {
        font-size: 1rem;
    }

    .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .small-box h3 {
        font-size: 1.5rem;
    }
}
</style>

<?php
$content = ob_get_clean();
require('views/layouts/admin.php');
?>