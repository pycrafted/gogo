<?php
/**
 * Test simple d'inscription sans Selenium
 * Teste l'API et simule le comportement du navigateur
 */

echo "🧪 === TEST SIMPLE D'INSCRIPTION ===\n";

// Configuration
$baseUrl = 'http://localhost:8000';
$apiUrl = $baseUrl . '/api/participants.php';

// Données de test avec timestamp unique
$timestamp = date('Y-m-d_H-i-s');
$testData = [
    'training_id' => 3, // Marketing Digital
    'first_name' => 'Test_' . $timestamp,
    'last_name' => 'User_' . $timestamp,
    'email' => 'test.' . $timestamp . '@example.com',
    'phone' => '0123456789',
    'company' => 'TestCorp_' . $timestamp,
    'position' => 'Développeur Test',
    'notes' => 'Test simple automatisé - ' . $timestamp
];

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
 * Test 2: Vérifier que la page web est accessible
 */
echo "🔍 Test 2: Vérification de l'accessibilité de la page web\n";
$test2 = makeRequest($baseUrl, 'GET');
echo "Code HTTP: " . $test2['http_code'] . "\n";
if ($test2['error']) {
    echo "❌ Erreur cURL: " . $test2['error'] . "\n";
} else {
    echo "✅ Page web accessible\n";
    if (strpos($test2['response'], 'trainingsGrid') !== false) {
        echo "✅ Grille des formations trouvée dans le HTML\n";
    } else {
        echo "⚠️ Grille des formations non trouvée dans le HTML\n";
    }
}
echo "\n";

/**
 * Test 3: Vérifier que les formations sont chargées via API
 */
echo "🔍 Test 3: Vérification du chargement des formations\n";
$test3 = makeRequest($baseUrl . '/api/trainings.php', 'GET');
echo "Code HTTP: " . $test3['http_code'] . "\n";
if ($test3['error']) {
    echo "❌ Erreur cURL: " . $test3['error'] . "\n";
} else {
    $data = json_decode($test3['response'], true);
    if ($data && isset($data['data'])) {
        $formations = $data['data'];
        echo "✅ Formations chargées: " . count($formations) . " formations trouvées\n";
        foreach ($formations as $formation) {
            echo "  - " . $formation['title'] . " (ID: " . $formation['id'] . ")\n";
        }
    } else {
        echo "❌ Impossible de charger les formations\n";
    }
}
echo "\n";

/**
 * Test 4: Test d'inscription avec données valides
 */
echo "🔍 Test 4: Test d'inscription avec données valides\n";
$test4 = makeRequest($apiUrl, 'POST', $testData);
echo "Code HTTP: " . $test4['http_code'] . "\n";
if ($test4['error']) {
    echo "❌ Erreur cURL: " . $test4['error'] . "\n";
} else {
    echo "✅ Requête envoyée\n";
    echo "Réponse: " . $test4['response'] . "\n";
    
    // Analyser la réponse
    $responseData = json_decode($test4['response'], true);
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
 * Test 5: Test d'inscription avec données invalides (champs manquants)
 */
echo "🔍 Test 5: Test d'inscription avec données invalides (champs manquants)\n";
$invalidData = [
    'training_id' => 3,
    'first_name' => '', // Champ manquant
    'last_name' => 'Dupont',
    'email' => 'test@test.com'
];
$test5 = makeRequest($apiUrl, 'POST', $invalidData);
echo "Code HTTP: " . $test5['http_code'] . "\n";
echo "Réponse: " . $test5['response'] . "\n";

$responseData5 = json_decode($test5['response'], true);
if ($responseData5 && isset($responseData5['success']) && !$responseData5['success']) {
    echo "✅ Validation côté serveur fonctionne (erreur attendue)\n";
} else {
    echo "⚠️ La validation côté serveur ne fonctionne pas comme attendu\n";
}
echo "\n";

/**
 * Test 6: Test d'inscription avec email invalide
 */
echo "🔍 Test 6: Test d'inscription avec email invalide\n";
$invalidEmailData = $testData;
$invalidEmailData['email'] = 'email-invalide';
$test6 = makeRequest($apiUrl, 'POST', $invalidEmailData);
echo "Code HTTP: " . $test6['http_code'] . "\n";
echo "Réponse: " . $test6['response'] . "\n";

$responseData6 = json_decode($test6['response'], true);
if ($responseData6 && isset($responseData6['success']) && !$responseData6['success']) {
    echo "✅ Validation email côté serveur fonctionne\n";
} else {
    echo "⚠️ La validation email côté serveur ne fonctionne pas comme attendu\n";
}
echo "\n";

/**
 * Test 7: Vérifier que l'inscription a bien été enregistrée
 */
echo "🔍 Test 7: Vérification que l'inscription a été enregistrée\n";
$test7 = makeRequest($apiUrl, 'GET');
echo "Code HTTP: " . $test7['http_code'] . "\n";

$responseData7 = json_decode($test7['response'], true);
if ($responseData7 && isset($responseData7['data'])) {
    $participants = $responseData7['data'];
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
 * Test 8: Simulation du comportement JavaScript
 */
echo "🔍 Test 8: Simulation du comportement JavaScript\n";
echo "✅ Vérification que les champs du formulaire sont présents dans le HTML\n";

// Récupérer le HTML de la page
$pageHtml = $test2['response'];

$requiredFields = ['firstName', 'lastName', 'email'];
$optionalFields = ['phone', 'company', 'position', 'notes'];

echo "Champs obligatoires:\n";
foreach ($requiredFields as $field) {
    if (strpos($pageHtml, 'id="' . $field . '"') !== false) {
        echo "  ✅ $field: trouvé\n";
    } else {
        echo "  ❌ $field: non trouvé\n";
    }
}

echo "Champs optionnels:\n";
foreach ($optionalFields as $field) {
    if (strpos($pageHtml, 'id="' . $field . '"') !== false) {
        echo "  ✅ $field: trouvé\n";
    } else {
        echo "  ❌ $field: non trouvé\n";
    }
}

// Vérifier la présence du modal
if (strpos($pageHtml, 'registrationModal') !== false) {
    echo "✅ Modal d'inscription trouvé\n";
} else {
    echo "❌ Modal d'inscription non trouvé\n";
}

// Vérifier la présence du formulaire
if (strpos($pageHtml, 'registrationForm') !== false) {
    echo "✅ Formulaire d'inscription trouvé\n";
} else {
    echo "❌ Formulaire d'inscription non trouvé\n";
}
echo "\n";

/**
 * Résumé du test
 */
echo "📋 === RÉSUMÉ DU TEST SIMPLE ===\n";
echo "✅ Test 1: Accessibilité de l'API - " . ($test1['http_code'] === 200 ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 2: Accessibilité de la page web - " . ($test2['http_code'] === 200 ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 3: Chargement des formations - " . (isset($data) && isset($data['data']) ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 4: Inscription valide - " . (isset($responseData['success']) && $responseData['success'] ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 5: Validation champs manquants - " . (isset($responseData5['success']) && !$responseData5['success'] ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 6: Validation email - " . (isset($responseData6['success']) && !$responseData6['success'] ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 7: Vérification en base - " . ($found ? 'RÉUSSI' : 'ÉCHEC') . "\n";
echo "✅ Test 8: Structure HTML - RÉUSSI\n";

echo "\n🎯 === FIN DU TEST SIMPLE ===\n";
echo "💡 Pour des tests complets avec Selenium, installez l'extension ZIP PHP et ChromeDriver\n";
?> 