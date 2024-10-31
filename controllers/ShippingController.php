<?php

class ShippingController {
    private $db;
    private $table_name = "commandes";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Affiche les commandes en cours d'expédition
    public function index() {
        try {
            // Récupérer les commandes en cours
        $query = "SELECT c.*, ch.date as date_expedition 
                 FROM " . $this->table_name . " c 
                 LEFT JOIN (
                     SELECT commande_id, date
                     FROM commandes_historique 
                     WHERE statut = :statut_expedie 
                     AND DATE(date) = CURRENT_DATE()
                 ) ch ON c.id = ch.commande_id 
                 ORDER BY c.date_creation DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':statut_expedie', OrderStatus::EXPEDIE);
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Calculer les statistiques
            $stats = $this->getShippingStats();

            require_once('views/shipping/index.php');
        } catch(PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la récupération des commandes : " . $e->getMessage();
            header('Location: index.php?action=shipping');
            exit();
        }
    }

    // Affiche les commandes expédiées
    public function shipped() {
        try {
            $date_debut = $_GET['date_debut'] ?? date('Y-m-d', strtotime('-30 days'));
            $date_fin = $_GET['date_fin'] ?? date('Y-m-d');

            $query = "SELECT c.*, ch.date as date_expedition, ch.username 
                     FROM " . $this->table_name . " c
                     JOIN (
                         SELECT commande_id, date, username,
                                ROW_NUMBER() OVER (PARTITION BY commande_id ORDER BY date DESC) as rn
                         FROM commandes_historique 
                         WHERE statut = :statut
                     ) ch ON c.id = ch.commande_id AND ch.rn = 1
                     WHERE DATE(ch.date) BETWEEN :date_debut AND :date_fin
                     ORDER BY ch.date DESC";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':statut', OrderStatus::EXPEDIE);
            $stmt->bindValue(':date_debut', $date_debut);
            $stmt->bindValue(':date_fin', $date_fin);
            $stmt->execute();
            $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stats = $this->getShippingStats();

            require_once('views/shipping/shipped.php');
        } catch(PDOException $e) {
            $_SESSION['error'] = "Erreur lors de la récupération des commandes : " . $e->getMessage();
            header('Location: index.php?action=shipping');
            exit();
        }
    }

    // Mettre à jour le statut d'une commande
 public function updateStatus() {
    try {
        // Vérification de la requête
        if(!isset($_POST['id']) || !isset($_POST['status'])) {
            throw new Exception("Données manquantes");
        }

        if(!isset($_SESSION['user_id'])) {
            throw new Exception("Utilisateur non authentifié");
        }

        $id = $_POST['id'];
        $newStatus = $_POST['status'];

        // Debug
        error_log("Tentative de mise à jour - ID: $id, Nouveau statut: $newStatus");

        // Vérification du statut
        if(!OrderStatus::isValidStatus($newStatus)) {
            throw new Exception("Statut invalide: $newStatus");
        }

        // Récupérer l'ancien statut
        $query = "SELECT statut FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new Exception("Commande non trouvée");
        }
        
        $oldStatus = $result['statut'];

        // Commencer une transaction
        $this->db->beginTransaction();

        try {
            // Mettre à jour le statut
            $query = "UPDATE " . $this->table_name . " SET statut = :statut WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':statut', $newStatus);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Ajouter dans l'historique
            $commentaire = "Changement de statut de '{$oldStatus}' à '{$newStatus}'";
            $this->addHistorique($id, $newStatus, $commentaire);

            $this->db->commit();

            // Réponse JSON
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'data' => [
                    'id' => $id,
                    'oldStatus' => $oldStatus,
                    'newStatus' => $newStatus,
                    'user' => $_SESSION['username']
                ]
            ]);
            exit;

        } catch(Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

    } catch(Exception $e) {
        error_log("Erreur lors de la mise à jour du statut: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

    // Statistiques des expéditions
    private function getShippingStats() {
        $stats = [
            'en_cours' => 0,
            'expedie_today' => 0,
            'expedie_week' => 0,
            'expedie_month' => 0
        ];

        // Commandes en cours
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                 WHERE statut = :statut";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':statut', OrderStatus::EN_COURS);
        $stmt->execute();
        $stats['en_cours'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Expéditions du jour
        $query = "SELECT COUNT(DISTINCT commande_id) as total 
                 FROM commandes_historique 
                 WHERE statut = :statut 
                 AND DATE(date) = CURRENT_DATE()";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':statut', OrderStatus::EXPEDIE);
        $stmt->execute();
        $stats['expedie_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Statistiques de la semaine et du mois
        $stats['expedie_week'] = $this->getExpeditionCount('WEEK');
        $stats['expedie_month'] = $this->getExpeditionCount('MONTH');

        return $stats;
    }

    private function getExpeditionCount($period) {
        $query = "SELECT COUNT(DISTINCT commande_id) as total 
                 FROM commandes_historique 
                 WHERE statut = :statut 
                 AND CASE 
                     WHEN :period = 'WEEK' THEN YEARWEEK(date) = YEARWEEK(CURRENT_DATE)
                     WHEN :period = 'MONTH' THEN MONTH(date) = MONTH(CURRENT_DATE) 
                                               AND YEAR(date) = YEAR(CURRENT_DATE)
                 END";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':statut', OrderStatus::EXPEDIE);
        $stmt->bindValue(':period', $period);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Ajout de la fonction confirm au ShippingController.php


public function confirm() {
    try {
        // Récupérer les commandes confirmées qui n'ont pas encore été traitées
        $query = "SELECT c.*, 
                        COALESCE(ch.date, c.date_creation) as date_confirmation,
                        COALESCE(ch.username, 'Système') as username
                 FROM " . $this->table_name . " c
                 LEFT JOIN (
                     SELECT commande_id, date, username,
                            ROW_NUMBER() OVER (PARTITION BY commande_id ORDER BY date DESC) as rn
                     FROM commandes_historique 
                     WHERE statut = :statut_confirme
                 ) ch ON c.id = ch.commande_id AND ch.rn = 1
                 WHERE c.statut = :statut_confirme
                 ORDER BY COALESCE(ch.date, c.date_creation) DESC";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':statut_confirme', 'CONFIRME');
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug pour voir la structure de la table
        if (empty($commandes)) {
            // Récupérer la structure de la table
            $query_structure = "DESCRIBE " . $this->table_name;
            $stmt = $this->db->prepare($query_structure);
            $stmt->execute();
            $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Structure de la table commandes : " . print_r($structure, true));
        }

        // Calculer les statistiques
        $stats = [
            'confirmes' => $this->getConfirmedCount(),
            'en_cours' => $this->getInProgressCount(),
            'expedie_today' => $this->getShippedTodayCount()
        ];

        require_once('views/shipping/confirmed.php');
    } catch(PDOException $e) {
        error_log("Erreur SQL dans confirm(): " . $e->getMessage());
        $_SESSION['error'] = "Erreur lors de la récupération des commandes confirmées : " . $e->getMessage();
        header('Location: index.php?action=shipping');
        exit();
    }
}
// Méthodes auxiliaires pour les statistiques
private function getConfirmedCount() {
    $query = "SELECT COUNT(*) as total 
             FROM " . $this->table_name . " 
             WHERE statut = :statut_confirme
             AND NOT EXISTS (
                 SELECT 1 FROM commandes_historique 
                 WHERE commande_id = " . $this->table_name . ".id 
                 AND statut IN (:statut_en_cours, :statut_expedie)
             )";

    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':statut_confirme', OrderStatus::CONFIRME);
    $stmt->bindValue(':statut_en_cours', OrderStatus::EN_COURS);
    $stmt->bindValue(':statut_expedie', OrderStatus::EXPEDIE);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

private function getInProgressCount() {
    $query = "SELECT COUNT(*) as total 
             FROM " . $this->table_name . " 
             WHERE statut = :statut_en_cours";

    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':statut_en_cours', OrderStatus::EN_COURS);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

private function getShippedTodayCount() {
    $query = "SELECT COUNT(DISTINCT commande_id) as total 
             FROM commandes_historique 
             WHERE statut = :statut_expedie 
             AND DATE(date) = CURRENT_DATE()";

    $stmt = $this->db->prepare($query);
    $stmt->bindValue(':statut_expedie', OrderStatus::EXPEDIE);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

// Méthode pour ajouter un historique (si elle n'existe pas déjà)
private function addHistorique($commande_id, $statut, $commentaire = null) {
    $query = "INSERT INTO commandes_historique 
             (commande_id, statut, commentaire, username, date)
             VALUES (:commande_id, :statut, :commentaire, :username, NOW())";

    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':commande_id', $commande_id);
    $stmt->bindParam(':statut', $statut);
    $stmt->bindParam(':commentaire', $commentaire);
    $stmt->bindParam(':username', $_SESSION['username']);
    return $stmt->execute();
}
}