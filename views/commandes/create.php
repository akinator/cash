<?php ob_start(); ?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Nouvelle Commande</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php?action=dashboard">Home</a></li>
                    <li class="breadcrumb-item"><a href="index.php?action=commandes">Commandes</a></li>
                    <li class="breadcrumb-item active">Nouvelle</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Informations de la commande</h3>
            </div>
            <form method="post" action="index.php?action=commandes/create" id="createForm">
                <div class="card-body">
                    <!-- Informations client -->
                    <div class="form-group">
                        <label for="nom">Nom du client *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required
                               placeholder="Nom complet du client">
                    </div>

                    <div class="form-group">
                        <label for="telephone">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone"
                               pattern="[0-9]{10}" placeholder="0123456789">
                        <small class="form-text text-muted">Format: 10 chiffres sans espaces</small>
                    </div>

                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <textarea class="form-control" id="adresse" name="adresse" rows="3"
                                  placeholder="Adresse complète du client"></textarea>
                    </div>

                    <!-- Informations commande -->
                    <div class="form-group">
                        <label for="produit">Produit *</label>
                        <input type="text" class="form-control" name="produit" id="produit" 
                               value="<?php echo isset($commande) ? htmlspecialchars($commande['produit']) : ''; ?>" 
                               required>
                    </div>
                    <div class="form-group">
                        <label for="montant">Montant TTC *</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="montant" name="montant" 
                                   step="0.01" min="0" required>
                            <div class="input-group-append">
                                <span class="input-group-text">€</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
    <label for="statut">Statut</label>
    <select name="statut" id="statut" class="form-control" required>
        <?php echo OrderStatus::getStatusSelectOptions($commande['statut'] ?? OrderStatus::EN_ATTENTE); ?>
    </select>
</div>

                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Notes supplémentaires..."></textarea>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Créer la commande</button>
                    <a href="index.php?action=commandes" class="btn btn-default">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.getElementById('createForm').addEventListener('submit', function(e) {
    let phone = document.getElementById('telephone').value;
    if(phone && !phone.match(/^[0-9]{10}$/)) {
        e.preventDefault();
        alert('Le numéro de téléphone doit contenir exactement 10 chiffres.');
    }
});
</script>

<?php
$content = ob_get_clean();
require('views/layouts/admin.php');
?>