<?php

require 'vendor/autoload.php';
require_once 'models/OrderStatus.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CommandeController {
    private $db;
    private $table_name = "commandes";

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

public function index() {
    try {
        // Initialisation des filtres
        $filtres = [
            'nom' => $_GET['nom'] ?? '',
            'telephone' => $_GET['telephone'] ?? '',
            'statut' => $_GET['statut'] ?? '',
            'periode' => $_GET['periode'] ?? '',
            'tri' => $_GET['tri'] ?? 'date_desc'
        ];

        // Paramètres de pagination
        $resultsPerPage = 10;
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($currentPage - 1) * $resultsPerPage;

        // Construction de la requête de base
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = array();

        // Ajout des conditions de filtrage
        if (!empty($filtres['nom'])) {
            $query .= " AND nom LIKE :nom";
            $params[':nom'] = '%' . $filtres['nom'] . '%';
        }

        if (!empty($filtres['telephone'])) {
            $query .= " AND telephone LIKE :telephone";
            $params[':telephone'] = '%' . $filtres['telephone'] . '%';
        }

        if (!empty($filtres['statut'])) {
            $query .= " AND statut = :statut";
            $params[':statut'] = $filtres['statut'];
        }

        // Ajout du tri
        switch ($filtres['tri']) {
            case 'date_asc':
                $query .= " ORDER BY date_creation ASC";
                break;
            case 'date_desc':
            default:
                $query .= " ORDER BY date_creation DESC";
                break;
        }

        // Ajout de la pagination
        $query .= " LIMIT :offset, :limit";

        // Préparation et exécution de la requête
        $stmt = $this->db->prepare($query);
        
        // Bind des paramètres de filtrage
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Bind des paramètres de pagination
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
        
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calcul du nombre total de résultats pour la pagination
        $countQuery = str_replace(" LIMIT :offset, :limit", "", $query);
        $countQuery = "SELECT COUNT(*) as total FROM (" . $countQuery . ") as t";
        $stmtCount = $this->db->prepare($countQuery);
        
        // Bind des paramètres pour la requête de compte
        foreach ($params as $key => $value) {
            $stmtCount->bindValue($key, $value);
        }
        
        $stmtCount->execute();
        $totalResultats = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
        
        $totalPages = ceil($totalResultats / $resultsPerPage);

        // Passage des variables à la vue
        require_once('views/commandes/index.php');

    } catch(PDOException $e) {
        error_log("Erreur dans CommandeController::index : " . $e->getMessage());
        // Initialiser un tableau vide en cas d'erreur
        $commandes = [];
        $filtres = [
            'nom' => '',
            'telephone' => '',
            'statut' => '',
            'periode' => '',
            'tri' => 'date_desc'
        ];
        $totalResultats = 0;
        $totalPages = 1;
        require_once('views/commandes/index.php');
    }
}

