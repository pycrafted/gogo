<?php
/**
 * Test de l'API HTTP après correction
 */

echo "🧪 Test de l'API HTTP...\n";

$timestamp = time();
$testData = [
    'training_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'API',
    'email' => "testapi{$timestamp}@example.com",
    'phone' => '0123456789',
    'company' => 'TestCorp',
    'position' => 'Développeur',
    'notes' => 'Test API HTTP'
];

echo "📤 Données envoyées: " . json_encode($testData) . "\n\n";

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
        echo "❌ Impossible d'accéder à l'API\n";
    } else {
        echo "📥 Réponse reçue:\n";
        echo $response . "\n\n";
        
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "✅ API HTTP fonctionnelle !\n";
            echo "📊 Participant créé avec succès\n";
        } else {
            echo "❌ Erreur dans l'API HTTP\n";
            if ($data) {
                echo "📋 Détails: " . json_encode($data) . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test terminé !\n";
?> 