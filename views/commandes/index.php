<?php ob_start(); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Gestion des Commandes</h1>
            </div>
            <div class="col-sm-6">
                <div class="float-sm-right">
                    <a href="index.php?action=commandes/create" class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i>Nouvelle Commande
                    </a>
                    <a href="index.php?action=commandes/import" class="btn btn-info ml-2">
                        <i class="fas fa-file-import mr-1"></i>Importer
                    </a>
                    <div class="btn-group ml-2">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            <i class="fas fa-download mr-1"></i>Exporter
                        </button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="index.php?action=commandes/export&format=excel">
                                <i class="fas fa-file-excel mr-2"></i>Excel
                            </a>
                            <a class="dropdown-item" href="index.php?action=commandes/export&format=pdf">
                                <i class="fas fa-file-pdf mr-2"></i>PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <!-- Bouton Nouvelle Commande -->




                <!-- Filtres améliorés -->
        <!-- Filtres avancés -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Filtres avancés</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="index.php" id="filterForm">
            <input type="hidden" name="action" value="commandes">
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Client</label>
                        <input type="text" class="form-control" name="nom" 
                               value="<?php echo htmlspecialchars($filtres['nom']); ?>" 
                               placeholder="Nom du client">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="text" class="form-control" name="telephone" 
                               value="<?php echo htmlspecialchars($filtres['telephone']); ?>" 
                               placeholder="Numéro de téléphone">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Statut</label>
                            <select name="statut" id="statut" class="form-control">
                                <option value="">Tous les statuts</option>
                                <?php echo OrderStatus::getStatusSelectOptions($filtres['statut'] ?? ''); ?>
                            </select>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Période</label>
                        <select class="form-control" name="periode">
                            <option value="">Toutes les périodes</option>
                            <option value="today" <?php echo ($filtres['periode'] === 'today') ? 'selected' : ''; ?>>Aujourd'hui</option>
                            <option value="week" <?php echo ($filtres['periode'] === 'week') ? 'selected' : ''; ?>>Cette semaine</option>
                            <option value="month" <?php echo ($filtres['periode'] === 'month') ? 'selected' : ''; ?>>Ce mois</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tri par</label>
                        <select class="form-control" name="tri">
                            <option value="date_desc" <?php echo ($filtres['tri'] === 'date_desc') ? 'selected' : ''; ?>>Date (Plus récent)</option>
                            <option value="date_asc" <?php echo ($filtres['tri'] === 'date_asc') ? 'selected' : ''; ?>>Date (Plus ancien)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Appliquer les filtres
                </button>
                <a href="index.php?action=commandes" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<?php if (isset($totalResultats)): ?>
<div class="alert alert-info mt-3">
    <?php echo $totalResultats; ?> résultat(s) trouvé(s)
    <?php if (!empty($_GET['nom']) || !empty($_GET['telephone']) || !empty($_GET['statut']) || !empty($_GET['periode'])): ?>
        avec les filtres appliqués
    <?php endif; ?>
