<?php
/**
 * Test direct de l'API des participants
 */

// Simuler une requête POST vers l'API
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
    'position' => 'Développeur'
];

// Simuler le contenu POST
file_put_contents('php://input', json_encode($testData));

echo "🧪 Test de l'API des participants...\n";
echo "📤 Données envoyées: " . json_encode($testData) . "\n\n";

// Inclure et exécuter le contrôleur
require_once 'src/controllers/ParticipantController.php';

try {
    $controller = new ParticipantController();
    $controller->handleRequest();
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?> 