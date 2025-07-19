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
            error_log("=== USER MODEL - DEBUT D'AUTHENTIFICATION ===");
            error_log("Tentative d'authentification pour: $username");
            
            $stmt = $this->pdo->prepare("
                SELECT id, username, email, role, password 
                FROM users 
                WHERE username = ?
            ");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            error_log("Utilisateur trouvé: " . ($user ? 'Oui' : 'Non'));
            
            if ($user && password_verify($password, $user['password'])) {
                error_log("✅ Authentification réussie pour: " . $user['username']);
                error_log("Rôle: " . $user['role']);
                
                // Ne pas retourner le mot de passe
                unset($user['password']);
                
                error_log("=== USER MODEL - FIN D'AUTHENTIFICATION ===");
                return $user;
            } else {
                error_log("❌ Échec d'authentification pour: $username");
                if ($user) {
                    error_log("Mot de passe incorrect");
                } else {
                    error_log("Utilisateur non trouvé");
                }
                error_log("=== USER MODEL - FIN D'AUTHENTIFICATION ===");
                return null;
            }
        } catch (PDOException $e) {
            error_log("🚨 Erreur lors de l'authentification: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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
            error_log("=== USER MODEL - RÉCUPÉRATION PAR ID ===");
            error_log("ID demandé: $id");
            
            $stmt = $this->pdo->prepare("
                SELECT id, username, email, role, created_at 
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result) {
                error_log("✅ Utilisateur trouvé: " . $result['username']);
            } else {
                error_log("❌ Utilisateur non trouvé pour l'ID: $id");
            }
            
            error_log("=== USER MODEL - FIN RÉCUPÉRATION PAR ID ===");
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("🚨 Erreur lors de la récupération de l'utilisateur ID $id: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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
            error_log("=== USER MODEL - DEBUT DE CRÉATION ===");
            error_log("Données reçues: " . json_encode($data));
            
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            error_log("Mot de passe hashé: " . substr($hashedPassword, 0, 20) . "...");
            
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
            
            $newId = $this->pdo->lastInsertId();
            error_log("✅ Utilisateur créé avec l'ID: $newId");
            error_log("=== USER MODEL - FIN DE CRÉATION ===");
            
            return $newId;
        } catch (PDOException $e) {
            error_log("🚨 Erreur lors de la création de l'utilisateur: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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
            error_log("=== USER MODEL - DEBUT DE MISE À JOUR ===");
            error_log("ID utilisateur: $id");
            error_log("Données reçues: " . json_encode($data));
            
            $updateFields = [];
            $params = [];
            
            if (isset($data['username'])) {
                $updateFields[] = "username = ?";
                $params[] = $data['username'];
            }
            
            if (isset($data['email'])) {
                $updateFields[] = "email = ?";
                $params[] = $data['email'];
            }
            
            if (isset($data['role'])) {
                $updateFields[] = "role = ?";
                $params[] = $data['role'];
            }
            
            if (isset($data['password']) && !empty($data['password'])) {
                $updateFields[] = "password = ?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            if (empty($updateFields)) {
                error_log("❌ Aucun champ à mettre à jour");
                return false;
            }
            
            $params[] = $id;
            $sql = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = ?";
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            if ($result) {
                error_log("✅ Utilisateur mis à jour avec succès");
            } else {
                error_log("❌ Échec de la mise à jour");
            }
            
            error_log("=== USER MODEL - FIN DE MISE À JOUR ===");
            return $result;
        } catch (PDOException $e) {
            error_log("🚨 Erreur lors de la mise à jour de l'utilisateur: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Impossible de mettre à jour l'utilisateur");
        }
    }
    
    /**
     * Supprime un utilisateur
     * @param int $id ID de l'utilisateur
     * @return bool True si suppression réussie
     */
    public function delete($id) {
        try {
            error_log("=== USER MODEL - DEBUT DE SUPPRESSION ===");
            error_log("ID utilisateur à supprimer: $id");
            
            $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                error_log("✅ Utilisateur supprimé avec succès");
            } else {
                error_log("❌ Échec de la suppression");
            }
            
            error_log("=== USER MODEL - FIN DE SUPPRESSION ===");
            return $result;
        } catch (PDOException $e) {
            error_log("🚨 Erreur lors de la suppression de l'utilisateur: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Impossible de supprimer l'utilisateur");
        }
    }
    
    /**
     * Récupère tous les utilisateurs
     * @return array Liste des utilisateurs
     */
    public function getAll() {
        try {
            error_log("=== USER MODEL - RÉCUPÉRATION DE TOUS LES UTILISATEURS ===");
            
            $stmt = $this->pdo->prepare("
                SELECT id, username, email, role, created_at 
                FROM users 
                ORDER BY username
            ");
            $stmt->execute();
            $users = $stmt->fetchAll();
            
            error_log("✅ " . count($users) . " utilisateurs récupérés");
            error_log("=== USER MODEL - FIN RÉCUPÉRATION ===");
            
            return $users;
        } catch (PDOException $e) {
            error_log("🚨 Erreur lors de la récupération des utilisateurs: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Impossible de récupérer les utilisateurs");
        }
    }
    
    /**
     * Vérifie si un nom d'utilisateur existe déjà
     * @param string $username Nom d'utilisateur
     * @param int $excludeId ID à exclure (pour la mise à jour)
     * @return bool True si le nom d'utilisateur existe
     */
    public function usernameExists($username, $excludeId = null) {
        try {
            error_log("=== USER MODEL - VÉRIFICATION NOM D'UTILISATEUR ===");
            error_log("Username à vérifier: $username");
            error_log("ID à exclure: " . ($excludeId ?? 'Aucun'));
            
            $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
            $params = [$username];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
            
            $exists = $count > 0;
            error_log("Username existe: " . ($exists ? 'Oui' : 'Non'));
            error_log("=== USER MODEL - FIN VÉRIFICATION ===");
            
            return $exists;
        } catch (PDOException $e) {
            error_log("🚨 Erreur lors de la vérification du nom d'utilisateur: " . $e->getMessage());
            throw new Exception("Impossible de vérifier le nom d'utilisateur");
        }
    }
    
    /**
     * Vérifie si un email existe déjà
     * @param string $email Email
     * @param int $excludeId ID à exclure (pour la mise à jour)
     * @return bool True si l'email existe
     */
    public function emailExists($email, $excludeId = null) {
        try {
            error_log("=== USER MODEL - VÉRIFICATION EMAIL ===");
            error_log("Email à vérifier: $email");
            error_log("ID à exclure: " . ($excludeId ?? 'Aucun'));
            
            $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
            $params = [$email];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
            
            $exists = $count > 0;
            error_log("Email existe: " . ($exists ? 'Oui' : 'Non'));
            error_log("=== USER MODEL - FIN VÉRIFICATION EMAIL ===");
            
            return $exists;
        } catch (PDOException $e) {
            error_log("🚨 Erreur lors de la vérification de l'email: " . $e->getMessage());
            throw new Exception("Impossible de vérifier l'email");
        }
    }
}
?> 