<?php
/**
 * Contrôleur pour la gestion des formations
 * Couche Contrôleur - Logique métier
 */

require_once __DIR__ . '/../models/Training.php';
require_once __DIR__ . '/../utils/helpers.php';

class TrainingController {
    private $trainingModel;
    
    public function __construct() {
        $this->trainingModel = new Training();
    }
    
    /**
     * Gère les requêtes HTTP
     */
    public function handleRequest() {
        try {
            $method = $_SERVER['REQUEST_METHOD'];
            
            // VÉRIFICATION D'AUTHENTIFICATION SEULEMENT POUR LES OPÉRATIONS D'ADMINISTRATION
            if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
                session_start();
                if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                    error_log("❌ Accès non autorisé - Utilisateur non authentifié pour opération $method");
                    header('HTTP/1.1 401 Unauthorized');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Authentification requise pour cette opération',
                        'data' => null
                    ], JSON_UNESCAPED_UNICODE);
                    exit();
                }
                
                // Vérifier l'expiration de la session (30 minutes)
                $sessionTimeout = 30 * 60; // 30 minutes
                if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $sessionTimeout) {
                    error_log("❌ Session expirée - Déconnexion automatique");
                    session_destroy();
                    header('HTTP/1.1 401 Unauthorized');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Session expirée - Veuillez vous reconnecter',
                        'data' => null
                    ], JSON_UNESCAPED_UNICODE);
                    exit();
                }
                
                // Mettre à jour le temps de connexion
                $_SESSION['login_time'] = time();
                error_log("✅ Utilisateur authentifié: " . ($_SESSION['username'] ?? 'inconnu'));
            }
            
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // Extraction de l'ID depuis l'URL
            $id = null;
            if (preg_match('/\/api\/trainings\.php\/(\d+)$/', $path, $matches)) {
                $id = (int) $matches[1];
            }
            
            // Gestion des requêtes OPTIONS pour CORS
            if ($method === 'OPTIONS') {
                header('Access-Control-Allow-Origin: *');
                header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
                header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
                header('HTTP/1.1 200 OK');
                exit;
            }
            
            // Headers CORS
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
            header('Content-Type: application/json; charset=utf-8');
            
            switch ($method) {
                case 'GET':
                    if ($id) {
                        $this->getTraining($id);
                    } else {
                        $this->getAllTrainings();
                    }
                    break;
                    
                case 'POST':
                    $this->createTraining();
                    break;
                    
                case 'PUT':
                    if ($id) {
                        $this->updateTraining($id);
                    } else {
                        echo jsonError('ID requis pour la mise à jour', 400);
                    }
                    break;
                    
                case 'DELETE':
                    if ($id) {
                        $this->deleteTraining($id);
                    } else {
                        echo jsonError('ID requis pour la suppression', 400);
                    }
                    break;
                    
                default:
                    echo jsonError('Méthode non autorisée', 405);
                    break;
            }
            
        } catch (Exception $e) {
            echo jsonError('Erreur serveur: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Récupère toutes les formations
     */
    private function getAllTrainings() {
        try {
            $trainings = $this->trainingModel->getAll();
            
            // Formatage des dates pour l'affichage
            foreach ($trainings as &$training) {
                $training['date_formatted'] = formatDate($training['date']);
                $training['duration_formatted'] = $training['duration'] ? $training['duration'] . ' jour(s)' : '';
                $training['price_formatted'] = $training['price'] ? formatPrice($training['price']) : '';
            }
            
            echo jsonSuccess($trainings, 'Formations récupérées avec succès');
            
        } catch (Exception $e) {
            echo jsonError('Erreur lors de la récupération des formations: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Récupère une formation par ID
     * @param int $id ID de la formation
     */
    private function getTraining($id) {
        try {
            $training = $this->trainingModel->getById($id);
            
            if (!$training) {
                echo jsonError('Formation non trouvée', 404);
                return;
            }
            
            // Formatage de la date pour l'affichage
            $training['date_formatted'] = formatDate($training['date']);
            $training['duration_formatted'] = $training['duration'] ? $training['duration'] . ' jour(s)' : '';
            $training['price_formatted'] = $training['price'] ? formatPrice($training['price']) : '';
            
            echo jsonSuccess($training, 'Formation récupérée avec succès');
            
        } catch (Exception $e) {
            echo jsonError('Erreur lors de la récupération de la formation: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Crée une nouvelle formation
     */
    private function createTraining() {
        try {
            // Récupération des données POST
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                $input = $_POST;
            }
            
            // Nettoyage des données
            $data = sanitizeTrainingData($input);
            
            // Validation des données
            $validation = validateTrainingData($data);
            if (!$validation['valid']) {
                echo jsonError('Données invalides: ' . implode(', ', $validation['errors']), 400);
                return;
            }
            
            // Création de la formation avec tous les champs
            $newId = $this->trainingModel->create($data);
            
            // Récupération de la formation créée
            $training = $this->trainingModel->getById($newId);
            $training['date_formatted'] = formatDate($training['date']);
            $training['duration_formatted'] = $training['duration'] ? $training['duration'] . ' jour(s)' : '';
            $training['price_formatted'] = $training['price'] ? formatPrice($training['price']) : '';
            
            echo jsonSuccess($training, 'Formation créée avec succès');
            
        } catch (Exception $e) {
            echo jsonError('Erreur lors de la création de la formation: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Met à jour une formation
     * @param int $id ID de la formation
     */
    private function updateTraining($id) {
        try {
            // Vérification de l'existence de la formation
            $existingTraining = $this->trainingModel->getById($id);
            if (!$existingTraining) {
                echo jsonError('Formation non trouvée', 404);
                return;
            }
            
            // Récupération des données PUT
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                parse_str(file_get_contents('php://input'), $input);
            }
            
            // Nettoyage des données
            $data = sanitizeTrainingData($input);
            
            // Validation des données
            $validation = validateTrainingData($data);
            if (!$validation['valid']) {
                echo jsonError('Données invalides: ' . implode(', ', $validation['errors']), 400);
                return;
            }
            
            // Mise à jour de la formation avec tous les champs
            $success = $this->trainingModel->update($id, $data);
            
            if (!$success) {
                echo jsonError('Erreur lors de la mise à jour de la formation', 500);
                return;
            }
            
            // Récupération de la formation mise à jour
            $training = $this->trainingModel->getById($id);
            $training['date_formatted'] = formatDate($training['date']);
            $training['duration_formatted'] = $training['duration'] ? $training['duration'] . ' jour(s)' : '';
            $training['price_formatted'] = $training['price'] ? formatPrice($training['price']) : '';
            
            echo jsonSuccess($training, 'Formation mise à jour avec succès');
            
        } catch (Exception $e) {
            echo jsonError('Erreur lors de la mise à jour de la formation: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Supprime une formation
     * @param int $id ID de la formation
     */
    private function deleteTraining($id) {
        try {
            // Vérification de l'existence de la formation
            $existingTraining = $this->trainingModel->getById($id);
            if (!$existingTraining) {
                echo jsonError('Formation non trouvée', 404);
                return;
            }
            
            // Suppression de la formation
            $success = $this->trainingModel->delete($id);
            
            if (!$success) {
                echo jsonError('Erreur lors de la suppression de la formation', 500);
                return;
            }
            
            echo jsonSuccess(null, 'Formation supprimée avec succès');
            
        } catch (Exception $e) {
            echo jsonError('Erreur lors de la suppression de la formation: ' . $e->getMessage(), 500);
        }
    }
}
?> 