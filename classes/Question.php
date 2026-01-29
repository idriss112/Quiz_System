<?php
class Question {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function add($quiz_id, $question_text, $bonne_reponse, $mauvaises_reponses, $image_url = null) {
        $sql = "INSERT INTO questions (quiz_id, question_text, bonne_reponse, mauvaises_reponses, image_url)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$quiz_id, $question_text, $bonne_reponse, $mauvaises_reponses, $image_url]);
    }

    public function getByQuizId($quiz_id) {
        $stmt = $this->conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM questions WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function update($id, $question_text, $bonne_reponse, $mauvaises_reponses, $image_url = null) {
        $sql = "UPDATE questions SET question_text = ?, bonne_reponse = ?, mauvaises_reponses = ?, image_url = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$question_text, $bonne_reponse, $mauvaises_reponses, $image_url, $id]);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function countAll() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM questions");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
}
?>
