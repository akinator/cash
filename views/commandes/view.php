<?php ob_start(); ?>
<section class="content-header">
   <div class="container-fluid">
       <div class="row mb-2">
           <div class="col-sm-6">
               <h1>Détails de la Commande</h1>
           </div>
           <div class="col-sm-6">
               <ol class="breadcrumb float-sm-right">
                   <li class="breadcrumb-item"><a href="index.php?action=dashboard">Home</a></li>
                   <li class="breadcrumb-item"><a href="index.php?action=commandes">Commandes</a></li>
                   <li class="breadcrumb-item active">Détails</li>
               </ol>
           </div>
       </div>
   </div>
</section>

<section class="content">
   <div class="container-fluid">
       <!-- Info boxes -->
       <div class="row">
           <div class="col-md-3">
               <div class="info-box">
                   <span class="info-box-icon bg-info"><i class="fas fa-shopping-cart"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text">Référence</span>
                       <span class="info-box-number"><?php echo $commande['reference']; ?></span>
                   </div>
               </div>
           </div>

           <div class="col-md-3">
               <div class="info-box">
                   <span class="info-box-icon bg-success"><i class="fas fa-euro-sign"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text">Montant</span>
                       <span class="info-box-number"><?php echo number_format($commande['montant'], 2); ?> €</span>
                   </div>
               </div>
           </div>

           <div class="col-md-3">
               <div class="info-box">
                   <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text">Statut</span>
                       <span class="info-box-number">
                        <?php echo OrderStatus::getStatusBadge($commande['statut']); ?>
                       </span>
                   </div>
               </div>
           </div>

           <div class="col-md-3">
               <div class="info-box">
                   <span class="info-box-icon bg-danger"><i class="fas fa-calendar"></i></span>
                   <div class="info-box-content">
                       <span class="info-box-text">Date de création</span>
                       <span class="info-box-number"><?php echo date('d/m/Y H:i', strtotime($commande['date_creation'])); ?></span>
                   </div>
               </div>
           </div>
       </div>

       <div class="row">
           <!-- Informations Client -->
           <div class="col-md-6">
               <div class="card">
                   <div class="card-header">
                       <h3 class="card-title">
                           <i class="fas fa-user mr-2"></i>
                           Informations Client
                       </h3>
                   </div>
                   <div class="card-body">
                       <table class="table table-bordered">
                           <tr>
                               <th style="width: 150px">Nom</th>
                               <td><?php echo $commande['nom']; ?></td>
                           </tr>
                           <tr>
                               <th>Téléphone</th>
                               <td><?php echo $commande['telephone']; ?></td>
                           </tr>
                           <tr>
                               <th>Adresse</th>
                               <td><?php echo $commande['adresse']; ?></td>
                           </tr>
                       </table>
                   </div>
               </div>
           </div>
                  <!-- Détails de la commande -->

           <div class="col-md-6">
               <div class="card">
                   <div class="card-header">
                       <h3 class="card-title">
                           <i class="fas fa-list mr-2"></i>
                           Détails de la commande
                       </h3>
                   </div>
                   <div class="card-body table-responsive">
                       <table class="table table-bordered">
                           <thead>
                               <tr>
                                   <th>Produit</th>
                                   <th>Quantité</th>
                                   <th>Total</th>
                               </tr>
                           </thead>
                           <tbody>
                               <tr>
                                   <td><?php echo $commande['produit']; ?></td>
                                   <td><?php echo '1' ?></td>
                                   <td><?php echo number_format($commande['montant'], 2); ?> €</td>
                               </tr>
                           </tbody>
                           <tfoot>

                               <tr>
                                   <th colspan="2" class="text-right">Total TTC</th>
                                   <td><strong><?php echo number_format($commande['montant'], 2); ?> €</strong></td>
                               </tr>
                           </tfoot>
                       </table>
                   </div>
               </div>
           </div>
<!-- Détails de la commande -->

           <div class="col-md-12">
               <div class="card">
                   <div class="card-header">
                       <h3 class="card-title">
                           <i class="fas fa-history mr-2"></i>
                           Historique des statuts
                       </h3>
                   </div>
                   <div class="card-body table-responsive p-0" style="height: 300px;">
                       <table class="table table-head-fixed text-nowrap">
                           <thead>
                               <tr>
                                   <th>Date</th>
                                   <th>Utilisateur</th>
                                   <th>Statut</th>
                                   <th>Commentaire</th>
                               </tr>
                           </thead>
                           <tbody>
                               <?php foreach($historique as $record): ?>
                               <tr>
                                   <td><?php echo date('d/m/Y H:i', strtotime($record['date'])); ?></td>
                                   <td><?php echo $record['username']; ?></td>
                                   <td>
                                       <span class="badge <?php echo getStatusBadgeClass($record['statut']); ?>">
                                           <?php echo $record['statut']; ?>
                                       </span>
                                   </td>
                                   <td><?php echo $record['commentaire']; ?></td>
                               </tr>
                               <?php endforeach; ?>
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>





    


        <!-- Boutons d'action -->
        <div class="mb-4">
            <a href="index.php?action=commandes" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <a href="index.php?action=commandes/edit&id=<?php echo $commande['id']; ?>" 
               class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
            <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $commande['id']; ?>)">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
   </div>
</section>

<script>
function confirmDelete(id) {
   if(confirm('Êtes-vous sûr de vouloir supprimer cette commande ?')) {
       window.location.href = 'index.php?action=commandes/delete&id=' + id;
   }
}
</script>

<?php
function getStatusBadgeClass($status) {
   switch($status) {
       case 'En attente':
           return 'badge-warning';
       case 'En cours':
           return 'badge-info';
       case 'Terminée':
           return 'badge-success';
       case 'Annulée':
           return 'badge-danger';
       default:
           return 'badge-secondary';
   }
}

$content = ob_get_clean();
require('views/layouts/admin.php');
?>