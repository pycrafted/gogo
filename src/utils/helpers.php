<?php
/**
 * Fonctions utilitaires - Couche Utilities
 * Validation, formatage et fonctions d'aide
 */

/**
 * Nettoie et valide une entrée utilisateur
 * @param string $input Entrée à nettoyer
 * @return string Entrée nettoyée
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Valide une date au format YYYY-MM-DD
 * @param string $date Date à valider
 * @return bool True si la date est valide
 */
function validateDate($date) {
    if (empty($date)) {
        return false;
    }
    
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Valide un domaine de formation
 * @param string $domain Domaine à valider
 * @return bool True si le domaine est valide
 */
function validateDomain($domain) {
    if (empty($domain) || strlen($domain) > 100) {
        return false;
    }
    
    // Liste des domaines autorisés (peut être étendue)
    $allowedDomains = [
        'Informatique', 'Management', 'Marketing', 'Finance', 
        'Ressources Humaines', 'Communication', 'Vente', 'Logistique'
    ];
    
    return in_array($domain, $allowedDomains);
}

/**
 * Valide un titre de formation
 * @param string $title Titre à valider
 * @return bool True si le titre est valide
 */
function validateTitle($title) {
    if (empty($title) || strlen($title) > 255) {
        return false;
    }
    
    return true;
}

/**
 * Formate une date pour l'affichage
 * @param string $date Date au format YYYY-MM-DD
 * @return string Date formatée (DD/MM/YYYY)
 */
function formatDate($date) {
    if (!validateDate($date)) {
        return '';
    }
    
    $dateObj = DateTime::createFromFormat('Y-m-d', $date);
    return $dateObj->format('d/m/Y');
}

/**
 * Formate une date pour l'input HTML
 * @param string $date Date au format YYYY-MM-DD
 * @return string Date formatée pour input type="date"
 */
function formatDateForInput($date) {
    if (!validateDate($date)) {
        return date('Y-m-d'); // Date actuelle par défaut
    }
    
    return $date;
}

/**
 * Formate un prix pour l'affichage
 * @param float $price Prix à formater
 * @return string Prix formaté en euros
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Génère un message d'erreur JSON
 * @param string $message Message d'erreur
 * @param int $code Code d'erreur HTTP
 * @return string JSON d'erreur
 */
function jsonError($message, $code = 400) {
    http_response_code($code);
    return json_encode([
        'success' => false,
        'error' => $message
    ]);
}

/**
 * Génère un message de succès JSON
 * @param mixed $data Données à retourner
 * @param string $message Message de succès
 * @return string JSON de succès
 */
function jsonSuccess($data = null, $message = 'Opération réussie') {
    return json_encode([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * Valide les données d'une formation
 * @param array $data Données à valider
 * @return array Tableau avec 'valid' (bool) et 'errors' (array)
 */
function validateTrainingData($data) {
    $errors = [];
    
    // Validation du domaine
    if (!isset($data['domain']) || !validateDomain($data['domain'])) {
        $errors[] = 'Le domaine est requis et doit être valide';
    }
    
    // Validation du titre
    if (!isset($data['title']) || !validateTitle($data['title'])) {
        $errors[] = 'Le titre est requis et ne doit pas dépasser 255 caractères';
    }
    
    // Validation de la date
    if (!isset($data['date']) || !validateDate($data['date'])) {
        $errors[] = 'La date est requise et doit être au format YYYY-MM-DD';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Valide les données d'un participant
 * @param array $data Données à valider
 * @return array Tableau avec 'valid' (bool) et 'errors' (array)
 */
function validateParticipantData($data) {
    $errors = [];
    
    // Validation de la formation
    if (!isset($data['training_id']) || !is_numeric($data['training_id']) || $data['training_id'] <= 0) {
        $errors[] = 'La formation est requise';
    }
    
    // Validation du prénom
    if (!isset($data['first_name']) || empty(trim($data['first_name']))) {
        $errors[] = 'Le prénom est requis';
    } elseif (strlen($data['first_name']) > 100) {
        $errors[] = 'Le prénom ne doit pas dépasser 100 caractères';
    }
    
    // Validation du nom
    if (!isset($data['last_name']) || empty(trim($data['last_name']))) {
        $errors[] = 'Le nom est requis';
    } elseif (strlen($data['last_name']) > 100) {
        $errors[] = 'Le nom ne doit pas dépasser 100 caractères';
    }
    
    // Validation de l'email
    if (!isset($data['email']) || empty(trim($data['email']))) {
        $errors[] = 'L\'email est requis';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email n\'est pas valide';
    } elseif (strlen($data['email']) > 255) {
        $errors[] = 'L\'email ne doit pas dépasser 255 caractères';
    }
    
    // Validation du téléphone (optionnel)
    if (isset($data['phone']) && !empty($data['phone']) && strlen($data['phone']) > 20) {
        $errors[] = 'Le téléphone ne doit pas dépasser 20 caractères';
    }
    
    // Validation de l'entreprise (optionnel)
    if (isset($data['company']) && !empty($data['company']) && strlen($data['company']) > 255) {
        $errors[] = 'L\'entreprise ne doit pas dépasser 255 caractères';
    }
    
    // Validation du poste (optionnel)
    if (isset($data['position']) && !empty($data['position']) && strlen($data['position']) > 255) {
        $errors[] = 'Le poste ne doit pas dépasser 255 caractères';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors
    ];
}

/**
 * Nettoie les données d'une formation
 * @param array $data Données à nettoyer
 * @return array Données nettoyées
 */
function sanitizeTrainingData($data) {
    return [
        'domain' => isset($data['domain']) ? sanitizeInput($data['domain']) : '',
        'title' => isset($data['title']) ? sanitizeInput($data['title']) : '',
        'location' => isset($data['location']) ? sanitizeInput($data['location']) : '',
        'date' => isset($data['date']) ? sanitizeInput($data['date']) : '',
        'duration' => isset($data['duration']) ? (int) $data['duration'] : 0,
        'price' => isset($data['price']) ? (float) $data['price'] : 0.0,
        'animators' => isset($data['animators']) ? sanitizeInput($data['animators']) : '',
        'program' => isset($data['program']) ? sanitizeInput($data['program']) : ''
    ];
}

/**
 * Nettoie les données d'un participant
 * @param array $data Données à nettoyer
 * @return array Données nettoyées
 */
function sanitizeParticipantData($data) {
    return [
        'training_id' => isset($data['training_id']) ? (int) $data['training_id'] : 0,
        'first_name' => isset($data['first_name']) ? sanitizeInput($data['first_name']) : '',
        'last_name' => isset($data['last_name']) ? sanitizeInput($data['last_name']) : '',
        'email' => isset($data['email']) ? strtolower(trim($data['email'])) : '',
        'phone' => isset($data['phone']) ? sanitizeInput($data['phone']) : '',
        'company' => isset($data['company']) ? sanitizeInput($data['company']) : '',
        'position' => isset($data['position']) ? sanitizeInput($data['position']) : '',
        'status' => isset($data['status']) ? sanitizeInput($data['status']) : 'pending',
        'notes' => isset($data['notes']) ? sanitizeInput($data['notes']) : ''
    ];
}

/**
 * Vérifie si une requête est de type AJAX
 * @return bool True si c'est une requête AJAX
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Définit les en-têtes CORS pour les requêtes AJAX
 */
function setCorsHeaders() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    header('Content-Type: application/json; charset=utf-8');
}

/**
 * Gère les requêtes OPTIONS (preflight CORS)
 */
function handleOptionsRequest() {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        setCorsHeaders();
        http_response_code(200);
        exit();
    }
}
?> 