private function generatePageUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return 'index.php?' . http_build_query($params);
}

    private function sendNotification($type, $message) {
    $_SESSION['notification'] = [
        'type' => $type,
        'message' => $message
    ];
    }

    public function create() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            // Générer une référence unique
            $reference = "CMD" . date("YmdHis");
            
            $query = "INSERT INTO " . $this->table_name . " 
                     (reference, produit, nom, telephone, adresse, montant, 
                      statut, notes, date_creation) 
                     VALUES 
                     (:reference, :produit, :nom, :telephone, :adresse, :montant, 
                      :statut, :notes, NOW())";
            
            $stmt = $this->db->prepare($query);
            
            // Nettoyer et valider les données
            $nom = htmlspecialchars(strip_tags($_POST['nom']));
            $telephone = htmlspecialchars(strip_tags($_POST['telephone']));
            $adresse = htmlspecialchars(strip_tags($_POST['adresse']));
            $produit = htmlspecialchars(strip_tags($_POST['produit']));
            $montant = htmlspecialchars(strip_tags($_POST['montant']));
            $statut = htmlspecialchars(strip_tags($_POST['statut']));
            $notes = htmlspecialchars(strip_tags($_POST['notes'] ?? ''));
            
            // Lier les paramètres
            $stmt->bindParam(":reference", $reference);
            $stmt->bindParam(":produit", $produit);
            $stmt->bindParam(":nom", $nom);
            $stmt->bindParam(":telephone", $telephone);
            $stmt->bindParam(":adresse", $adresse);
            $stmt->bindParam(":montant", $montant);
            $stmt->bindParam(":statut", $statut);
            $stmt->bindParam(":notes", $notes);
            
            if($stmt->execute()) {
                $commande_id = $this->db->lastInsertId();
                $this->addHistorique($commande_id, $statut, "Création de la commande");
                $this->sendNotification('success', 'La commande a été créée avec succès.');
                header("Location: index.php?action=commandes");
                exit();
            }
        } catch(PDOException $e) {
            $this->sendNotification('error', 'Erreur lors de la création de la commande.');
        }
    }
    require_once('views/commandes/create.php');
}

    public function edit($id) {
    if(!$id) {
        header("Location: index.php?action=commandes");
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET nom = :nom,
                         telephone = :telephone,
                         adresse = :adresse,
                         produit = :produit,
                         montant = :montant,
                         statut = :statut,
                         notes = :notes
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            
            // Nettoyer et valider les données
            $nom = htmlspecialchars(strip_tags($_POST['nom']));
            $telephone = htmlspecialchars(strip_tags($_POST['telephone']));
            $adresse = htmlspecialchars(strip_tags($_POST['adresse']));
            $produit = htmlspecialchars(strip_tags($_POST['produit']));
            $montant = htmlspecialchars(strip_tags($_POST['montant']));
            $statut = htmlspecialchars(strip_tags($_POST['statut']));
            $notes = htmlspecialchars(strip_tags($_POST['notes'] ?? ''));
            
            // Lier les paramètres
            $stmt->bindParam(":nom", $nom);
            $stmt->bindParam(":telephone", $telephone);
            $stmt->bindParam(":adresse", $adresse);
            $stmt->bindParam(":produit", $produit);
            $stmt->bindParam(":montant", $montant);
            $stmt->bindParam(":statut", $statut);
            $stmt->bindParam(":notes", $notes);
            $stmt->bindParam(":id", $id);
            
            if($stmt->execute()) {
                $this->addHistorique($id, $statut, "Modification de la commande");
                $this->sendNotification('success', 'La commande a été modifiée avec succès.');
                header("Location: index.php?action=commandes/view&id=" . $id);
                exit();
            }
        } catch(PDOException $e) {
            $this->sendNotification('error', 'Erreur lors de la modification de la commande.');
        }
    }
    
    // Récupérer les données de la commande
    $commande = $this->getCommande($id);
    require_once('views/commandes/edit.php');
}

    public function view($id) {
    try {
        if (!$id) {
            header('Location: index.php?action=commandes');
            exit();
        }

        // Récupérer les détails de la commande
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $commande = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$commande) {
            throw new Exception("Commande non trouvée");
        }

        // Récupérer l'historique des statuts si la table existe
        $historique = [];
        try {
            $query = "SELECT * FROM commandes_historique 
                     WHERE commande_id = :commande_id 
                     ORDER BY date DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':commande_id', $id);
            $stmt->execute();
            $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Si la table n'existe pas ou autre erreur, on continue sans l'historique
            error_log("Erreur lors de la récupération de l'historique : " . $e->getMessage());
        }

        require_once('views/commandes/view.php');
    } catch(Exception $e) {
        $_SESSION['error'] = "Erreur : " . $e->getMessage();
        header('Location: index.php?action=commandes');
        exit();
    }
}

