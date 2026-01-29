<?php
class Quiz {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($titre, $description) {
        $sql = "INSERT INTO quiz (titre, description) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$titre, $description]);
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM quiz ORDER BY date_creation DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM quiz WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM quiz WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $titre, $description) {
        $stmt = $this->conn->prepare("UPDATE quiz SET titre = ?, description = ? WHERE id = ?");
        return $stmt->execute([$titre, $description, $id]);
    }

    public function countAll() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM quiz");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
}
?>
