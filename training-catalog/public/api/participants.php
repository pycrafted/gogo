<?php
/**
 * Endpoint API pour les participants
 * Point d'entrée pour toutes les opérations CRUD sur les participants
 */

// Inclusion du contrôleur
require_once __DIR__ . '/../../src/controllers/ParticipantController.php';

// Création d'une instance du contrôleur et gestion de la requête
$controller = new ParticipantController();
$controller->handleRequest();
?> 