public function delete($id) {
    try {
        if(!$id) {
            $_SESSION['error'] = "ID de commande manquant";
            header('Location: index.php?action=commandes');
            exit();
        }

        // Démarrer une transaction
        $this->db->beginTransaction();

        try {
            // Vérifier si la commande existe
            $query = "SELECT reference FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if(!$stmt->fetch()) {
                throw new Exception("Commande introuvable");
            }

            // 1. D'abord supprimer l'historique
            $query = "DELETE FROM commandes_historique WHERE commande_id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // 2. Ensuite supprimer la commande
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Si tout s'est bien passé, on valide la transaction
            $this->db->commit();
            $_SESSION['success'] = "La commande et son historique ont été supprimés avec succès";

        } catch(Exception $e) {
            // En cas d'erreur, on annule la transaction
            $this->db->rollBack();
            throw $e;
        }

    } catch(Exception $e) {
        $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
    }

    header('Location: index.php?action=commandes');
    exit();
}

    private function getCommande($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getHistorique($commande_id) {
        $query = "SELECT * FROM commandes_historique 
                 WHERE commande_id = :commande_id 
                 ORDER BY date DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":commande_id", $commande_id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

private function addHistorique($commande_id, $statut, $commentaire) {
    $query = "INSERT INTO commandes_historique 
             (commande_id, statut, commentaire, date, user_id, username) 
             VALUES (:commande_id, :statut, :commentaire, NOW(), :user_id, :username)";
    
    $stmt = $this->db->prepare($query);
    
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? 'Système';
    
    $stmt->bindParam(":commande_id", $commande_id);
    $stmt->bindParam(":statut", $statut);
    $stmt->bindParam(":commentaire", $commentaire);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":username", $username);
    
    return $stmt->execute();
}

    public function export($format = 'excel') {
    try {
        $query = "SELECT c.*, DATE_FORMAT(c.date_creation, '%d/%m/%Y') as date_formatted 
                 FROM " . $this->table_name . " c 
                 ORDER BY c.date_creation DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($format === 'excel') {
            // Création du fichier Excel
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="commandes.xlsx"');
            header('Cache-Control: max-age=0');

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // En-têtes
            $sheet->setCellValue('A1', 'Référence');
            $sheet->setCellValue('B1', 'Client');
            $sheet->setCellValue('C1', 'Téléphone');
            $sheet->setCellValue('D1', 'Montant TTC');
            $sheet->setCellValue('E1', 'Date');
            $sheet->setCellValue('F1', 'Statut');

            // Données
            $row = 2;
            foreach($commandes as $commande) {
                $sheet->setCellValue('A'.$row, $commande['reference']);
                $sheet->setCellValue('B'.$row, $commande['nom']);
                $sheet->setCellValue('C'.$row, $commande['telephone']);
                $sheet->setCellValue('D'.$row, $commande['montant']);
                $sheet->setCellValue('E'.$row, $commande['date_formatted']);
                $sheet->setCellValue('F'.$row, $commande['statut']);
                $row++;
            }

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        }
    } catch(Exception $e) {
        echo "Erreur lors de l'export : " . $e->getMessage();
    }
}

// Ajout de la fonction pour générer des statistiques
public function getStats() {
    try {
        $stats = [];
        
        // Total des commandes
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->db->query($query);
        $stats['total_commandes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Chiffre d'affaires total
        $query = "SELECT SUM(montant) as ca_total FROM " . $this->table_name;
        $stmt = $this->db->query($query);
        $stats['ca_total'] = $stmt->fetch(PDO::FETCH_ASSOC)['ca_total'];

        // Commandes par statut
        $query = "SELECT statut, COUNT(*) as nombre FROM " . $this->table_name . " GROUP BY statut";
        $stmt = $this->db->query($query);
        $stats['par_statut'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Évolution mensuelle
        $query = "SELECT DATE_FORMAT(date_creation, '%Y-%m') as mois, 
                        COUNT(*) as nombre, 
                        SUM(montant) as ca 
                 FROM " . $this->table_name . "
                 GROUP BY DATE_FORMAT(date_creation, '%Y-%m')
                 ORDER BY mois DESC
                 LIMIT 12";
        $stmt = $this->db->query($query);
        $stats['evolution_mensuelle'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $stats;
    } catch(Exception $e) {
        error_log("Erreur dans getStats: " . $e->getMessage());
        return null;
    }
}

private function validateCommande($data) {
    $errors = [];
    
    if(empty($data['nom'])) {
        $errors[] = "Le nom du client est obligatoire";
    }

    if(empty($data['produit'])) {
        $errors[] = "Le produit est obligatoire";
    }
    
    if(!empty($data['telephone']) && !preg_match("/^[0-9]{10}$/", $data['telephone'])) {
        $errors[] = "Le numéro de téléphone doit contenir exactement 10 chiffres";
    }
    
    if(!is_numeric($data['montant']) || $data['montant'] <= 0) {
        $errors[] = "Le montant doit être un nombre positif";
    }
    
    if(!OrderStatus::isValidStatus($data['statut'])) {
            $errors[] = "Le statut n'est pas valide";
    }
    
    return $errors;
}

public function generatePDF($id) {
    require_once 'vendor/tecnickcom/tcpdf/tcpdf.php';

    try {
        $commande = $this->getCommande($id);
        $historique = $this->getHistorique($id);

        // Création du PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Métadonnées du document
        $pdf->SetCreator('GestionCommandes');
        $pdf->SetAuthor('Votre Entreprise');
        $pdf->SetTitle('Commande ' . $commande['reference']);

        // En-tête et pied de page
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(true);

        // Nouvelle page
        $pdf->AddPage();

        // En-tête du document
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 10, 'Commande ' . $commande['reference'], 0, 1, 'C');
        $pdf->Ln(10);

        // Informations de la commande
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Informations de la commande', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Cell(50, 7, 'Date:', 0);
        $pdf->Cell(0, 7, date('d/m/Y H:i', strtotime($commande['date_creation'])), 0, 1);
        
        $pdf->Cell(50, 7, 'Statut:', 0);
        $pdf->Cell(0, 7, $commande['statut'], 0, 1);

        // Informations client
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Informations client', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Cell(50, 7, 'Nom:', 0);
        $pdf->Cell(0, 7, $commande['nom'], 0, 1);
        
        $pdf->Cell(50, 7, 'Téléphone:', 0);
        $pdf->Cell(0, 7, $commande['telephone'], 0, 1);
        
        $pdf->Cell(50, 7, 'Adresse:', 0);
        $pdf->MultiCell(0, 7, $commande['adresse'], 0, 'L');

        // Détails financiers
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Détails financiers', 0, 1, 'L');
        $pdf->SetFont('helvetica', '', 10);
        
        $pdf->Cell(50, 7, 'Montant HT:', 0);
        $pdf->Cell(0, 7, number_format($commande['montant_ht'], 2) . ' €', 0, 1);
        
        $pdf->Cell(50, 7, 'TVA:', 0);
        $pdf->Cell(0, 7, number_format($commande['montant_tva'], 2) . ' €', 0, 1);
        
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(50, 7, 'Montant TTC:', 0);
        $pdf->Cell(0, 7, number_format($commande['montant'], 2) . ' €', 0, 1);

        // Historique
        if (!empty($historique)) {
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Historique des statuts', 0, 1, 'L');
            
            $pdf->SetFont('helvetica', '', 10);
            foreach($historique as $h) {
                $pdf->Cell(40, 7, date('d/m/Y H:i', strtotime($h['date'])), 0);
                $pdf->Cell(30, 7, $h['statut'], 0);
                $pdf->MultiCell(0, 7, $h['commentaire'], 0, 'L');
            }
        }

        // Notes
        if (!empty($commande['notes'])) {
            $pdf->Ln(5);
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'Notes', 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 7, $commande['notes'], 0, 'L');
        }

        // Génération du PDF
        $pdf->Output('commande_' . $commande['reference'] . '.pdf', 'D');
        
    } catch(Exception $e) {
        $this->sendNotification('error', 'Erreur lors de la génération du PDF: ' . $e->getMessage());
        header('Location: index.php?action=commandes/view&id=' . $id);
        exit();
    }
}

public function updateStatus() {
    try {
        if(!isset($_POST['id']) || !isset($_POST['status'])) {
            throw new Exception("Données manquantes");
        }

        if(!OrderStatus::isValidStatus($_POST['status'])) {
                throw new Exception("Statut invalide");
        }

        if(!isset($_SESSION['user_id'])) {
            throw new Exception("Utilisateur non authentifié");
        }

        $id = $_POST['id'];
        $newStatus = $_POST['status'];

        // Récupérer l'ancien statut
        $query = "SELECT statut FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $oldStatus = $stmt->fetch(PDO::FETCH_ASSOC)['statut'];

        // Vérifier si le statut est valide
        /*$validStatuts = ['En attente', 'En cours', 'Terminée', 'Annulée'];
        if(!in_array($newStatus, $validStatuts)) {
            throw new Exception("Statut invalide");
        }*/

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

            // Retourner une réponse JSON
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'user' => [
                    'id' => $_SESSION['user_id'],
                    'username' => $_SESSION['username']
                ]
            ]);
        } catch(Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

    } catch(Exception $e) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}

