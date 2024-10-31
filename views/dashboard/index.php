<?php
// views/dashboard/index.php
ob_start();
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Tableau de Bord</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item">Tableau de bord</li>
                </ol>
            </div>
        </div>
    </div>
</div>


<section class="content">
    <!-- Boîtes d'information -->
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-info" style="background-color: #17a2b8 !important;">
                <div class="inner">
                    <h3><?php echo $stats['total_orders']; ?></h3>
                    <p>Commandes Totales</p>
                </div>
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <a href="index.php?action=commandes" class="small-box-footer">
                    Voir toutes les commandes <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-success" style="background-color: #28a745 !important;">
                <div class="inner">
                    <h3><?php echo number_format($stats['total_revenue'], 2); ?> €</h3>
                    <p>Chiffre d'Affaires</p>
                </div>
                <div class="icon">
                    <i class="fa fa-euro-sign"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Détails <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-warning" style="background-color: #ffc107 !important;">
                <div class="inner">
                    <h3><?php echo $stats['pending_orders']; ?></h3>
                    <p>Commandes en Attente</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clock"></i>
                </div>
                <a href="index.php?action=commandes&status=pending" class="small-box-footer">
                    Voir les commandes en attente <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="small-box bg-danger" style="background-color: #dc3545 !important;">
                <div class="inner">
                    <h3><?php echo $stats['total_customers']; ?></h3>
                    <p>Clients Actifs</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="index.php?action=clients" class="small-box-footer">
                    Voir les clients <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Graphique des commandes -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Évolution des Commandes
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="orderChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Statut des commandes -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Statut des Commandes
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Dernières commandes -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Dernières Commandes</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Référence</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recentOrders as $order): ?>
                                <tr>
                                    <td><?php echo $order['reference']; ?></td>
                                    <td><?php echo $order['nom']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($order['date_creation'])); ?></td>
                                    <td><?php echo number_format($order['montant'], 2); ?> €</td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($order['statut']); ?>">
                                            <?php echo $order['statut']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="index.php?action=commandes/edit&id=<?php echo $order['id']; ?>" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="index.php?action=commandes/view&id=<?php echo $order['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
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

<script>
// Graphique d'évolution des commandes
var orderCtx = document.getElementById('orderChart').getContext('2d');
var orderChart = new Chart(orderCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column(array_reverse($monthlyStats), 'month')); ?>,
        datasets: [{
            label: 'Nombre de commandes',
            data: <?php echo json_encode(array_column(array_reverse($monthlyStats), 'total_orders')); ?>,
            borderColor: '#17a2b8',
            backgroundColor: 'rgba(23, 162, 184, 0.2)',
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Graphique des statuts
var statusCtx = document.getElementById('statusChart').getContext('2d');
var statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['En attente', 'En cours', 'Terminées', 'Annulées'],
        datasets: [{
            data: [
                <?php echo $stats['pending_orders']; ?>,
                <?php echo $stats['processing_orders']; ?>,
                <?php echo $stats['completed_orders']; ?>,
                <?php echo $stats['cancelled_orders']; ?>
            ],
            backgroundColor: ['#ffc107', '#17a2b8', '#28a745', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
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