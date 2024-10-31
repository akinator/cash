<?php
class Commande {
    private $conn;
    private $table_name = "commandes";

    public $id;
    public $reference;
    public $client_id;
    public $montant;
    public $statut;
    public $date_creation;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                SET reference=:reference, client_id=:client_id, 
                    montant=:montant, statut=:statut, date_creation=:date_creation";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":reference", $this->reference);
        $stmt->bindParam(":client_id", $this->client_id);
        $stmt->bindParam(":montant", $this->montant);
        $stmt->bindParam(":statut", $this->statut);
        $stmt->bindParam(":date_creation", $this->date_creation);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                SET statut = :statut
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":statut", $this->statut);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
