<?php 
require_once('models/User.php');

// Vérifiez si l'utilisateur est connecté
if (isset($_SESSION['user_id'])) {
    $user = new User($this->db);
    $user_data = $user->getUserRole($_SESSION['user_id']);

    // Vérifiez si l'utilisateur existe et récupérez ses sections de barre latérale
    if ($user_data) {
        $role = $user_data['role_name'];
        $sidebar_sections = json_decode($user_data['sidebar_sections'], true);
    } else {
        $role = null;
        $sidebar_sections = [];
    }
} else {
    $role = null;
    $sidebar_sections = [];
}
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
        <img src="assets/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">GestionCommandes</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?= htmlspecialchars($_SESSION['username'] ?? 'Utilisateur'); ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Dashboard -->
                <?php if (in_array('dashboard', $sidebar_sections)): ?>
                <li class="nav-item menu-open">
                    <a href="#" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?action=dashboard" class="nav-link active">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Vue générale</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?action=dashboard&view=sales" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Statistiques ventes</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Commandes -->
                <?php if (in_array('commandes', $sidebar_sections)): ?>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Commandes<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?action=commandes" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Liste des commandes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?action=commandes/create" class="nav-link">
                                <i class="fa fa-plus nav-icon"></i>
                                <p>Nouvelle commande</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?action=commandes/import" class="nav-link">
                                <i class="fa fa-upload nav-icon"></i>
                                <p>Importer commandes</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?action=commandes/tracking" class="nav-link">
                                <i class="fa fa-calendar nav-icon"></i>
                                <p>Suivi des commandes</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Users -->
                <?php if (in_array('users', $sidebar_sections)): ?>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Users<i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="index.php?action=users" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Liste des Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?action=users/create" class="nav-link">
                                <i class="fa fa-plus nav-icon"></i>
                                <p>Nouvelle User</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
                
                <!-- Autres sections comme Clients, Configuration, Rapports ici -->
                <!-- ... -->
            </ul>
        </nav>
    </div>
</aside>
