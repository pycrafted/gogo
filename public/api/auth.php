<?php
/**
 * API d'authentification pour l'administration
 * Gère la connexion et déconnexion des administrateurs
 */

// Désactiver l'affichage des warnings pour éviter qu'ils polluent la réponse JSON
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 0);

// Démarrer la session
session_start();

// Headers CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Inclusion des fichiers nécessaires
require_once __DIR__ . '/../../src/config.php';
require_once __DIR__ . '/../../src/models/User.php';
require_once __DIR__ . '/../../src/utils/helpers.php';

/**
 * Envoie une réponse JSON
 */
function sendJsonResponse($success, $message, $data = null, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Authentifie un utilisateur
 */
function authenticateUser($username, $password) {
    try {
        error_log("=== AUTH - DEBUT D'AUTHENTIFICATION ===");
        error_log("Tentative de connexion pour l'utilisateur: $username");
        
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);
        
        if ($user) {
            error_log("✅ Authentification réussie pour: " . $user['username']);
            error_log("Rôle: " . $user['role']);
            
            // Créer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            error_log("Session créée avec succès");
            error_log("Session ID: " . session_id());
            error_log("=== AUTH - FIN D'AUTHENTIFICATION ===");
            
            return $user;
        } else {
            error_log("❌ Échec d'authentification pour: $username");
            error_log("=== AUTH - FIN D'AUTHENTIFICATION ===");
            return null;
        }
        
    } catch (Exception $e) {
        error_log("🚨 Erreur lors de l'authentification: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        throw $e;
    }
}

/**
 * Déconnecte un utilisateur
 */
function logoutUser() {
    error_log("=== AUTH - DEBUT DE DÉCONNEXION ===");
    
    // Détruire la session
    session_destroy();
    
    error_log("✅ Session détruite avec succès");
    error_log("=== AUTH - FIN DE DÉCONNEXION ===");
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isUserLoggedIn() {
    // Vérifier si la session existe et est valide
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    
    // Vérifier si la session n'a pas expiré (30 minutes)
    $sessionTimeout = 30 * 60; // 30 minutes en secondes
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $sessionTimeout) {
        error_log("❌ Session expirée pour l'utilisateur: " . ($_SESSION['username'] ?? 'inconnu'));
        session_destroy();
        return false;
    }
    
    // Mettre à jour le temps de connexion pour prolonger la session
    $_SESSION['login_time'] = time();
    
    return true;
}

/**
 * Vérifie si l'utilisateur est administrateur
 */
function isUserAdmin() {
    return isUserLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Gestion des requêtes
try {
    $method = $_SERVER['REQUEST_METHOD'];
    error_log("=== AUTH - REQUÊTE RECUE ===");
    error_log("Méthode: $method");
    error_log("URI: " . $_SERVER['REQUEST_URI']);
    
    switch ($method) {
        case 'POST':
            // Authentification
            $rawInput = file_get_contents('php://input');
            error_log("Raw input reçu: " . $rawInput);
            
            $input = json_decode($rawInput, true);
            error_log("JSON decode result: " . (is_array($input) ? json_encode($input) : (string)$input));
            
            if (!$input) {
                error_log("❌ Données JSON invalides");
                sendJsonResponse(false, 'Données invalides', null, 400);
            }
            
            $username = trim($input['username'] ?? '');
            $password = $input['password'] ?? '';
            
            error_log("Données d'authentification:");
            error_log("Username: $username");
            error_log("Password: " . (empty($password) ? 'VIDE' : 'PRÉSENT'));
            
            if (empty($username) || empty($password)) {
                error_log("❌ Champs manquants");
                sendJsonResponse(false, 'Nom d\'utilisateur et mot de passe requis', null, 400);
            }
            
            $user = authenticateUser($username, $password);
            
            if ($user) {
                error_log("✅ Authentification réussie, envoi de la réponse");
                sendJsonResponse(true, 'Connexion réussie', [
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ],
                    'session_id' => session_id()
                ]);
            } else {
                error_log("❌ Authentification échouée");
                sendJsonResponse(false, 'Nom d\'utilisateur ou mot de passe incorrect', null, 401);
            }
            break;
            
        case 'GET':
            // Vérification du statut de connexion
            if (isUserLoggedIn()) {
                error_log("✅ Utilisateur connecté: " . $_SESSION['username']);
                sendJsonResponse(true, 'Utilisateur connecté', [
                    'user' => [
                        'id' => $_SESSION['user_id'],
                        'username' => $_SESSION['username'],
                        'email' => $_SESSION['user_email'],
                        'role' => $_SESSION['user_role']
                    ],
                    'session_id' => session_id()
                ]);
            } else {
                error_log("❌ Aucun utilisateur connecté");
                sendJsonResponse(false, 'Aucun utilisateur connecté', null, 401);
            }
            break;
            
        case 'DELETE':
            // Déconnexion
            logoutUser();
            sendJsonResponse(true, 'Déconnexion réussie');
            break;
            
        default:
            error_log("❌ Méthode non autorisée: $method");
            sendJsonResponse(false, 'Méthode non autorisée', null, 405);
            break;
    }
    
} catch (Exception $e) {
    error_log("🚨 ERREUR CRITIQUE dans auth.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    sendJsonResponse(false, 'Erreur serveur: ' . $e->getMessage(), null, 500);
}
?> 