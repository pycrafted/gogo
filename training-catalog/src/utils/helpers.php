<?php
/**
 * Fonctions utilitaires - Couche Utilities
 * Fonctions d'aide pour l'application
 */

/**
 * Configuration CORS pour les requêtes AJAX
 */
function setupCORS() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    
    // Gérer les requêtes OPTIONS (preflight)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

/**
 * Envoie une réponse JSON
 * @param bool $success Statut de la réponse
 * @param string $message Message de la réponse
 * @param mixed $data Données à retourner
 * @param array $errors Erreurs de validation
 */
function sendJsonResponse($success, $message, $data = null, $errors = null) {
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    if ($errors !== null) {
        $response['errors'] = $errors;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Formate une date au format français
 * @param string $dateString Date au format YYYY-MM-DD
 * @return string Date formatée DD/MM/YYYY
 */
function formatDate($dateString) {
    if (!$dateString) return '';
    
    $date = new DateTime($dateString);
    return $date->format('d/m/Y');
}

/**
 * Valide un domaine de formation
 * @param string $domain Domaine à valider
 * @return bool True si le domaine est valide
 */
function validateDomain($domain) {
    $validDomains = [
        'Informatique',
        'Management',
        'Marketing',
        'Finance',
        'Ressources Humaines',
        'Communication',
        'Vente',
        'Logistique'
    ];
    
    return in_array($domain, $validDomains);
}

/**
 * Nettoie et valide les données d'entrée
 * @param mixed $data Données à nettoyer
 * @return mixed Données nettoyées
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeInput($value);
        }
    } else {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
    
    return $data;
}

/**
 * Valide une adresse email
 * @param string $email Email à valider
 * @return bool True si l'email est valide
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un numéro de téléphone
 * @param string $phone Numéro de téléphone à valider
 * @return bool True si le numéro est valide
 */
function validatePhone($phone) {
    // Supprimer tous les caractères non numériques
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Vérifier que le numéro a entre 10 et 15 chiffres
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

/**
 * Génère un token CSRF
 * @return string Token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Valide un token CSRF
 * @param string $token Token à valider
 * @return bool True si le token est valide
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirige vers une URL
 * @param string $url URL de redirection
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Affiche un message d'erreur
 * @param string $message Message d'erreur
 */
function showError($message) {
    echo "<div class='alert alert-danger'>$message</div>";
}

/**
 * Affiche un message de succès
 * @param string $message Message de succès
 */
function showSuccess($message) {
    echo "<div class='alert alert-success'>$message</div>";
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool True si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est administrateur
 * @return bool True si l'utilisateur est admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Démarre la session si elle n'est pas déjà démarrée
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Déconnecte l'utilisateur
 */
function logout() {
    startSession();
    session_destroy();
    redirect('/login.php');
}

/**
 * Formate un prix
 * @param float $price Prix à formater
 * @return string Prix formaté
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Formate une durée
 * @param int $duration Durée en jours
 * @return string Durée formatée
 */
function formatDuration($duration) {
    return $duration . ' jour(s)';
}

/**
 * Tronque un texte
 * @param string $text Texte à tronquer
 * @param int $length Longueur maximale
 * @return string Texte tronqué
 */
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . '...';
}
?> 