public function template() {
        // Création du template Excel pour l'import
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // En-têtes
        $headers = ['Produit', 'Nom du client', 'Téléphone', 'Adresse', 'Montant TTC', 'Statut'];
        $sheet->fromArray($headers, NULL, 'A1');
        
        // Exemple de ligne
        $example = ['Produit exemple', 'Jean Dupont', '0612345678', '123 rue Example', '99.99', 'En attente'];
        $sheet->fromArray($example, NULL, 'A2');
        
        // Styling
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        
        // Ajuster la largeur des colonnes
        foreach(range('A','F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Headers HTTP pour le téléchargement
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_commandes.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    public function import() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        try {
            if (!isset($_FILES['importFile']) || $_FILES['importFile']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Erreur lors du téléchargement du fichier");
            }

            $inputFileName = $_FILES['importFile']['tmp_name'];
            
            // Lecture du fichier Excel
            $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            // Supprimer l'en-tête
            array_shift($rows);
            
            $this->db->beginTransaction();
            $importCount = 0;
            
            foreach ($rows as $row) {
                if (empty($row[0])) continue; // Ignorer les lignes vides
                
                $reference = "CMD" . date("YmdHis") . rand(100, 999);
                
                $query = "INSERT INTO " . $this->table_name . " 
                         (reference, produit, nom, telephone, adresse, montant, statut, date_creation)
                         VALUES (:reference, :produit, :nom, :telephone, :adresse, :montant, :statut, NOW())";
                
                $stmt = $this->db->prepare($query);
                
                $stmt->execute([
                    ':reference' => $reference,
                    ':produit' => $row[0],
                    ':nom' => $row[1],
                    ':telephone' => $row[2],
                    ':adresse' => $row[3],
                    ':montant' => $row[4],
                    ':statut' => $row[5]
                ]);
                
                $commande_id = $this->db->lastInsertId();
                $this->addHistorique($commande_id, $row[5], "Commande importée");
                $importCount++;
            }
            
            $this->db->commit();

            // Réponse AJAX
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => $importCount . ' commandes ont été importées avec succès'
                ]);
                exit;
            }

            // Réponse normale
            $this->sendNotification('success', $importCount . ' commandes ont été importées avec succès');
            header('Location: index.php?action=commandes');
            exit;
            
        } catch (Exception $e) {
            $this->db->rollBack();

            // Réponse AJAX
            if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'error' => 'Erreur lors de l\'import : ' . $e->getMessage()
                ]);
                exit;
            }

            // Réponse normale
            $this->sendNotification('error', 'Erreur lors de l\'import : ' . $e->getMessage());
        }
    }
    
    require_once('views/commandes/import.php');
}

