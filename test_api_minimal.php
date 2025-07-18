<?php
/**
 * Test minimal de l'API
 */

echo "🧪 Test minimal de l'API...\n";

// Test 1: GET simple
echo "1. Test GET:\n";
$response = file_get_contents('http://localhost:8000/api/participants.php');
if ($response !== false) {
    echo "   ✅ GET OK\n";
    echo "   📊 Réponse: " . substr($response, 0, 100) . "...\n";
} else {
    echo "   ❌ GET échoue\n";
}

// Test 2: POST simple
echo "\n2. Test POST:\n";
$data = [
    'training_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'Minimal',
    'email' => 'testminimal@example.com'
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($data)
    ]
]);

$response = file_get_contents('http://localhost:8000/api/participants.php', false, $context);
if ($response !== false) {
    echo "   ✅ POST OK\n";
    echo "   📊 Réponse: " . $response . "\n";
} else {
    echo "   ❌ POST échoue\n";
}

echo "\n🎉 Test terminé !\n";
?> 