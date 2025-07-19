<?php
/**
 * Test de debug de l'API
 */

echo "🔍 Debug de l'API...\n";

// Test 1: Vérifier que le serveur répond
echo "1. Test de connexion:\n";
$response = file_get_contents('http://localhost:8000/api/trainings.php');
if ($response !== false) {
    echo "   ✅ Serveur accessible\n";
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "   📊 API trainings fonctionne\n";
    }
} else {
    echo "   ❌ Serveur inaccessible\n";
    exit;
}

// Test 2: Test GET participants
echo "\n2. Test GET participants:\n";
$response = file_get_contents('http://localhost:8000/api/participants.php');
if ($response !== false) {
    echo "   ✅ GET participants fonctionne\n";
    $data = json_decode($response, true);
    if ($data && isset($data['success'])) {
        echo "   📊 Réponse: " . json_encode($data) . "\n";
    }
} else {
    echo "   ❌ GET participants échoue\n";
}

// Test 3: Test POST simple
echo "\n3. Test POST simple:\n";
$testData = [
    'training_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'Debug',
    'email' => 'testdebug@example.com'
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($testData)
    ]
]);

$response = file_get_contents('http://localhost:8000/api/participants.php', false, $context);
if ($response !== false) {
    echo "   ✅ POST fonctionne\n";
    echo "   📊 Réponse: " . $response . "\n";
} else {
    echo "   ❌ POST échoue\n";
}

echo "\n🎉 Debug terminé !\n";
?> 