public function tracking() {
    try {
        // Récupérer toutes les commandes
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date_creation DESC";
        $stmt = $this->db->query($query);
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Compter les commandes par statut
        $statusCounts = [];
        foreach ($commandes as $commande) {
            $status = $commande['statut'];
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }

        // Passage des variables à la vue
        require_once('views/commandes/tracking.php');

    } catch(PDOException $e) {
        error_log("Erreur dans CommandeController::tracking : " . $e->getMessage());
        $_SESSION['error'] = "Une erreur est survenue lors du chargement du suivi des commandes.";
        header('Location: index.php?action=commandes');
        exit();
    }
}

public function confirmed() {
    try {
        // Initialisation des filtres
        $filtres = [
            'nom' => $_GET['nom'] ?? '',
            'telephone' => $_GET['telephone'] ?? '',
            'periode' => $_GET['periode'] ?? '',
            'tri' => $_GET['tri'] ?? 'date_desc'
        ];

        // Construction de la requête de base
        $query = "SELECT * FROM " . $this->table_name . " WHERE statut = 'Confirmé'";
        $params = [];

        // Ajout des conditions de filtrage
        if (!empty($filtres['nom'])) {
            $query .= " AND nom LIKE :nom";
            $params[':nom'] = '%' . $filtres['nom'] . '%';
        }

        if (!empty($filtres['telephone'])) {
            $query .= " AND telephone LIKE :telephone";
            $params[':telephone'] = '%' . $filtres['telephone'] . '%';
        }

        // Gestion de la période
        switch ($filtres['periode']) {
            case 'today':
                $query .= " AND DATE(date_creation) = CURDATE()";
                break;
            case 'week':
                $query .= " AND YEARWEEK(date_creation) = YEARWEEK(CURDATE())";
                break;
            case 'month':
                $query .= " AND MONTH(date_creation) = MONTH(CURDATE()) AND YEAR(date_creation) = YEAR(CURDATE())";
                break;
        }

        // Ajout du tri
        $query .= " ORDER BY date_creation " . ($filtres['tri'] === 'date_asc' ? 'ASC' : 'DESC');

        // Exécution de la requête
        $stmt = $this->db->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Passage à la vue
        require_once('views/commandes/confirmed.php');

    } catch(PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la récupération des commandes: " . $e->getMessage();
        header('Location: index.php?action=commandes');
        exit();
    }
}

