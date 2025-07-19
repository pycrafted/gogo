<?php
/**
 * Test final de l'inscription
 */

echo "🎯 Test final de l'inscription...\n";

// Test d'inscription via l'API
$timestamp = time();
$testData = [
    'training_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'Final',
    'email' => "testfinal{$timestamp}@example.com",
    'phone' => '0123456789',
    'company' => 'TestCorp',
    'position' => 'Développeur',
    'notes' => 'Test final'
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
        echo "❌ Erreur: Impossible d'accéder à l'API\n";
    } else {
        echo "📥 Réponse reçue:\n";
        echo $response . "\n\n";
        
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "✅ INSCRIPTION RÉUSSIE !\n";
            echo "📊 Participant créé avec succès\n";
            echo "📧 Email: {$testData['email']}\n";
        } else {
            echo "❌ Erreur dans l'inscription\n";
            if ($data) {
                echo "📋 Détails: " . json_encode($data) . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test terminé !\n";
echo "\n📋 Instructions pour tester l'inscription:\n";
echo "1. Ouvrez http://localhost:8000/index.html dans votre navigateur\n";
echo "2. Cliquez sur '📖' pour voir le programme d'une formation\n";
echo "3. Cliquez sur 'S'inscrire' dans le modal\n";
echo "4. Remplissez le formulaire et cliquez sur 'S'inscrire'\n";
echo "5. Vérifiez que vous recevez un message de succès\n";
?> 