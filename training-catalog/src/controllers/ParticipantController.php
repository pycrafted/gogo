<?php
/**
 * Contrôleur Participant - Couche Business Logic
 * Gère toutes les opérations CRUD pour les participants
 */

require_once __DIR__ . '/../models/Participant.php';
require_once __DIR__ . '/../utils/helpers.php';

class ParticipantController {
    private $participant;
    
    public function __construct() {
        $this->participant = new Participant();
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
        
        // Extraire l'ID du participant si présent
        $participantId = null;
        if (count($pathParts) > 2 && is_numeric($pathParts[2])) {
            $participantId = (int)$pathParts[2];
        }
        
        try {
            switch ($method) {
                case 'GET':
                    if ($participantId) {
                        $this->getParticipant($participantId);
                    } else {
                        $this->getAllParticipants();
                    }
                    break;
                    
                case 'POST':
                    $this->createParticipant();
                    break;
                    
                case 'PUT':
                    if ($participantId) {
                        $this->updateParticipant($participantId);
                    } else {
                        sendJsonResponse(false, 'ID du participant requis pour la mise à jour');
                    }
                    break;
                    
                case 'DELETE':
                    if ($participantId) {
                        $this->deleteParticipant($participantId);
                    } else {
                        sendJsonResponse(false, 'ID du participant requis pour la suppression');
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
     * Récupère tous les participants
     */
    private function getAllParticipants() {
        $participants = $this->participant->getAll();
        
        // Formater les données pour l'affichage
        foreach ($participants as &$participant) {
            $participant['registration_date_formatted'] = formatDate($participant['registration_date']);
            $participant['full_name'] = $participant['first_name'] . ' ' . $participant['last_name'];
        }
        
        sendJsonResponse(true, 'Participants récupérés avec succès', $participants);
    }
    
    /**
     * Récupère un participant par son ID
     */
    private function getParticipant($id) {
        $participant = $this->participant->getById($id);
        
        if (!$participant) {
            sendJsonResponse(false, 'Participant non trouvé');
            return;
        }
        
        // Formater les données pour l'affichage
        $participant['registration_date_formatted'] = formatDate($participant['registration_date']);
        $participant['full_name'] = $participant['first_name'] . ' ' . $participant['last_name'];
        
        sendJsonResponse(true, 'Participant récupéré avec succès', $participant);
    }
    
    /**
     * Crée une nouvelle inscription
     */
    private function createParticipant() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            sendJsonResponse(false, 'Données JSON invalides');
            return;
        }
        
        // Validation des données
        $errors = $this->validateParticipantData($input);
        if (!empty($errors)) {
            sendJsonResponse(false, 'Données invalides', null, $errors);
            return;
        }
        
        // Vérifier si déjà inscrit
        if ($this->participant->isAlreadyRegistered($input['email'], $input['training_id'])) {
            sendJsonResponse(false, 'Vous êtes déjà inscrit à cette formation');
            return;
        }
        
        // Création de l'inscription
        $participantId = $this->participant->create($input);
        
        sendJsonResponse(true, 'Inscription créée avec succès', ['id' => $participantId]);
    }
    
    /**
     * Met à jour un participant
     */
    private function updateParticipant($id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            sendJsonResponse(false, 'Données JSON invalides');
            return;
        }
        
        // Validation des données
        $errors = $this->validateParticipantData($input);
        if (!empty($errors)) {
            sendJsonResponse(false, 'Données invalides', null, $errors);
            return;
        }
        
        // Vérifier que le participant existe
        $existingParticipant = $this->participant->getById($id);
        if (!$existingParticipant) {
            sendJsonResponse(false, 'Participant non trouvé');
            return;
        }
        
        // Mise à jour du participant
        $success = $this->participant->update($id, $input);
        
        if ($success) {
            sendJsonResponse(true, 'Participant mis à jour avec succès');
        } else {
            sendJsonResponse(false, 'Erreur lors de la mise à jour du participant');
        }
    }
    
    /**
     * Supprime un participant
     */
    private function deleteParticipant($id) {
        // Vérifier que le participant existe
        $existingParticipant = $this->participant->getById($id);
        if (!$existingParticipant) {
            sendJsonResponse(false, 'Participant non trouvé');
            return;
        }
        
        // Suppression du participant
        $success = $this->participant->delete($id);
        
        if ($success) {
            sendJsonResponse(true, 'Participant supprimé avec succès');
        } else {
            sendJsonResponse(false, 'Erreur lors de la suppression du participant');
        }
    }
    
    /**
     * Valide les données d'un participant
     */
    private function validateParticipantData($data) {
        $errors = [];
        
        // Validation du prénom
        if (empty($data['first_name'])) {
            $errors[] = 'Le prénom est requis';
        } elseif (strlen($data['first_name']) > 50) {
            $errors[] = 'Le prénom ne peut pas dépasser 50 caractères';
        }
        
        // Validation du nom
        if (empty($data['last_name'])) {
            $errors[] = 'Le nom est requis';
        } elseif (strlen($data['last_name']) > 50) {
            $errors[] = 'Le nom ne peut pas dépasser 50 caractères';
        }
        
        // Validation de l'email
        if (empty($data['email'])) {
            $errors[] = 'L\'email est requis';
        } elseif (!validateEmail($data['email'])) {
            $errors[] = 'L\'email n\'est pas valide';
        }
        
        // Validation du téléphone (optionnel)
        if (!empty($data['phone']) && !validatePhone($data['phone'])) {
            $errors[] = 'Le numéro de téléphone n\'est pas valide';
        }
        
        // Validation de l'entreprise (optionnel)
        if (!empty($data['company']) && strlen($data['company']) > 100) {
            $errors[] = 'Le nom de l\'entreprise ne peut pas dépasser 100 caractères';
        }
        
        // Validation de la formation
        if (!isset($data['training_id']) || !is_numeric($data['training_id'])) {
            $errors[] = 'L\'ID de la formation est requis et doit être un nombre';
        }
        
        return $errors;
    }
}
?> 