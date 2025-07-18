<?php
/**
 * Endpoint API pour les formations
 * Point d'entrée pour toutes les opérations CRUD sur les formations
 */

// Inclusion du contrôleur
require_once __DIR__ . '/../../src/controllers/TrainingController.php';

// Création d'une instance du contrôleur et gestion de la requête
$controller = new TrainingController();
$controller->handleRequest();
?> 