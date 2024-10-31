<?php ob_start(); ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    <i class="fas fa-check-double mr-2"></i>Commandes confirmées
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Expédition</li>
                    <li class="breadcrumb-item active">Confirmées</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Statistiques -->
        <div class="row">
            <div class="col-lg-4">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?php echo $stats['confirmes']; ?></h3>
                        <p>Commandes confirmées</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-check-double"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?php echo $stats['en_cours']; ?></h3>
                        <p>En cours d'expédition</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $stats['expedie_today']; ?></h3>
                        <p>Expédiées aujourd'hui</p>
                        <small><?php echo date('d/m/Y'); ?></small>
                    </div>
                    <div class="icon">
                        <i class="fas fa-truck"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des commandes -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list mr-2"></i>Commandes confirmées à traiter
                </h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-success btn-sm" id="exportExcel">
                        <i class="fas fa-file-excel mr-1"></i>Exporter
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="confirmedTable">
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
                                <th>Date confirmation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($commandes)): ?>
                                <?php foreach ($commandes as $commande): ?>
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
                                        <td><?php echo date('d/m/Y H:i', strtotime($commande['date_confirmation'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" 
                                                        class="btn btn-xs btn-info update-status" 
                                                        data-id="<?php echo $commande['id']; ?>"
                                                        data-reference="<?php echo $commande['reference']; ?>"
                                                        data-status="<?php echo OrderStatus::EN_COURS; ?>">
                                                    <i class="fas fa-clock mr-1"></i>En cours
                                                </button>
                                                <a href="index.php?action=commandes/view&id=<?php echo $commande['id']; ?>" 
                                                   class="btn btn-xs btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Aucune commande confirmée à traiter</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <div class="btn-group">
                    <button type="button" class="btn btn-info" id="markSelectedInProgress" disabled>
                        <i class="fas fa-clock mr-1"></i> Marquer en cours d'expédition
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation de DataTable
    $('#confirmedTable').DataTable({
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
        ]
    });

    // Gestion des checkboxes
    $('#checkAll').on('change', function() {
        $('.commande-check').prop('checked', this.checked);
        updateButtons();
    });

    $('.commande-check').on('change', function() {
        updateButtons();
    });

    function updateButtons() {
        const selectedCount = $('.commande-check:checked').length;
        const btnText = selectedCount > 1 ? 's' : '';
        
        $('#markSelectedInProgress')
            .prop('disabled', selectedCount === 0)
            .html(`<i class="fas fa-clock mr-1"></i> Marquer ${selectedCount} commande${btnText} en cours`);
    }

    // AJOUT DE LA FONCTION MANQUANTE
    function showStatusConfirmation(commandes, status) {
        if (commandes.length === 0) {
            alert('Aucune commande sélectionnée');
            return;
        }

        console.log('Commandes à mettre à jour:', commandes);
        console.log('Nouveau statut:', status);

        const modal = createConfirmationModal(commandes, status);
        document.body.appendChild(modal);

        // Gestionnaire pour le bouton de confirmation
        const confirmBtn = modal.querySelector('#confirmStatus');
        confirmBtn.addEventListener('click', () => {
            updateStatuses(commandes, status, modal);
        });

        // Gestionnaire pour le bouton d'annulation
        const cancelBtn = modal.querySelector('#cancelStatus');
        cancelBtn.addEventListener('click', () => {
            document.body.removeChild(modal);
        });

        // Fermeture du modal en cliquant à l'extérieur
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    function createConfirmationModal(commandes, status) {
        const modal = document.createElement('div');
        modal.className = 'modal fade show';
        modal.style.display = 'block';
        modal.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-clock fa-4x text-info"></i>
                        </div>
                        <h3 class="modal-title mb-3">Confirmation de changement de statut</h3>
                        <p>Voulez-vous vraiment marquer comme "En cours d'expédition" :</p>
                        <ul class="list-unstyled text-left bg-light p-3 mb-4">
                            ${commandes.map(c => `<li><i class="fas fa-check mr-2"></i>${c.reference}</li>`).join('')}
                        </ul>
                        <div class="mt-4">
                            <button type="button" class="btn btn-info mr-2" id="confirmStatus">
                                <i class="fas fa-clock mr-1"></i> Oui, confirmer
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancelStatus">
                                Annuler
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        return modal;
    }

    function updateStatuses(commandes, status, modal) {
        console.log('Début de la mise à jour des statuts');
        
        const promises = commandes.map(commande => {
            console.log('Envoi de la requête pour la commande:', commande);
            
            const formData = new FormData();
            formData.append('id', commande.id);
            formData.append('status', status);

            return fetch('index.php?action=shipping/updateStatus', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const rawResponse = await response.text();
                console.log('Réponse brute du serveur:', rawResponse);

                try {
                    const data = JSON.parse(rawResponse);
                    if (!data.success) {
                        throw new Error(data.error || 'Erreur lors de la mise à jour');
                    }
                    return data;
                } catch (e) {
                    console.error('Erreur parsing JSON:', e);
                    throw new Error('Réponse invalide du serveur: ' + rawResponse);
                }
            });
        });

        Promise.all(promises)
            .then(results => {
                console.log('Toutes les mises à jour réussies:', results);
                location.reload();
            })
            .catch(error => {
                console.error('Erreur lors de la mise à jour:', error);
                alert('Erreur : ' + error.message);
                if (modal && modal.parentNode) {
                    document.body.removeChild(modal);
                }
            });
    }

    // Action de masse
    $('#markSelectedInProgress').on('click', function() {
        const selected = getSelectedCommandes();
        showStatusConfirmation(selected, '<?php echo OrderStatus::EN_COURS; ?>');
    });

    // Action individuelle
    $('.update-status').on('click', function(e) {
        e.preventDefault();
        const id = $(this).data('id');
        const reference = $(this).data('reference');
        const status = $(this).data('status');
        showStatusConfirmation([{id, reference}], status);
    });

    function getSelectedCommandes() {
        return $('.commande-check:checked').map(function() {
            return {
                id: $(this).data('id'),
                reference: $(this).data('reference')
            };
        }).get();
    }

    // Export Excel
    $('#exportExcel').on('click', function() {
        window.location.href = 'index.php?action=shipping/exportConfirmed';
    });
});
</script>

<style>
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

.small-box small {
    position: absolute;
    right: 10px;
    bottom: 45px;
    z-index: 0;
    font-size: 0.8rem;
    color: rgba(255,255,255,0.8);
}

.dropdown-item {
    cursor: pointer;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
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