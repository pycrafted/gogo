<?php
/**
 * Contrôleur Training - Couche Business Logic
 * Gère toutes les opérations CRUD pour les formations
 */

require_once __DIR__ . '/../models/Training.php';
require_once __DIR__ . '/../utils/helpers.php';

class TrainingController {
    private $training;
    
    public function __construct() {
        $this->training = new Training();
    }
    
    /**
     * Gère toutes les requêtes HTTP
     */
    public function handleRequest() {
        // Configuration CORS
        setupCORS();
        
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));
        
        // Extraire l'ID de la formation si présent
        $trainingId = null;
        if (count($pathParts) > 2 && is_numeric($pathParts[2])) {
            $trainingId = (int)$pathParts[2];
        }
        
        try {
            switch ($method) {
                case 'GET':
                    if ($trainingId) {
                        $this->getTraining($trainingId);
                    } else {
                        $this->getAllTrainings();
                    }
                    break;
                    
                case 'POST':
                    $this->createTraining();
                    break;
                    
                case 'PUT':
                    if ($trainingId) {
                        $this->updateTraining($trainingId);
                    } else {
                        sendJsonResponse(false, 'ID de formation requis pour la mise à jour');
                    }
                    break;
                    
                case 'DELETE':
                    if ($trainingId) {
                        $this->deleteTraining($trainingId);
                    } else {
                        sendJsonResponse(false, 'ID de formation requis pour la suppression');
                    }
                    break;
                    
                default:
                    sendJsonResponse(false, 'Méthode HTTP non supportée');
                    break;
            }
        } catch (Exception $e) {
            sendJsonResponse(false, $e->getMessage());
        }
    }
    
    /**
     * Récupère toutes les formations
     */
    private function getAllTrainings() {
        $trainings = $this->training->getAll();
        
        // Formater les données pour l'affichage
        foreach ($trainings as &$training) {
            $training['date_formatted'] = formatDate($training['date']);
            $training['price_formatted'] = number_format($training['price'], 2, ',', ' ') . ' €';
            $training['duration_formatted'] = $training['duration'] . ' jour(s)';
        }
        
        sendJsonResponse(true, 'Formations récupérées avec succès', $trainings);
    }
    
    /**
     * Récupère une formation par son ID
     */
    private function getTraining($id) {
        $training = $this->training->getById($id);
        
        if (!$training) {
            sendJsonResponse(false, 'Formation non trouvée');
            return;
        }
        
        // Formater les données pour l'affichage
        $training['date_formatted'] = formatDate($training['date']);
        $training['price_formatted'] = number_format($training['price'], 2, ',', ' ') . ' €';
        $training['duration_formatted'] = $training['duration'] . ' jour(s)';
        
        sendJsonResponse(true, 'Formation récupérée avec succès', $training);
    }
    
    /**
     * Crée une nouvelle formation
     */
    private function createTraining() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            sendJsonResponse(false, 'Données JSON invalides');
            return;
        }
        
        // Validation des données
        $errors = $this->validateTrainingData($input);
        if (!empty($errors)) {
            sendJsonResponse(false, 'Données invalides', null, $errors);
            return;
        }
        
        // Création de la formation
        $trainingId = $this->training->create($input);
        
        sendJsonResponse(true, 'Formation créée avec succès', ['id' => $trainingId]);
    }
    
    /**
     * Met à jour une formation
     */
    private function updateTraining($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            sendJsonResponse(false, 'Données JSON invalides');
            return;
        }
        
        // Validation des données
        $errors = $this->validateTrainingData($input);
        if (!empty($errors)) {
            sendJsonResponse(false, 'Données invalides', null, $errors);
            return;
        }
        
        // Vérifier que la formation existe
        $existingTraining = $this->training->getById($id);
        if (!$existingTraining) {
            sendJsonResponse(false, 'Formation non trouvée');
            return;
        }
        
        // Mise à jour de la formation
        $success = $this->training->update($id, $input);
        
        if ($success) {
            sendJsonResponse(true, 'Formation mise à jour avec succès');
        } else {
            sendJsonResponse(false, 'Erreur lors de la mise à jour de la formation');
        }
    }
    
    /**
     * Supprime une formation
     */
    private function deleteTraining($id) {
        // Vérifier que la formation existe
        $existingTraining = $this->training->getById($id);
        if (!$existingTraining) {
            sendJsonResponse(false, 'Formation non trouvée');
            return;
        }
        
        // Suppression de la formation
        $success = $this->training->delete($id);
        
        if ($success) {
            sendJsonResponse(true, 'Formation supprimée avec succès');
        } else {
            sendJsonResponse(false, 'Erreur lors de la suppression de la formation');
        }
    }
    
    /**
     * Valide les données d'une formation
     */
    private function validateTrainingData($data) {
        $errors = [];
        
        // Validation du domaine
        if (empty($data['domain'])) {
            $errors[] = 'Le domaine est requis';
        } elseif (!validateDomain($data['domain'])) {
            $errors[] = 'Le domaine n\'est pas valide';
        }
        
        // Validation du titre
        if (empty($data['title'])) {
            $errors[] = 'Le titre est requis';
        } elseif (strlen($data['title']) > 255) {
            $errors[] = 'Le titre ne peut pas dépasser 255 caractères';
        }
        
        // Validation du lieu
        if (empty($data['location'])) {
            $errors[] = 'Le lieu est requis';
        } elseif (strlen($data['location']) > 100) {
            $errors[] = 'Le lieu ne peut pas dépasser 100 caractères';
        }
        
        // Validation de la date
        if (empty($data['date'])) {
            $errors[] = 'La date est requise';
        } elseif (!strtotime($data['date'])) {
            $errors[] = 'La date n\'est pas valide';
        }
        
        // Validation de la durée
        if (!isset($data['duration']) || !is_numeric($data['duration'])) {
            $errors[] = 'La durée est requise et doit être un nombre';
        } elseif ($data['duration'] < 1 || $data['duration'] > 30) {
            $errors[] = 'La durée doit être comprise entre 1 et 30 jours';
        }
        
        // Validation du prix
        if (!isset($data['price']) || !is_numeric($data['price'])) {
            $errors[] = 'Le prix est requis et doit être un nombre';
        } elseif ($data['price'] < 0) {
            $errors[] = 'Le prix ne peut pas être négatif';
        }
        
        // Validation des animateurs
        if (empty($data['animators'])) {
            $errors[] = 'Les animateurs sont requis';
        }
        
        // Validation du programme
        if (empty($data['program'])) {
            $errors[] = 'Le programme est requis';
        }
        
        return $errors;
    }
}
?> 