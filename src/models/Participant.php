<?php
/**
 * Modèle Participant - Couche Data Access
 * Gère toutes les opérations CRUD pour les participants
 */

require_once __DIR__ . '/../config.php';

class Participant {
    private $pdo;
    
    public function __construct() {
        error_log("=== PARTICIPANT MODEL - CONSTRUCTEUR ===");
        
        // Vérifier quel fichier de base de données est utilisé
        $configPath = __DIR__ . '/../config.php';
        error_log("Chemin du fichier config: " . $configPath);
        
        if (file_exists($configPath)) {
            error_log("Fichier config trouvé");
        } else {
            error_log("ERREUR: Fichier config non trouvé");
        }
        
        $this->pdo = getDatabaseConnection();
        
        // Vérifier la structure de la table
        try {
            $stmt = $this->pdo->prepare("PRAGMA table_info(participants)");
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Structure de la table participants dans le modèle:");
            foreach ($columns as $column) {
                error_log("  - {$column['name']} ({$column['type']})");
            }
        } catch (Exception $e) {
            error_log("ERREUR lors de la vérification de la structure: " . $e->getMessage());
        }
        
        error_log("=== PARTICIPANT MODEL - CONSTRUCTEUR FIN ===");
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
            error_log("=== PARTICIPANT MODEL - DEBUT DE CREATION ===");
            error_log("Données reçues: " . print_r($data, true));
            
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
            
            error_log("Colonne position existe: " . ($hasPosition ? 'Oui' : 'Non'));
            
            if ($hasPosition) {
                $sql = "INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
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
                $sql = "INSERT INTO participants (training_id, first_name, last_name, email, phone, company, notes, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
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
            
            error_log("SQL à exécuter: " . $sql);
            error_log("Paramètres: " . print_r($params, true));
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            error_log("Résultat de l'exécution: " . ($result ? 'Succès' : 'Échec'));
            
            if ($result) {
                $newId = $this->pdo->lastInsertId();
                error_log("Nouvel ID généré: " . $newId);
                error_log("=== PARTICIPANT MODEL - FIN DE CREATION ===");
                return $newId;
            } else {
                error_log("Échec de l'insertion - Erreur PDO: " . print_r($stmt->errorInfo(), true));
                throw new Exception("Impossible de créer l'inscription");
            }
            
        } catch (PDOException $e) {
            error_log("ERREUR PDO lors de la création du participant: " . $e->getMessage());
            error_log("Code d'erreur: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Impossible de créer l'inscription: " . $e->getMessage());
        } catch (Exception $e) {
            error_log("ERREUR GENERALE lors de la création du participant: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
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
            error_log("=== PARTICIPANT MODEL - DEBUT DE MISE À JOUR ===");
            error_log("ID du participant à mettre à jour: $id");
            error_log("Données reçues: " . print_r($data, true));
            
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
            error_log("Colonne position existe: " . ($hasPosition ? 'Oui' : 'Non'));
            
            // Log du statut reçu
            error_log("Statut reçu dans les données: " . ($data['status'] ?? 'Non défini'));
            
            if ($hasPosition) {
                $sql = "UPDATE participants SET training_id = ?, first_name = ?, last_name = ?, email = ?, phone = ?, company = ?, position = ?, status = ?, notes = ? WHERE id = ?";
                $params = [
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
                ];
            } else {
                $sql = "UPDATE participants SET training_id = ?, first_name = ?, last_name = ?, email = ?, phone = ?, company = ?, status = ?, notes = ? WHERE id = ?";
                $params = [
                    $data['training_id'],
                    $data['first_name'],
                    $data['last_name'],
                    $data['email'],
                    $data['phone'] ?? '',
                    $data['company'] ?? '',
                    $data['status'] ?? 'pending',
                    $data['notes'] ?? '',
                    $id
                ];
            }
            
            error_log("SQL à exécuter: " . $sql);
            error_log("Paramètres: " . print_r($params, true));
            
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute($params);
            
            error_log("Résultat de l'exécution: " . ($result ? 'Succès' : 'Échec'));
            error_log("Nombre de lignes affectées: " . $stmt->rowCount());
            
            if ($result && $stmt->rowCount() > 0) {
                error_log("✅ Mise à jour réussie");
                
                // Vérifier que la mise à jour a bien eu lieu
                $verificationStmt = $this->pdo->prepare("SELECT status FROM participants WHERE id = ?");
                $verificationStmt->execute([$id]);
                $newStatus = $verificationStmt->fetchColumn();
                error_log("Statut après mise à jour: " . $newStatus);
                
                error_log("=== PARTICIPANT MODEL - FIN DE MISE À JOUR ===");
                return true;
            } else {
                error_log("❌ Aucune ligne mise à jour");
                error_log("=== PARTICIPANT MODEL - FIN DE MISE À JOUR ===");
                return false;
            }
        } catch (PDOException $e) {
            error_log("ERREUR PDO lors de la mise à jour du participant ID $id: " . $e->getMessage());
            error_log("Code d'erreur: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            error_log("=== PARTICIPANT MODEL - FIN DE MISE À JOUR (ERREUR) ===");
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
            error_log("=== PARTICIPANT MODEL - DEBUT DE VÉRIFICATION D'EMAIL ===");
            error_log("Email à vérifier: " . $email);
            error_log("ID à exclure (pour la mise à jour): " . ($excludeId ?? 'Aucun'));
            
            $sql = "SELECT COUNT(*) FROM participants WHERE email = ?";
            $params = [$email];
            
            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            error_log("SQL pour la vérification: " . $sql);
            error_log("Paramètres: " . print_r($params, true));
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $count = $stmt->fetchColumn();
            error_log("Nombre de résultats trouvés: " . $count);
            error_log("=== PARTICIPANT MODEL - FIN DE VÉRIFICATION D'EMAIL ===");
            return $count > 0;
        } catch (PDOException $e) {
            error_log("ERREUR PDO lors de la vérification de l'email: " . $e->getMessage());
            error_log("Code d'erreur: " . $e->getCode());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        } catch (Exception $e) {
            error_log("ERREUR GENERALE lors de la vérification de l'email: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
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