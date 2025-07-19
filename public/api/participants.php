<?php
/**
 * Endpoint API pour les participants
 * Point d'entrée pour toutes les opérations CRUD sur les participants
 */

// Désactiver l'affichage des warnings pour éviter qu'ils polluent la réponse JSON
error_reporting(E_ALL & ~E_WARNING);
ini_set('display_errors', 0);

// Inclusion des fonctions utilitaires
require_once __DIR__ . '/../../src/utils/helpers.php';

// Inclusion du contrôleur
require_once __DIR__ . '/../../src/controllers/ParticipantController.php';

// Création d'une instance du contrôleur et gestion de la requête
$controller = new ParticipantController();
$controller->handleRequest();
?> 