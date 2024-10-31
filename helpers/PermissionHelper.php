<?php
// helpers/PermissionHelper.php

class PermissionHelper {
    public static function getPermissions() {
        if (!isset($_SESSION['permissions'])) {
            return [];
        }
        $permissions = $_SESSION['permissions'];
        return is_string($permissions) ? json_decode($permissions, true) : $permissions;
    }

    public static function getSidebarSections() {
        if (!isset($_SESSION['sidebar_sections'])) {
            return [];
        }
        $sections = $_SESSION['sidebar_sections'];
        return is_string($sections) ? json_decode($sections, true) : $sections;
    }

    public static function can($module, $action) {
        // Si c'est un admin, accès total
        if (self::isAdmin()) {
            return true;
        }

        $permissions = self::getPermissions();
        
        // Debug log
        error_log("Module: " . $module . ", Action: " . $action);
        error_log("Permissions: " . print_r($permissions, true));
        
        if (isset($permissions[$module])) {
            return isset($permissions[$module][$action]) && $permissions[$module][$action] === true;
        }
        
        return false;
    }

    public static function hasSection($section) {
        $sections = self::getSidebarSections();
        return in_array($section, $sections);
    }

    public static function isAdmin() {
        return isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'admin';
    }

    public static function renderSidebarSection($section, $icon, $title, $items = []) {
        if (!self::hasSection($section)) {
            return '';
        }

        $currentPage = $_GET['action'] ?? 'dashboard';
        $isActive = $currentPage === $section;
        $hasSubmenu = !empty($items);

        $html = '<li class="nav-item' . ($hasSubmenu ? ' has-treeview' : '') . ($isActive ? ' menu-open' : '') . '">';
        
        // Menu principal
        $html .= '<a href="#" class="nav-link' . ($isActive ? ' active' : '') . '">';
        $html .= '<i class="nav-icon ' . htmlspecialchars($icon) . '"></i>';
        $html .= '<p>' . htmlspecialchars($title);
        if ($hasSubmenu) {
            $html .= '<i class="right fas fa-angle-left"></i>';
        }
        $html .= '</p></a>';

        // Sous-menu
        if ($hasSubmenu) {
            $html .= '<ul class="nav nav-treeview">';
            foreach ($items as $item) {
                if (!isset($item['permission']) || self::can($section, $item['permission'])) {
                    $isItemActive = $currentPage === $item['url'];
                    $html .= '<li class="nav-item">';
                    $html .= '<a href="index.php?action=' . htmlspecialchars($item['url']) . '" ';
                    $html .= 'class="nav-link' . ($isItemActive ? ' active' : '') . '">';
                    $html .= '<i class="' . htmlspecialchars($item['icon']) . ' nav-icon"></i>';
                    $html .= '<p>' . htmlspecialchars($item['label']) . '</p>';
                    $html .= '</a></li>';
                }
            }
            $html .= '</ul>';
        }

        $html .= '</li>';
        return $html;
    }
    public static function canPerformAction($action) {
        if (!isset($_SESSION['actions'])) {
            return false;
        }
        $actions = is_string($_SESSION['actions']) ? 
            json_decode($_SESSION['actions'], true) : 
            $_SESSION['actions'];
            
        return isset($actions[$action]) && $actions[$action] === true;
    }
}