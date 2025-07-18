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
                SELECT p.*, t.title as training_title, t.domain as training_domain, t.date as training_date
                FROM participants p
                LEFT JOIN trainings t ON p.training_id = t.id
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
                SELECT p.*, t.title as training_title, t.domain as training_domain, t.date as training_date
                FROM participants p
                LEFT JOIN trainings t ON p.training_id = t.id
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
     * Récupère les participants par formation
     * @param int $trainingId ID de la formation
     * @return array Tableau des participants
     */
    public function getByTrainingId($trainingId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT p.*, t.title as training_title, t.domain as training_domain, t.date as training_date
                FROM participants p
                LEFT JOIN trainings t ON p.training_id = t.id
                WHERE p.training_id = ?
                ORDER BY p.registration_date ASC
            ");
            $stmt->execute([$trainingId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des participants pour la formation $trainingId: " . $e->getMessage());
            throw new Exception("Impossible de récupérer les participants de cette formation");
        }
    }
    
    /**
     * Crée un nouveau participant
     * @param array $data Données du participant
     * @return int ID du participant créé
     */
    public function create($data) {
        try {
            error_log("Participant::create - Données reçues: " . print_r($data, true));
            
            // Vérifier la structure de la table
            $stmt = $this->pdo->prepare("PRAGMA table_info(participants)");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Structure de la table participants: " . print_r($columns, true));
            
            // Vérifier si la colonne position existe
            $hasPosition = false;
            foreach ($columns as $column) {
                if ($column['name'] === 'position') {
                    $hasPosition = true;
                    break;
                }
            }
            
            if ($hasPosition) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, notes, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $params = [
                    $data['training_id'],
                    $data['first_name'],
                    $data['last_name'],
                    $data['email'],
                    $data['phone'] ?? '',
                    $data['company'] ?? '',
                    $data['position'] ?? '',
                    $data['notes'] ?? '',
                    'pending'
                ];
            } else {
                $stmt = $this->pdo->prepare("
                    INSERT INTO participants (training_id, first_name, last_name, email, phone, company, notes, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $params = [
                    $data['training_id'],
                    $data['first_name'],
                    $data['last_name'],
                    $data['email'],
                    $data['phone'] ?? '',
                    $data['company'] ?? '',
                    $data['notes'] ?? '',
                    'pending'
                ];
            }
            
            error_log("Paramètres d'insertion: " . print_r($params, true));
            
            $stmt->execute($params);
            $newId = $this->pdo->lastInsertId();
            error_log("Participant créé avec l'ID: " . $newId);
            return $newId;
        } catch (PDOException $e) {
            error_log("Erreur lors de la création du participant: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Impossible de créer le participant");
        }
    }
    
    /**
     * Met à jour un participant existant
     * @param int $id ID du participant
     * @param array $data Données du participant
     * @return bool True si mise à jour réussie
     */
    public function update($id, $data) {
        try {
            // Vérifier la structure de la table
            $stmt = $this->pdo->prepare("PRAGMA table_info(participants)");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Vérifier si la colonne position existe
            $hasPosition = false;
            foreach ($columns as $column) {
                if ($column['name'] === 'position') {
                    $hasPosition = true;
                    break;
                }
            }
            
            if ($hasPosition) {
                $stmt = $this->pdo->prepare("
                    UPDATE participants 
                    SET training_id = ?, first_name = ?, last_name = ?, email = ?, phone = ?, company = ?, position = ?, status = ?, notes = ? 
                    WHERE id = ?
                ");
                $result = $stmt->execute([
                    $data['training_id'],
                    $data['first_name'],
                    $data['last_name'],
                    $data['email'],
                    $data['phone'] ?? '',
                    $data['company'] ?? '',
                    $data['position'] ?? '',
                    $data['status'] ?? 'pending',
                    $data['notes'] ?? '',
                    $id
                ]);
            } else {
                $stmt = $this->pdo->prepare("
                    UPDATE participants 
                    SET training_id = ?, first_name = ?, last_name = ?, email = ?, phone = ?, company = ?, status = ?, notes = ? 
                    WHERE id = ?
                ");
                $result = $stmt->execute([
                    $data['training_id'],
                    $data['first_name'],
                    $data['last_name'],
                    $data['email'],
                    $data['phone'] ?? '',
                    $data['company'] ?? '',
                    $data['status'] ?? 'pending',
                    $data['notes'] ?? '',
                    $id
                ]);
            }
            
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
     * Vérifie si un email existe déjà
     * @param string $email Email à vérifier
     * @param int $excludeId ID du participant à exclure (pour les mises à jour)
     * @return bool True si l'email existe
     */
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT COUNT(*) FROM participants WHERE email = ?";
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
            return false;
        }
    }
    
    /**
     * Récupère les statistiques des participants
     * @return array Statistiques
     */
    public function getStats() {
        try {
            $stats = [];
            
            // Total des participants
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM participants");
            $stmt->execute();
            $stats['total'] = $stmt->fetchColumn();
            
            // Participants par statut
            $stmt = $this->pdo->prepare("SELECT status, COUNT(*) as count FROM participants GROUP BY status");
            $stmt->execute();
            $stats['by_status'] = $stmt->fetchAll();
            
            // Participants par formation
            $stmt = $this->pdo->prepare("
                SELECT t.title, COUNT(p.id) as count
                FROM trainings t
                LEFT JOIN participants p ON t.id = p.training_id
                GROUP BY t.id, t.title
                ORDER BY count DESC
            ");
            $stmt->execute();
            $stats['by_training'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
            throw new Exception("Impossible de récupérer les statistiques");
        }
    }
}
?> 