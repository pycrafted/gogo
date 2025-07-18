<?php
/**
 * Modèle Participant - Couche Data Access
 * Gère toutes les opérations CRUD pour les participants
 */

require_once __DIR__ . '/../config.php';

class Participant {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDatabaseConnection();
    }
    
    /**
     * Récupère tous les participants
     * @return array Tableau des participants
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, t.title as training_title, t.domain as training_domain
                FROM participants p
                JOIN trainings t ON p.training_id = t.id
                ORDER BY p.registration_date DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des participants: " . $e->getMessage());
            throw new Exception("Impossible de récupérer les participants");
        }
    }
    
    /**
     * Récupère un participant par son ID
     * @param int $id ID du participant
     * @return array|null Données du participant ou null si non trouvé
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, t.title as training_title, t.domain as training_domain
                FROM participants p
                JOIN trainings t ON p.training_id = t.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du participant ID $id: " . $e->getMessage());
            throw new Exception("Impossible de récupérer le participant");
        }
    }
    
    /**
     * Crée une nouvelle inscription
     * @param array $data Données du participant
     * @return int ID du participant créé
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO participants (first_name, last_name, email, phone, company, training_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['company'] ?? null,
                $data['training_id']
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du participant: " . $e->getMessage());
            throw new Exception("Impossible de créer l'inscription");
        }
    }
    
    /**
     * Met à jour un participant
     * @param int $id ID du participant
     * @param array $data Données du participant
     * @return bool True si mise à jour réussie
     */
    public function update($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE participants 
                SET first_name = ?, last_name = ?, email = ?, phone = ?, company = ?, training_id = ?, status = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['company'] ?? null,
                $data['training_id'],
                $data['status'] ?? 'registered',
                $id
            ]);
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du participant ID $id: " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour le participant");
        }
    }
    
    /**
     * Supprime un participant
     * @param int $id ID du participant à supprimer
     * @return bool True si suppression réussie
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM participants WHERE id = ?");
            $result = $stmt->execute([$id]);
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du participant ID $id: " . $e->getMessage());
            throw new Exception("Impossible de supprimer le participant");
        }
    }
    
    /**
     * Récupère les participants par formation
     * @param int $trainingId ID de la formation
     * @return array Tableau des participants
     */
    public function getByTraining($trainingId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, t.title as training_title, t.domain as training_domain
                FROM participants p
                JOIN trainings t ON p.training_id = t.id
                WHERE p.training_id = ?
                ORDER BY p.registration_date DESC
            ");
            $stmt->execute([$trainingId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des participants par formation: " . $e->getMessage());
            throw new Exception("Impossible de récupérer les participants");
        }
    }
    
    /**
     * Récupère les participants par email
     * @param string $email Email du participant
     * @return array Tableau des participants
     */
    public function getByEmail($email) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, t.title as training_title, t.domain as training_domain
                FROM participants p
                JOIN trainings t ON p.training_id = t.id
                WHERE p.email = ?
                ORDER BY p.registration_date DESC
            ");
            $stmt->execute([$email]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des participants par email: " . $e->getMessage());
            throw new Exception("Impossible de récupérer les participants");
        }
    }
    
    /**
     * Vérifie si un email est déjà inscrit à une formation
     * @param string $email Email du participant
     * @param int $trainingId ID de la formation
     * @return bool True si déjà inscrit
     */
    public function isAlreadyRegistered($email, $trainingId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM participants 
                WHERE email = ? AND training_id = ?
            ");
            $stmt->execute([$email, $trainingId]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification d'inscription: " . $e->getMessage());
            throw new Exception("Impossible de vérifier l'inscription");
        }
    }
    
    /**
     * Compte le nombre de participants par formation
     * @param int $trainingId ID de la formation
     * @return int Nombre de participants
     */
    public function countByTraining($trainingId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) FROM participants 
                WHERE training_id = ?
            ");
            $stmt->execute([$trainingId]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur lors du comptage des participants: " . $e->getMessage());
            throw new Exception("Impossible de compter les participants");
        }
    }
}
?> 