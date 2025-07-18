<?php
/**
 * Test de l'API POST avec des données réelles
 */

echo "🧪 Test de l'API POST...\n";

// Données de test
$testData = [
    'training_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'API',
    'email' => 'testapi@example.com',
    'phone' => '0123456789',
    'company' => 'TestCorp',
    'position' => 'Développeur',
    'notes' => 'Test API POST'
];

echo "📤 Données envoyées: " . json_encode($testData) . "\n\n";

// Test avec file_get_contents
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($testData)
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/participants.php', false, $context);
    
    if ($response === false) {
        echo "❌ Erreur: Impossible d'accéder à l'API\n";
    } else {
        echo "📥 Réponse reçue:\n";
        echo $response . "\n";
        
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "✅ Inscription réussie !\n";
        } else {
            echo "❌ Erreur dans l'inscription\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test terminé !\n";
?> 