<?php
class DashboardController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        try {
            // Récupérer les statistiques
            $stats = $this->getStats();
            $recentOrders = $this->getRecentOrders();
            $monthlyStats = $this->getMonthlyStats();
            
            // Inclure la vue
            require_once('views/dashboard/index.php');
        } catch(Exception $e) {
            echo "Une erreur est survenue : " . $e->getMessage();
        }
    }

    private function getStats() {
    $stats = [];
    
    try {
        // Total des commandes
        $query = "SELECT COUNT(*) as total FROM commandes";
        $stmt = $this->db->query($query);
        $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Chiffre d'affaires total
        $query = "SELECT COALESCE(SUM(montant), 0) as revenue FROM commandes";
        $stmt = $this->db->query($query);
        $stats['total_revenue'] = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'];

        // Commandes par statut
        $query = "SELECT statut, COUNT(*) as count FROM commandes GROUP BY statut";
        $stmt = $this->db->query($query);
        $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stats['pending_orders'] = 0;
        $stats['processing_orders'] = 0;
        $stats['completed_orders'] = 0;
        $stats['cancelled_orders'] = 0;
        
        foreach($statusCounts as $status) {
            switch($status['statut']) {
                case 'En attente':
                    $stats['pending_orders'] = $status['count'];
                    break;
                case 'En cours':
                    $stats['processing_orders'] = $status['count'];
                    break;
                case 'Terminée':
                    $stats['completed_orders'] = $status['count'];
                    break;
                case 'Annulée':
                    $stats['cancelled_orders'] = $status['count'];
                    break;
            }
        }

        // Clients actifs (avec au moins une commande)
        $query = "SELECT COUNT(DISTINCT client_id) as total FROM commandes";
        $stmt = $this->db->query($query);
        $stats['total_customers'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    } catch(Exception $e) {
        error_log("Erreur dans getStats: " . $e->getMessage());
    }

    return $stats;
}

    private function getRecentOrders() {
        try {
            $query = "SELECT * FROM commandes ORDER BY date_creation DESC LIMIT 5";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            error_log("Erreur dans getRecentOrders: " . $e->getMessage());
            return [];
        }
    }

    private function getMonthlyStats() {
        try {
            $query = "SELECT 
                        DATE_FORMAT(date_creation, '%Y-%m') as month,
                        COUNT(*) as total_orders,
                        COALESCE(SUM(montant), 0) as revenue
                     FROM commandes 
                     GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
                     ORDER BY month DESC 
                     LIMIT 6";
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(Exception $e) {
            error_log("Erreur dans getMonthlyStats: " . $e->getMessage());
            return [];
        }
    }
}