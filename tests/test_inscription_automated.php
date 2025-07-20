<?php
/**
 * Test automatisé pour l'inscription aux formations
 * Ce test vérifie l'API d'inscription sans avoir besoin d'ouvrir le navigateur
 */

// Configuration
$baseUrl = 'http://localhost:8000';
$apiUrl = $baseUrl . '/api/participants.php';

// Données de test
$testData = [
    'training_id' => 3, // Marketing Digital
    'first_name' => 'Jean',
    'last_name' => 'Dupont',
    'email' => 'jean.dupont@test.com',
    'phone' => '0123456789',
    'company' => 'TestCorp',
    'position' => 'Développeur',
    'notes' => 'Test automatisé - ' . date('Y-m-d H:i:s')
];

echo "🧪 === TEST AUTOMATISÉ D'INSCRIPTION ===\n";
echo "URL de l'API: $apiUrl\n";
echo "Données de test:\n";
foreach ($testData as $key => $value) {
    echo "  - $key: $value\n";
}
echo "\n";

/**
 * Fonction pour faire une requête HTTP
 */
function makeRequest($url, $method = 'GET', $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error
    ];
}

/**
 * Test 1: Vérifier que l'API est accessible
 */
echo "🔍 Test 1: Vérification de l'accessibilité de l'API\n";
$test1 = makeRequest($apiUrl, 'GET');
echo "Code HTTP: " . $test1['http_code'] . "\n";
if ($test1['error']) {
    echo "❌ Erreur cURL: " . $test1['error'] . "\n";
} else {
    echo "✅ API accessible\n";
    echo "Réponse: " . substr($test1['response'], 0, 200) . "...\n";
}
echo "\n";

/**
 * Test 2: Test d'inscription avec données valides
 */
echo "🔍 Test 2: Test d'inscription avec données valides\n";
$test2 = makeRequest($apiUrl, 'POST', $testData);
echo "Code HTTP: " . $test2['http_code'] . "\n";
if ($test2['error']) {
    echo "❌ Erreur cURL: " . $test2['error'] . "\n";
} else {
    echo "✅ Requête envoyée\n";
    echo "Réponse: " . $test2['response'] . "\n";
    
    // Analyser la réponse
    $responseData = json_decode($test2['response'], true);
    if ($responseData) {
        if (isset($responseData['success']) && $responseData['success']) {
            echo "🎉 INSCRIPTION RÉUSSIE!\n";
            if (isset($responseData['data']['id'])) {
                echo "ID du participant: " . $responseData['data']['id'] . "\n";
            }
        } else {
            echo "❌ ÉCHEC DE L'INSCRIPTION\n";
            if (isset($responseData['message'])) {
                echo "Message d'erreur: " . $responseData['message'] . "\n";
            }
        }
    } else {
        echo "⚠️ Réponse non-JSON reçue\n";
    }
}
echo "\n";

/**
 * Test 3: Test d'inscription avec données invalides (champs manquants)
 */
echo "🔍 Test 3: Test d'inscription avec données invalides (champs manquants)\n";
$invalidData = [
    'training_id' => 3,
    'first_name' => '', // Champ manquant
    'last_name' => 'Dupont',
    'email' => 'test@test.com'
];
$test3 = makeRequest($apiUrl, 'POST', $invalidData);
echo "Code HTTP: " . $test3['http_code'] . "\n";
echo "Réponse: " . $test3['response'] . "\n";

$responseData3 = json_decode($test3['response'], true);
if ($responseData3 && isset($responseData3['success']) && !$responseData3['success']) {
    echo "✅ Validation côté serveur fonctionne (erreur attendue)\n";
} else {
    echo "⚠️ La validation côté serveur ne fonctionne pas comme attendu\n";
}
echo "\n";

/**
 * Test 4: Test d'inscription avec email invalide
 */
echo "🔍 Test 4: Test d'inscription avec email invalide\n";
$invalidEmailData = $testData;
$invalidEmailData['email'] = 'email-invalide';
$test4 = makeRequest($apiUrl, 'POST', $invalidEmailData);
echo "Code HTTP: " . $test4['http_code'] . "\n";
echo "Réponse: " . $test4['response'] . "\n";

$responseData4 = json_decode($test4['response'], true);
if ($responseData4 && isset($responseData4['success']) && !$responseData4['success']) {
    echo "✅ Validation email côté serveur fonctionne\n";
} else {
    echo "⚠️ La validation email côté serveur ne fonctionne pas comme attendu\n";
}
echo "\n";

/**
 * Test 5: Vérifier que l'inscription a bien été enregistrée
 */
echo "🔍 Test 5: Vérification que l'inscription a été enregistrée\n";
$test5 = makeRequest($apiUrl, 'GET');
echo "Code HTTP: " . $test5['http_code'] . "\n";

$responseData5 = json_decode($test5['response'], true);
if ($responseData5 && isset($responseData5['data'])) {
    $participants = $responseData5['data'];
    $found = false;
    
    foreach ($participants as $participant) {
        if ($participant['email'] === $testData['email'] && 
            $participant['first_name'] === $testData['first_name'] &&
            $participant['last_name'] === $testData['last_name']) {
            echo "✅ Participant trouvé dans la base de données!\n";
            echo "  - ID: " . $participant['id'] . "\n";
            echo "  - Formation: " . $participant['training_id'] . "\n";
            echo "  - Statut: " . $participant['status'] . "\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "❌ Participant non trouvé dans la base de données\n";
    }
} else {
    echo "⚠️ Impossible de récupérer la liste des participants\n";
}
echo "\n";

/**
 * Résumé du test
 */
echo "📋 === RÉSUMÉ DU TEST ===\n";
echo "✅ Test 1: Accessibilité de l'API - " . ($test1['http_code'] === 200 ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 2: Inscription valide - " . (isset($responseData['success']) && $responseData['success'] ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 3: Validation champs manquants - " . (isset($responseData3['success']) && !$responseData3['success'] ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 4: Validation email - " . (isset($responseData4['success']) && !$responseData4['success'] ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 5: Vérification en base - " . ($found ? 'RÉUSSI' : 'ÉCHEC') . "\n";

echo "\n🎯 === FIN DU TEST AUTOMATISÉ ===\n";
?> 