/*public function shipping() {
    try {
        // Récupérer toutes les commandes
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date_creation DESC";
        $stmt = $this->db->query($query);
        $commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculer les statistiques
        $stats = [
            'en_cours' => 0,
            'expedie' => 0
        ];

        foreach ($commandes as $commande) {
            if ($commande['statut'] === OrderStatus::EN_COURS) {
                $stats['en_cours']++;
            } elseif ($commande['statut'] === OrderStatus::EXPEDIE) {
                $stats['expedie']++;
            }
        }

        require_once('views/commandes/shipping.php');

    } catch(PDOException $e) {
        $_SESSION['error'] = "Erreur lors de la récupération des commandes: " . $e->getMessage();
        header('Location: index.php?action=commandes');
        exit();
    }
}*/

public function shipping() {
    try {
        // Récupérer toutes les commandes
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

        // Calculer le nombre de commandes expédiées aujourd'hui
        $query_expedie_today = "SELECT COUNT(DISTINCT commande_id) as total 
                              FROM commandes_historique 
                              WHERE statut = :statut_expedie 
                              AND DATE(date) = CURRENT_DATE()";

        $stmt_expedie = $this->db->prepare($query_expedie_today);
        $stmt_expedie->bindValue(':statut_expedie', OrderStatus::EXPEDIE);
        $stmt_expedie->execute();
        $expedie_result = $stmt_expedie->fetch(PDO::FETCH_ASSOC);

        // Calculer le nombre de commandes en cours
        $query_en_cours = "SELECT COUNT(*) as total 
                          FROM " . $this->table_name . " 
                          WHERE statut = :statut_en_cours";

        $stmt_en_cours = $this->db->prepare($query_en_cours);
        $stmt_en_cours->bindValue(':statut_en_cours', OrderStatus::EN_COURS);
        $stmt_en_cours->execute();
        $en_cours_result = $stmt_en_cours->fetch(PDO::FETCH_ASSOC);

        // Statistiques
        $stats = [
            'en_cours' => $en_cours_result['total'],
            'expedie_today' => $expedie_result['total']
        ];

        // Récupérer le détail des commandes expédiées aujourd'hui pour le modal
        $query_detail_today = "SELECT c.*, ch.date as date_expedition, ch.username
                             FROM commandes_historique ch
                             JOIN " . $this->table_name . " c ON ch.commande_id = c.id
                             WHERE ch.statut = :statut_expedie 
                             AND DATE(ch.date) = CURRENT_DATE()
                             ORDER BY ch.date DESC";

        $stmt_detail = $this->db->prepare($query_detail_today);
        $stmt_detail->bindValue(':statut_expedie', OrderStatus::EXPEDIE);
        $stmt_detail->execute();
        $expeditions_today = $stmt_detail->fetchAll(PDO::FETCH_ASSOC);

        require_once('views/commandes/shipping.php');

    } catch(PDOException $e) {
        error_log("Erreur dans shipping: " . $e->getMessage());
        $_SESSION['error'] = "Erreur lors de la récupération des commandes: " . $e->getMessage();
        header('Location: index.php?action=commandes');
        exit();
    }
}


}