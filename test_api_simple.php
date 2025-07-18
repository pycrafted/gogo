<?php
/**
 * Test simple de l'API avec des données de test
 */

echo "🧪 Test simple de l'API...\n";

// Simuler une requête POST
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/participants.php';
$_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';

// Données de test
$testData = [
    'training_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'phone' => '0123456789',
    'company' => 'TestCorp',
    'position' => 'Développeur',
    'notes' => 'Test simple'
];

echo "📤 Données de test: " . json_encode($testData) . "\n\n";

// Inclure et exécuter le contrôleur
require_once 'src/controllers/ParticipantController.php';

try {
    $controller = new ParticipantController();
    $controller->handleRequest();
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "📋 Stack trace: " . $e->getTraceAsString() . "\n";
}
?> 