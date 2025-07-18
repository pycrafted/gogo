<?php
/**
 * Test simple de l'API pour diagnostiquer l'erreur 500
 */

echo "🧪 Test simple de l'API...\n";

// Test 1: Vérifier que l'API répond
echo "1. Test GET de l'API:\n";
try {
    $response = file_get_contents('http://localhost:8000/api/participants.php');
    if ($response !== false) {
        echo "   ✅ GET fonctionne\n";
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            echo "   📊 Réponse: " . json_encode($data) . "\n";
        }
    } else {
        echo "   ❌ GET échoue\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur GET: " . $e->getMessage() . "\n";
}

// Test 2: Test POST avec données minimales
echo "\n2. Test POST avec données minimales:\n";
try {
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'Simple',
        'email' => 'testsimple@example.com'
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($testData)
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/participants.php', false, $context);
    
    if ($response === false) {
        echo "   ❌ POST échoue\n";
    } else {
        echo "   📥 Réponse POST:\n";
        echo "   " . $response . "\n";
        
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ POST fonctionne !\n";
        } else {
            echo "   ❌ Erreur dans la réponse\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Exception POST: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test terminé !\n";
?> 