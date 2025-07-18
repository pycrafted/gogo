<?php
/**
 * Modèle Training - Couche Data Access
 * Gère toutes les opérations CRUD pour les formations
 */

require_once __DIR__ . '/../config.php';

class Training {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDatabaseConnection();
    }
    
    /**
     * Récupère toutes les formations
     * @return array Tableau des formations
     */
    public function getAll() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, domain, title, location, date, duration, price, animators, program, created_at, updated_at 
                FROM trainings 
                ORDER BY date ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des formations: " . $e->getMessage());
            throw new Exception("Impossible de récupérer les formations");
        }
    }
    
    /**
     * Récupère une formation par son ID
     * @param int $id ID de la formation
     * @return array|null Données de la formation ou null si non trouvée
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, domain, title, location, date, duration, price, animators, program, created_at, updated_at 
                FROM trainings 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la formation ID $id: " . $e->getMessage());
            throw new Exception("Impossible de récupérer la formation");
        }
    }
    
    /**
     * Crée une nouvelle formation
     * @param array $data Données de la formation
     * @return int ID de la formation créée
     */
    public function create($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO trainings (domain, title, location, date, duration, price, animators, program) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['domain'],
                $data['title'],
                $data['location'],
                $data['date'],
                $data['duration'],
                $data['price'],
                $data['animators'],
                $data['program']
            ]);
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Erreur lors de la création de la formation: " . $e->getMessage());
            throw new Exception("Impossible de créer la formation");
        }
    }
    
    /**
     * Met à jour une formation existante
     * @param int $id ID de la formation
     * @param array $data Données de la formation
     * @return bool True si mise à jour réussie
     */
    public function update($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE trainings 
                SET domain = ?, title = ?, location = ?, date = ?, duration = ?, price = ?, animators = ?, program = ? 
                WHERE id = ?
            ");
            $result = $stmt->execute([
                $data['domain'],
                $data['title'],
                $data['location'],
                $data['date'],
                $data['duration'],
                $data['price'],
                $data['animators'],
                $data['program'],
                $id
            ]);
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de la formation ID $id: " . $e->getMessage());
            throw new Exception("Impossible de mettre à jour la formation");
        }
    }
    
    /**
     * Supprime une formation
     * @param int $id ID de la formation à supprimer
     * @return bool True si suppression réussie
     */
    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM trainings WHERE id = ?");
            $result = $stmt->execute([$id]);
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression de la formation ID $id: " . $e->getMessage());
            throw new Exception("Impossible de supprimer la formation");
        }
    }
    
    /**
     * Recherche des formations par domaine
     * @param string $domain Domaine à rechercher
     * @return array Tableau des formations trouvées
     */
    public function searchByDomain($domain) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, domain, title, location, date, duration, price, animators, program, created_at, updated_at 
                FROM trainings 
                WHERE domain LIKE ? 
                ORDER BY date ASC
            ");
            $stmt->execute(['%' . $domain . '%']);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche par domaine: " . $e->getMessage());
            throw new Exception("Impossible de rechercher les formations");
        }
    }
    
    /**
     * Recherche des formations par titre
     * @param string $title Titre à rechercher
     * @return array Tableau des formations trouvées
     */
    public function searchByTitle($title) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, domain, title, location, date, duration, price, animators, program, created_at, updated_at 
                FROM trainings 
                WHERE title LIKE ? 
                ORDER BY date ASC
            ");
            $stmt->execute(['%' . $title . '%']);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la recherche par titre: " . $e->getMessage());
            throw new Exception("Impossible de rechercher les formations");
        }
    }
    
    /**
     * Récupère les formations par date
     * @param string $date Date au format YYYY-MM-DD
     * @return array Tableau des formations trouvées
     */
    public function getByDate($date) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, domain, title, location, date, duration, price, animators, program, created_at, updated_at 
                FROM trainings 
                WHERE date = ? 
                ORDER BY title ASC
            ");
            $stmt->execute([$date]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération par date: " . $e->getMessage());
            throw new Exception("Impossible de récupérer les formations par date");
        }
    }
}
?> 