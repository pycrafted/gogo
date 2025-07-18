<?php
/**
 * Modèle User - Couche Data Access
 * Gère l'authentification des administrateurs
 */

require_once __DIR__ . '/../config.php';

class User {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDatabaseConnection();
    }
    
    /**
     * Authentifie un utilisateur
     * @param string $username Nom d'utilisateur
     * @param string $password Mot de passe
     * @return array|null Données de l'utilisateur ou null si échec
     */
    public function authenticate($username, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, username, email, role, password 
                FROM users 
                WHERE username = ?
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Ne pas retourner le mot de passe
                unset($user['password']);
                return $user;
            }
            
            return null;
        } catch (PDOException $e) {
            error_log("Erreur lors de l'authentification: " . $e->getMessage());
            throw new Exception("Impossible d'authentifier l'utilisateur");
        }
    }
    
    /**
     * Récupère un utilisateur par son ID
     * @param int $id ID de l'utilisateur
     * @return array|null Données de l'utilisateur ou null si non trouvé
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, username, email, role, created_at 
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'utilisateur ID $id: " . $e->getMessage());
            throw new Exception("Impossible de récupérer l'utilisateur");
        }
    }
    
    /**
     * Crée un nouvel utilisateur
     * @param array $data Données de l'utilisateur
     * @return int ID de l'utilisateur créé
     */
    public function create($data) {
        try {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, password, email, role) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['username'],
                $hashedPassword,
                $data['email'],
                $data['role'] ?? 'admin'
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
            throw new Exception("Impossible de créer l'utilisateur");
        }
    }
    
    /**
     * Met à jour un utilisateur
     * @param int $id ID de l'utilisateur
     * @param array $data Données de l'utilisateur
     * @return bool True si mise à jour réussie
     */
    public function update($id, $data) {
        try {
            $updates = [];
            $params = [];
            
            if (isset($data['username'])) {
                $updates[] = "username = ?";
                $params[] = $data['username'];
            }
            
            if (isset($data['email'])) {
                $updates[] = "email = ?";
                $params[] = $data['email'];
            }
            
            if (isset($data['role'])) {
                $updates[] = "role = ?";
                $params[] = $data['role'];
            }
            
            if (isset($data['password'])) {
                $updates[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updates)) {
                return false;
            }
            
            $params[] = $id;
            $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'utilisateur ID $id: " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour l'utilisateur");
        }
    }
    
    /**
     * Supprime un utilisateur
     * @param int $id ID de l'utilisateur à supprimer
     * @return bool True si suppression réussie
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$id]);
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de l'utilisateur ID $id: " . $e->getMessage());
            throw new Exception("Impossible de supprimer l'utilisateur");
        }
    }
    
    /**
     * Récupère tous les utilisateurs
     * @return array Tableau des utilisateurs
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, username, email, role, created_at 
                FROM users 
                ORDER BY created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des utilisateurs: " . $e->getMessage());
            throw new Exception("Impossible de récupérer les utilisateurs");
        }
    }
    
    /**
     * Vérifie si un nom d'utilisateur existe déjà
     * @param string $username Nom d'utilisateur
     * @param int $excludeId ID à exclure (pour les mises à jour)
     * @return bool True si le nom d'utilisateur existe
     */
    public function usernameExists($username, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
            $params = [$username];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification du nom d'utilisateur: " . $e->getMessage());
            throw new Exception("Impossible de vérifier le nom d'utilisateur");
        }
    }
    
    /**
     * Vérifie si un email existe déjà
     * @param string $email Email
     * @param int $excludeId ID à exclure (pour les mises à jour)
     * @return bool True si l'email existe
     */
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
            $params = [$email];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de l'email: " . $e->getMessage());
            throw new Exception("Impossible de vérifier l'email");
        }
    }
}
?> 