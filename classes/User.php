<?php 
require_once 'config/Database.php';

class User {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($nom, $email, $password, $role) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO users (nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nom, $email, $hashedPassword, $role]);
    }

    public function login($email, $mot_de_passe) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {
            return $user;
        }
        return false;
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($id, $newPassword) {
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("UPDATE users SET mot_de_passe = ? WHERE id = ?");
        return $stmt->execute([$newHash, $id]);
    }

        // Count total users
    public function countAll() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }

    // Get paginated users
    public function getPaginated($limit, $offset) {
        $stmt = $this->conn->prepare("SELECT * FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function verifyPassword($userId, $password) {
        $stmt = $this->conn->prepare("SELECT mot_de_passe FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $hash = $stmt->fetchColumn();
        return password_verify($password, $hash);
    }
    
    public function update($id, $nom, $email, $role) {
        $stmt = $this->conn->prepare("UPDATE users SET nom = ?, email = ?, role = ? WHERE id = ?");
        return $stmt->execute([$nom, $email, $role, $id]);
    }
    
    public function delete($id) {
        if ($id == Auth::getUserId()) {
            return false; // prevent self-deletion
        }
    
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT id, nom FROM users WHERE role = 'participant'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}

?>