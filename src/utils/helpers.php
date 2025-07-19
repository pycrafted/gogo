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
    if (!$price) return 'N/A';
    return number_format($price, 0, ',', ' ') . ' F CFA';
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
    error_log("=== HELPERS - DEBUT DE VALIDATION ===");
    error_log("Données à valider: " . print_r($data, true));
    
    $errors = [];
    
    // Validation de la formation
    if (!isset($data['training_id']) || !is_numeric($data['training_id']) || $data['training_id'] <= 0) {
        $errors[] = 'La formation est requise';
        error_log("Erreur validation: formation invalide - " . ($data['training_id'] ?? 'non défini'));
    }
    
    // Validation du prénom
    if (!isset($data['first_name']) || empty(trim($data['first_name']))) {
        $errors[] = 'Le prénom est requis';
        error_log("Erreur validation: prénom manquant");
    } elseif (strlen($data['first_name']) > 100) {
        $errors[] = 'Le prénom ne doit pas dépasser 100 caractères';
        error_log("Erreur validation: prénom trop long - " . strlen($data['first_name']) . " caractères");
    }
    
    // Validation du nom
    if (!isset($data['last_name']) || empty(trim($data['last_name']))) {
        $errors[] = 'Le nom est requis';
        error_log("Erreur validation: nom manquant");
    } elseif (strlen($data['last_name']) > 100) {
        $errors[] = 'Le nom ne doit pas dépasser 100 caractères';
        error_log("Erreur validation: nom trop long - " . strlen($data['last_name']) . " caractères");
    }
    
    // Validation de l'email
    if (!isset($data['email']) || empty(trim($data['email']))) {
        $errors[] = 'L\'email est requis';
        error_log("Erreur validation: email manquant");
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'L\'email n\'est pas valide';
        error_log("Erreur validation: email invalide - " . $data['email']);
    } elseif (strlen($data['email']) > 255) {
        $errors[] = 'L\'email ne doit pas dépasser 255 caractères';
        error_log("Erreur validation: email trop long - " . strlen($data['email']) . " caractères");
    }
    
    // Validation du téléphone (optionnel)
    if (isset($data['phone']) && !empty($data['phone']) && strlen($data['phone']) > 20) {
        $errors[] = 'Le téléphone ne doit pas dépasser 20 caractères';
        error_log("Erreur validation: téléphone trop long - " . strlen($data['phone']) . " caractères");
    }
    
    // Validation de l'entreprise (optionnel)
    if (isset($data['company']) && !empty($data['company']) && strlen($data['company']) > 255) {
        $errors[] = 'L\'entreprise ne doit pas dépasser 255 caractères';
        error_log("Erreur validation: entreprise trop longue - " . strlen($data['company']) . " caractères");
    }
    
    // Validation du poste (optionnel)
    if (isset($data['position']) && !empty($data['position']) && strlen($data['position']) > 255) {
        $errors[] = 'Le poste ne doit pas dépasser 255 caractères';
        error_log("Erreur validation: poste trop long - " . strlen($data['position']) . " caractères");
    }
    
    $result = [
        'valid' => empty($errors),
        'errors' => $errors
    ];
    
    error_log("Résultat validation: " . print_r($result, true));
    error_log("=== HELPERS - FIN DE VALIDATION ===");
    
    return $result;
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
 * @param array $data Données brutes
 * @return array Données nettoyées
 */
function sanitizeParticipantData($data) {
    error_log("=== HELPERS - DEBUT DE NETTOYAGE ===");
    error_log("Données brutes reçues: " . print_r($data, true));
    
    $clean = [];
    
    // Nettoyage des champs obligatoires
    $clean['training_id'] = isset($data['training_id']) ? (int) $data['training_id'] : 0;
    $clean['first_name'] = isset($data['first_name']) ? trim(strip_tags($data['first_name'])) : '';
    $clean['last_name'] = isset($data['last_name']) ? trim(strip_tags($data['last_name'])) : '';
    $clean['email'] = isset($data['email']) ? trim(strtolower($data['email'])) : '';
    
    // Nettoyage des champs optionnels
    $clean['phone'] = isset($data['phone']) ? trim(strip_tags($data['phone'])) : '';
    $clean['company'] = isset($data['company']) ? trim(strip_tags($data['company'])) : '';
    $clean['position'] = isset($data['position']) ? trim(strip_tags($data['position'])) : '';
    $clean['notes'] = isset($data['notes']) ? trim(strip_tags($data['notes'])) : '';
    
    // Nettoyage du statut (important pour les mises à jour)
    if (isset($data['status'])) {
        $status = trim(strtolower($data['status']));
        // Valider que le statut est autorisé
        $allowedStatuses = ['pending', 'confirmed', 'cancelled'];
        if (in_array($status, $allowedStatuses)) {
            $clean['status'] = $status;
            error_log("Statut nettoyé et validé: $status");
        } else {
            error_log("Statut invalide reçu: $status, utilisation de 'pending' par défaut");
            $clean['status'] = 'pending';
        }
    } else {
        error_log("Aucun statut reçu, utilisation de 'pending' par défaut");
        $clean['status'] = 'pending';
    }
    
    error_log("Données nettoyées: " . print_r($clean, true));
    error_log("=== HELPERS - FIN DE NETTOYAGE ===");
    
    return $clean;
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