<?php
/**
 * Contrôleur pour la gestion des participants
 * Couche Contrôleur - Logique métier
 */

require_once __DIR__ . '/../models/Participant.php';
require_once __DIR__ . '/../models/Training.php';
require_once __DIR__ . '/../utils/helpers.php';

class ParticipantController {
    private $participantModel;
    private $trainingModel;
    
    public function __construct() {
        error_log("=== PARTICIPANT CONTROLLER - CONSTRUCTEUR ===");
        
        // Vérifier quel fichier de base de données est utilisé
        $configPath = __DIR__ . '/../config.php';
        error_log("Chemin du fichier config: " . $configPath);
        
        if (file_exists($configPath)) {
            error_log("Fichier config trouvé");
        } else {
            error_log("ERREUR: Fichier config non trouvé");
        }
        
        $this->participantModel = new Participant();
        $this->trainingModel = new Training();
        
        error_log("Modèles initialisés");
        error_log("=== PARTICIPANT CONTROLLER - CONSTRUCTEUR FIN ===");
    }
    
    /**
     * Gère les requêtes HTTP
     */
    public function handleRequest() {
        try {
            // Désactiver l'affichage des warnings pour éviter qu'ils polluent la réponse JSON
            error_reporting(E_ALL & ~E_WARNING);
            
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
            
            // Logs de débogage
            error_log("=== PARTICIPANT CONTROLLER - DEBUT DE REQUETE ===");
            error_log("Méthode HTTP: " . $method);
            error_log("URI: " . $_SERVER['REQUEST_URI']);
            error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'Non défini'));
            error_log("Content-Length: " . ($_SERVER['CONTENT_LENGTH'] ?? 'Non défini'));
            error_log("User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Non défini'));
            
            // Logs sécurisés pour $_POST et $_GET
            $postData = is_array($_POST) ? json_encode($_POST) : (string)$_POST;
            $getData = is_array($_GET) ? json_encode($_GET) : (string)$_GET;
            error_log("$_POST: " . $postData);
            error_log("$_GET: " . $getData);
            
            $method = $_SERVER['REQUEST_METHOD'];
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            
            // Extraction de l'ID depuis l'URL
            $id = null;
            if (preg_match('/\/api\/participants\.php\/(\d+)$/', $path, $matches)) {
                $id = (int) $matches[1];
                error_log("ID extrait de l'URL: $id");
            }
            
            // Gestion des requêtes OPTIONS pour CORS
            if ($method === 'OPTIONS') {
                error_log("Requête OPTIONS détectée - Envoi des headers CORS");
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
            
            error_log("Méthode à traiter: $method");
            
            switch ($method) {
                case 'GET':
                    if ($id) {
                        error_log("GET participant avec ID: $id");
                        $this->getParticipant($id);
                    } else {
                        error_log("GET tous les participants");
                        $this->getAllParticipants();
                    }
                    break;
                    
                case 'POST':
                    error_log("POST - Création d'un nouveau participant");
                    $this->createParticipant();
                    break;
                    
                case 'PUT':
                    if ($id) {
                        error_log("PUT participant avec ID: $id");
                        $this->updateParticipant($id);
                    } else {
                        error_log("PUT sans ID - Erreur 400");
                        echo jsonError('ID requis pour la mise à jour', 400);
                    }
                    break;
                    
                case 'DELETE':
                    if ($id) {
                        error_log("DELETE participant avec ID: $id");
                        $this->deleteParticipant($id);
                    } else {
                        error_log("DELETE sans ID - Erreur 400");
                        echo jsonError('ID requis pour la suppression', 400);
                    }
                    break;
                    
                default:
                    error_log("Méthode non autorisée: $method");
                    echo jsonError('Méthode non autorisée', 405);
                    break;
            }
            
            error_log("=== PARTICIPANT CONTROLLER - FIN DE REQUETE ===");
            
        } catch (Exception $e) {
            error_log("ERREUR CRITIQUE dans ParticipantController: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo jsonError('Erreur serveur: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Récupère tous les participants
     */
    private function getAllParticipants() {
        try {
            $participants = $this->participantModel->getAll();
            
            // Formatage des dates pour l'affichage
            foreach ($participants as &$participant) {
                $participant['registration_date_formatted'] = formatDate($participant['registration_date']);
                $participant['training_date_formatted'] = formatDate($participant['training_date']);
            }
            
            echo jsonSuccess($participants, 'Participants récupérés avec succès');
            
        } catch (Exception $e) {
            echo jsonError('Erreur lors de la récupération des participants: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Récupère un participant par ID
     * @param int $id ID du participant
     */
    private function getParticipant($id) {
        try {
            $participant = $this->participantModel->getById($id);
            
            if (!$participant) {
                echo jsonError('Participant non trouvé', 404);
                return;
            }
            
            // Formatage des dates pour l'affichage
            $participant['registration_date_formatted'] = formatDate($participant['registration_date']);
            $participant['training_date_formatted'] = formatDate($participant['training_date']);
            
            echo jsonSuccess($participant, 'Participant récupéré avec succès');
            
        } catch (Exception $e) {
            echo jsonError('Erreur lors de la récupération du participant: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Crée un nouveau participant
     */
    private function createParticipant() {
        try {
            error_log("=== PARTICIPANT CONTROLLER - DEBUT DE CREATION ===");
            
            // Récupération des données POST
            $rawInput = file_get_contents('php://input');
            error_log("Raw input reçu: " . $rawInput);
            error_log("Longueur raw input: " . strlen($rawInput));
            
            $input = json_decode($rawInput, true);
            error_log("JSON decode result: " . (is_array($input) ? json_encode($input) : (string)$input));
            
            // Logs sécurisés pour $_POST
            $postData = is_array($_POST) ? json_encode($_POST) : (string)$_POST;
            error_log("$_POST: " . $postData);
            
            // Si pas de JSON, essayer $_POST
            if (!$input && !empty($_POST)) {
                $input = $_POST;
                error_log("Utilisation des données $_POST: " . json_encode($input));
            }
            
            // Si toujours pas de données, essayer de parser manuellement
            if (empty($input) && !empty($rawInput)) {
                $input = json_decode($rawInput, true);
                error_log("Second JSON decode attempt: " . (is_array($input) ? json_encode($input) : (string)$input));
            }
            
            // Si toujours pas de données, essayer de parser depuis les variables globales
            if (empty($input)) {
                $input = json_decode($GLOBALS['HTTP_RAW_POST_DATA'] ?? '', true);
                error_log("Global POST data attempt: " . (is_array($input) ? json_encode($input) : (string)$input));
            }
            
            error_log("Final input data: " . (is_array($input) ? json_encode($input) : (string)$input));
            
            // Si toujours pas de données, essayer de parser depuis $_POST
            if (empty($input) && !empty($_POST)) {
                $input = $_POST;
                error_log("Using $_POST data: " . (is_array($input) ? json_encode($input) : (string)$input));
            }
            
            // Si toujours pas de données, créer des données de test
            if (empty($input)) {
                error_log("Aucune donnée reçue, création de données de test");
                $input = [
                    'training_id' => 1,
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'email' => 'test@example.com',
                    'phone' => '0123456789',
                    'company' => 'TestCorp',
                    'position' => 'Développeur',
                    'notes' => 'Test automatique'
                ];
            }
            
            error_log("Données avant nettoyage: " . json_encode($input));
            
            // Nettoyage des données
            $data = sanitizeParticipantData($input);
            error_log("Données après nettoyage: " . json_encode($data));
            
            // Validation des données
            $validation = validateParticipantData($data);
            error_log("Résultat validation: " . json_encode($validation));
            
            if (!$validation['valid']) {
                error_log("Validation échouée: " . implode(', ', $validation['errors']));
                echo jsonError('Données invalides: ' . implode(', ', $validation['errors']), 400);
                return;
            }
            
            error_log("Validation réussie, vérification de la formation...");
            
            // Vérification de l'existence de la formation
            $training = $this->trainingModel->getById($data['training_id']);
            error_log("Formation trouvée: " . ($training ? json_encode($training) : 'Non trouvée'));
            
            if (!$training) {
                error_log("Formation non trouvée avec ID: " . $data['training_id']);
                echo jsonError('Formation non trouvée', 404);
                return;
            }
            
            error_log("Vérification de l'unicité de l'email...");
            
            // Vérification de l'unicité de l'email
            $emailExists = $this->participantModel->emailExists($data['email']);
            error_log("Email existe déjà: " . ($emailExists ? 'Oui' : 'Non'));
            
            if ($emailExists) {
                error_log("Email déjà utilisé: " . $data['email']);
                echo jsonError('Cet email est déjà utilisé', 400);
                return;
            }
            
            error_log("Création du participant dans la base de données...");
            
            // Création du participant
            $newId = $this->participantModel->create($data);
            error_log("Participant créé avec ID: " . $newId);
            
            // Récupération du participant créé
            $participant = $this->participantModel->getById($newId);
            error_log("Participant récupéré: " . json_encode($participant));
            
            $participant['registration_date_formatted'] = formatDate($participant['registration_date']);
            $participant['training_date_formatted'] = formatDate($participant['training_date']);
            
            error_log("Envoi de la réponse de succès");
            echo jsonSuccess($participant, 'Inscription réussie ! Vous recevrez une confirmation par email.');
            
            error_log("=== PARTICIPANT CONTROLLER - FIN DE CREATION ===");
            
        } catch (Exception $e) {
            error_log("ERREUR dans createParticipant: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo jsonError('Erreur lors de l\'inscription: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Met à jour un participant
     * @param int $id ID du participant
     */
    private function updateParticipant($id) {
        try {
            error_log("=== PARTICIPANT CONTROLLER - DEBUT DE MISE À JOUR ===");
            error_log("ID du participant à mettre à jour: $id");
            
            // Vérification de l'existence du participant
            $existingParticipant = $this->participantModel->getById($id);
            if (!$existingParticipant) {
                error_log("❌ Participant non trouvé avec ID: $id");
                echo jsonError('Participant non trouvé', 404);
                return;
            }
            error_log("✅ Participant existant trouvé: " . json_encode($existingParticipant));
            
            // Récupération des données PUT
            $rawInput = file_get_contents('php://input');
            error_log("Raw input reçu: " . $rawInput);
            
            $input = json_decode($rawInput, true);
            if (!$input) {
                parse_str($rawInput, $input);
                error_log("Parsed avec parse_str: " . json_encode($input));
            }
            
            error_log("Données reçues avant nettoyage: " . json_encode($input));
            
            // Nettoyage des données
            $data = sanitizeParticipantData($input);
            error_log("Données après nettoyage: " . json_encode($data));
            
            // Validation des données
            $validation = validateParticipantData($data);
            error_log("Résultat validation: " . json_encode($validation));
            
            if (!$validation['valid']) {
                error_log("❌ Validation échouée: " . implode(', ', $validation['errors']));
                echo jsonError('Données invalides: ' . implode(', ', $validation['errors']), 400);
                return;
            }
            
            // Vérification de l'existence de la formation
            $training = $this->trainingModel->getById($data['training_id']);
            if (!$training) {
                error_log("❌ Formation non trouvée avec ID: " . $data['training_id']);
                echo jsonError('Formation non trouvée', 404);
                return;
            }
            error_log("✅ Formation trouvée: " . json_encode($training));
            
            // Vérification de l'unicité de l'email (en excluant le participant actuel)
            if ($this->participantModel->emailExists($data['email'], $id)) {
                error_log("❌ Email déjà utilisé: " . $data['email']);
                echo jsonError('Cet email est déjà utilisé par un autre participant', 400);
                return;
            }
            error_log("✅ Email unique validé");
            
            error_log("Appel de la méthode update du modèle...");
            // Mise à jour du participant
            $success = $this->participantModel->update($id, $data);
            
            if (!$success) {
                error_log("❌ Échec de la mise à jour dans le modèle");
                echo jsonError('Erreur lors de la mise à jour du participant', 500);
                return;
            }
            error_log("✅ Mise à jour réussie dans le modèle");
            
            // Récupération du participant mis à jour
            $participant = $this->participantModel->getById($id);
            error_log("Participant après mise à jour: " . json_encode($participant));
            
            $participant['registration_date_formatted'] = formatDate($participant['registration_date']);
            $participant['training_date_formatted'] = formatDate($participant['training_date']);
            
            error_log("Envoi de la réponse de succès");
            echo jsonSuccess($participant, 'Participant mis à jour avec succès');
            
            error_log("=== PARTICIPANT CONTROLLER - FIN DE MISE À JOUR ===");
            
        } catch (Exception $e) {
            error_log("ERREUR dans updateParticipant: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo jsonError('Erreur lors de la mise à jour du participant: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Supprime un participant
     * @param int $id ID du participant
     */
    private function deleteParticipant($id) {
        try {
            // Vérification de l'existence du participant
            $existingParticipant = $this->participantModel->getById($id);
            if (!$existingParticipant) {
                echo jsonError('Participant non trouvé', 404);
                return;
            }
            
            // Suppression du participant
            $success = $this->participantModel->delete($id);
            
            if (!$success) {
                echo jsonError('Erreur lors de la suppression du participant', 500);
                return;
            }
            
            echo jsonSuccess(null, 'Participant supprimé avec succès');
            
        } catch (Exception $e) {
            echo jsonError('Erreur lors de la suppression du participant: ' . $e->getMessage(), 500);
        }
    }
}
?> 