</div>
<?php endif; ?>

        <!-- Liste des commandes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Liste des commandes</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Produit</th>  <!-- Nouvelle colonne -->
                            <th>Client</th>
                            <th>Téléphone</th>
                            <th>Montant TTC</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($commandes) && is_array($commandes)): ?>
                            <?php foreach($commandes as $commande): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($commande['reference']); ?></td>
                                    <td><?php echo htmlspecialchars($commande['produit']); ?></td>  <!-- Nouvelle colonne -->
                                    <td><?php echo htmlspecialchars($commande['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($commande['telephone']); ?></td>
                                    <td><?php echo number_format($commande['montant'], 2); ?> €</td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($commande['date_creation'])); ?></td>
                                    <td>
                                        <?php echo OrderStatus::getStatusBadge($commande['statut']); ?>
                                    </td>
<td class="text-center">
    <div class="btn-group">
        <a href="index.php?action=commandes/view&id=<?php echo $commande['id']; ?>" 
           class="btn btn-info btn-sm" title="Voir">
            <i class="fas fa-eye"></i>
        </a>
        <button type="button" 
            class="btn btn-primary btn-sm update-status"
            title="Changer le statut"
            data-id="<?php echo $commande['id']; ?>"
            data-reference="<?php echo htmlspecialchars($commande['reference']); ?>"
            data-status="<?php echo htmlspecialchars($commande['statut']); ?>">
            <i class="fas fa-sync-alt"></i>
        </button>
        <a href="index.php?action=commandes/edit&id=<?php echo $commande['id']; ?>" 
           class="btn btn-warning btn-sm" title="Modifier">
            <i class="fas fa-edit"></i>
        </a>
        <button type="button" 
                class="btn btn-danger btn-sm delete-commande" 
                data-id="<?php echo $commande['id']; ?>" 
                data-reference="<?php echo htmlspecialchars($commande['reference']); ?>"
                title="Supprimer">
            <i class="fas fa-trash"></i>
        </button>
    </div>
</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Aucune commande trouvée</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
           <!-- Modal Update Status -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="updateStatusModalLabel">Modifier le Statut</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateStatusForm">
                    <input type="hidden" id="commandeId">
                    <div class="form-group">
                        <label>Référence de la commande</label>
                        <input type="text" class="form-control" id="commandeReference" readonly>
                    </div>
                    <div class="form-group">
                        <label>Statut actuel</label>
                        <div id="currentStatusDisplay"></div>
                    </div>
                    <div class="form-group">
                        <label for="newStatus">Nouveau statut</label>
                            <select class="form-control" id="newStatus" name="newStatus">
        <?php echo OrderStatus::getStatusSelectOptions($commande['statut'] ?? OrderStatus::EN_ATTENTE); ?>
    </select>
                        
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmUpdateStatus">Enregistrer</button>
            </div>
        </div>
    </div>
</div>
            <!-- Pagination -->
<div class="card-footer clearfix">
        <ul class="pagination pagination-sm m-0 float-right">
            <?php
            $totalPages = ceil($totalResultats / $resultsPerPage);
            $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            ?>
            
            <!-- Première page -->
            <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $this->generatePageUrl(1); ?>">&laquo;</a>
            </li>
            
            <!-- Pages -->
            <?php for($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                <li class="page-item <?php echo ($currentPage == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo $this->generatePageUrl($i); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <!-- Dernière page -->
            <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo $this->generatePageUrl($totalPages); ?>">&raquo;</a>
            </li>
        </ul>
        
        <div class="float-left">
            Affichage de 
            <?php echo min(($currentPage - 1) * $resultsPerPage + 1, $totalResultats); ?> 
            à 
            <?php echo min($currentPage * $resultsPerPage, $totalResultats); ?> 
            sur <?php echo $totalResultats; ?> entrées
        </div>
    </div>
        </div>
    </div>
</section>

<!-- Ajoutez ces scripts avant la fermeture de la balise body -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- À la fin du fichier, juste avant le require du layout -->
<!-- Scripts pour la suppression -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la suppression
    const deleteButtons = document.querySelectorAll('.delete-commande');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const id = this.getAttribute('data-id');
            const reference = this.getAttribute('data-reference');
            
            Swal.fire({
                title: 'Confirmation de suppression',
                text: `Voulez-vous vraiment supprimer la commande ${reference} ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Animation de suppression
                    const row = this.closest('tr');
                    row.style.transition = 'opacity 0.5s ease';
                    row.style.opacity = '0';
                    
                    setTimeout(() => {
                        window.location.href = `index.php?action=commandes/delete&id=${id}`;
                    }, 500);
                }
            });
        });
    });
});
</script>

<!-- Après le script de suppression -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestionnaire pour les boutons de mise à jour du statut
    const updateStatusButtons = document.querySelectorAll('.update-status');
    
    updateStatusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const reference = this.getAttribute('data-reference');
            const currentStatus = this.getAttribute('data-status');

            // Remplir le modal avec les informations
            document.getElementById('commandeId').value = id;
            document.getElementById('commandeReference').value = reference;
            document.getElementById('currentStatusDisplay').innerHTML = `
                <span class="badge badge-${getStatusBadgeClass(currentStatus)}">
                    ${currentStatus}
                </span>
            `;
            document.getElementById('newStatus').value = currentStatus;

            // Afficher le modal
            $('#updateStatusModal').modal('show');
        });
    });

    // Gestionnaire pour le bouton de confirmation
    document.getElementById('confirmUpdateStatus').addEventListener('click', function() {
        const id = document.getElementById('commandeId').value;
        const newStatus = document.getElementById('newStatus').value;
        const row = document.querySelector(`button[data-id="${id}"]`).closest('tr');

        // Appel AJAX pour mettre à jour le statut
        fetch('index.php?action=commandes/updateStatus', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${id}&status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Mettre à jour l'affichage dans le tableau
                const statusCell = row.querySelector('.badge');
                statusCell.className = `badge badge-${getStatusBadgeClass(newStatus)}`;
                statusCell.textContent = newStatus;

                // Mettre à jour le data-status du bouton
                row.querySelector('.update-status').setAttribute('data-status', newStatus);

                // Fermer le modal
                $('#updateStatusModal').modal('hide');

                // Notification de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Statut mis à jour !',
                    text: `Le statut a été changé en "${newStatus}"`,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur !',
                    text: data.error || 'Une erreur est survenue lors de la mise à jour du statut.',
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erreur !',
                text: 'Une erreur est survenue lors de la mise à jour du statut.',
            });
        });
    });

    // Fonction utilitaire pour les classes de badge
    function getStatusBadgeClass(status) {
        switch(status) {
            case 'En attente': return 'warning';
            case 'En cours': return 'info';
            case 'Confirmé': return 'success';
            case 'Annulée': return 'danger';
           default: return 'secondary';
        }
    }
});
</script>


<?php
$content = ob_get_clean();
require('views/layouts/admin.php');
?>