<?php
// views/layouts/sidebar.php
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="index.php" class="brand-link">
        <img src="assets/dist/img/AdminLTELogo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">ERP</span>
    </a>

    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="assets/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block">
                    <?= htmlspecialchars($_SESSION['username'] ?? 'Utilisateur') ?>
                    <small class="d-block text-muted"><?= htmlspecialchars($_SESSION['role_name'] ?? '') ?></small>
                </a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php
                // Dashboard Section
                echo PermissionHelper::renderSidebarSection('dashboard', 'fas fa-tachometer-alt', 'Dashboard', [
                    [
                        'url' => 'dashboard',
                        'icon' => 'far fa-circle',
                        'label' => 'Vue générale',
                        'permission' => 'view'
                    ],
                    [
                        'url' => 'dashboard&view=stats',
                        'icon' => 'far fa-chart-bar',
                        'label' => 'Statistiques',
                        'permission' => 'stats'
                    ]
                ]);

                // Users Section
                echo PermissionHelper::renderSidebarSection('users', 'fas fa-users', 'Utilisateurs', [
                    [
                        'url' => 'users',
                        'icon' => 'far fa-circle',
                        'label' => 'Liste des utilisateurs',
                        'permission' => 'view'
                    ],
                    [
                        'url' => 'users/create',
                        'icon' => 'fas fa-plus',
                        'label' => 'Nouvel utilisateur',
                        'permission' => 'create'
                    ]
                ]);

                // Commandes Section
                echo PermissionHelper::renderSidebarSection('commandes', 'fas fa-users', 'Commandes', [
                    [
                        'url' => 'commandes',
                        'icon' => 'far fa-circle',
                        'label' => 'Liste des commandes',
                        'permission' => 'view'
                    ],
                    [
                        'url' => 'commandes/create',
                        'icon' => 'fas fa-plus',
                        'label' => 'Nouvel commandes',
                        'permission' => 'create'
                    ]
                ]);

                // Expedition Section
                echo PermissionHelper::renderSidebarSection('expedition', 'fas fa-users', 'Expedition', [
                    [
                        'url' => 'expedition',
                        'icon' => 'far fa-circle',
                        'label' => 'Liste des Expedition',
                        'permission' => 'view'
                    ],
                    [
                        'url' => 'expedition/create',
                        'icon' => 'fas fa-plus',
                        'label' => 'Nouvel Expedition',
                        'permission' => 'confirm'
                    ]
                ]);


                // Roles Section
                echo PermissionHelper::renderSidebarSection('roles', 'fas fa-user-shield', 'Rôles', [
                    [
                        'url' => 'roles',
                        'icon' => 'far fa-circle',
                        'label' => 'Liste des rôles',
                        'permission' => 'view'
                    ],
                    [
                        'url' => 'roles/create',
                        'icon' => 'fas fa-plus',
                        'label' => 'Nouveau rôle',
                        'permission' => 'create'
                    ]
                ]);

                // Configuration Section
                if (PermissionHelper::isAdmin()) {
                    echo PermissionHelper::renderSidebarSection('settings', 'fas fa-cogs', 'Configuration', [
                        [
                            'url' => 'settings',
                            'icon' => 'fas fa-sliders-h',
                            'label' => 'Paramètres généraux',
                            'permission' => 'view'
                        ],
                        [
                            'url' => 'settings/system',
                            'icon' => 'fas fa-server',
                            'label' => 'Système',
                            'permission' => 'view'
                        ]
                    ]);
                }
                ?>
            </ul>
        </nav>
    </div>
</aside>