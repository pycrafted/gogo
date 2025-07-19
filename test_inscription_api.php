<?php
/**
 * Test de l'API d'inscription des participants
 */

// Configuration
$apiUrl = 'http://localhost:8000/api/participants.php';
$testData = [
    'training_id' => 1,
    'first_name' => 'Jean',
    'last_name' => 'Dupont',
    'email' => 'jean.dupont@test.com',
    'phone' => '0123456789',
    'company' => 'TestCorp',
    'position' => 'Développeur',
    'notes' => 'Test d\'inscription via API'
];

echo "=== Test de l'API d'inscription ===\n";
echo "URL: $apiUrl\n";
echo "Données de test: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

// Test avec cURL
function testWithCurl($url, $data) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'X-Requested-With: XMLHttpRequest'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    curl_close($ch);
    
    return [
        'response' => $response,
        'http_code' => $httpCode,
        'error' => $error
    ];
}

// Test avec file_get_contents
function testWithFileGetContents($url, $data) {
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'X-Requested-With: XMLHttpRequest'
            ],
            'content' => json_encode($data)
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    $httpCode = $http_response_header[0] ?? 'Unknown';
    
    return [
        'response' => $response,
        'http_code' => $httpCode,
        'error' => $response === false ? 'Failed to get response' : null
    ];
}

// Test 1: cURL
echo "--- Test avec cURL ---\n";
$result1 = testWithCurl($apiUrl, $testData);
echo "Code HTTP: " . $result1['http_code'] . "\n";
if ($result1['error']) {
    echo "Erreur cURL: " . $result1['error'] . "\n";
} else {
    echo "Réponse: " . $result1['response'] . "\n";
}
echo "\n";

// Test 2: file_get_contents
echo "--- Test avec file_get_contents ---\n";
$result2 = testWithFileGetContents($apiUrl, $testData);
echo "Code HTTP: " . $result2['http_code'] . "\n";
if ($result2['error']) {
    echo "Erreur: " . $result2['error'] . "\n";
} else {
    echo "Réponse: " . $result2['response'] . "\n";
}
echo "\n";

// Test 3: Vérification de la base de données
echo "--- Vérification de la base de données ---\n";
try {
    $pdo = new PDO('sqlite:database/training_catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier les formations
    $stmt = $pdo->query("SELECT COUNT(*) FROM trainings");
    $trainingCount = $stmt->fetchColumn();
    echo "Nombre de formations dans la base: $trainingCount\n";
    
    // Vérifier les participants
    $stmt = $pdo->query("SELECT COUNT(*) FROM participants");
    $participantCount = $stmt->fetchColumn();
    echo "Nombre de participants dans la base: $participantCount\n";
    
    // Afficher les derniers participants
    $stmt = $pdo->query("SELECT * FROM participants ORDER BY registration_date DESC LIMIT 5");
    $recentParticipants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if ($recentParticipants) {
        echo "Derniers participants:\n";
        foreach ($recentParticipants as $participant) {
            echo "- " . $participant['first_name'] . " " . $participant['last_name'] . 
                 " (" . $participant['email'] . ") - Formation ID: " . $participant['training_id'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Erreur lors de la vérification de la base: " . $e->getMessage() . "\n";
}

echo "\n=== Fin du test ===\n";
?> 