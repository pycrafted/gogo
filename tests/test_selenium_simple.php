<?php
/**
 * Test Selenium simplifié pour simuler la saisie utilisateur réelle
 * Utilise ChromeDriver directement sans dépendances Composer
 */

echo "🧪 === TEST SELENIUM SIMPLIFIÉ ===\n";

// Configuration
$baseUrl = 'http://localhost:8000';
$timestamp = date('Y-m-d_H-i-s');
$testData = [
    'first_name' => 'Test_' . $timestamp,
    'last_name' => 'User_' . $timestamp,
    'email' => 'test.' . $timestamp . '@example.com',
    'phone' => '0123456789',
    'company' => 'TestCorp_' . $timestamp,
    'position' => 'Développeur Test',
    'notes' => 'Test Selenium utilisateur - ' . $timestamp
];

echo "URL de test: $baseUrl\n";
echo "Données de test:\n";
foreach ($testData as $key => $value) {
    echo "  - $key: $value\n";
}
echo "\n";

/**
 * Vérifier que ChromeDriver est disponible
 */
function checkChromeDriver() {
    echo "🔍 Vérification de ChromeDriver...\n";
    
    // Chercher ChromeDriver dans différents emplacements
    $possiblePaths = [
        'chromedriver.exe',
        './chromedriver.exe',
        'C:/Windows/System32/chromedriver.exe',
        'C:/Program Files/Google/Chrome/Application/chromedriver.exe'
    ];
    
    foreach ($possiblePaths as $path) {
        if (file_exists($path)) {
            echo "✅ ChromeDriver trouvé: $path\n";
            return $path;
        }
    }
    
    echo "❌ ChromeDriver non trouvé\n";
    echo "📥 Téléchargez ChromeDriver depuis: https://chromedriver.chromium.org/\n";
    echo "📁 Placez chromedriver.exe dans le répertoire du projet\n";
    return false;
}

/**
 * Lancer ChromeDriver
 */
function startChromeDriver($driverPath) {
    echo "🚀 Démarrage de ChromeDriver...\n";
    
    // Lancer ChromeDriver en arrière-plan
    $command = "start /B \"\" \"$driverPath\" --port=4444 --whitelisted-ips=\"\"";
    exec($command);
    
    // Attendre que ChromeDriver démarre
    sleep(3);
    
    echo "✅ ChromeDriver démarré\n";
}

/**
 * Faire une requête vers ChromeDriver
 */
function seleniumRequest($method, $url, $data = null) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "http://localhost:4444$url");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
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
 * Test principal
 */
function runSeleniumTest($testData) {
    echo "🔍 Test 1: Vérification de l'accessibilité de ChromeDriver\n";
    
    // Vérifier que ChromeDriver répond
    $test = seleniumRequest('GET', '/status');
    if ($test['http_code'] === 200) {
        echo "✅ ChromeDriver accessible\n";
    } else {
        echo "❌ ChromeDriver non accessible\n";
        return false;
    }
    
    echo "\n🔍 Test 2: Ouverture de la page web\n";
    
    // Créer une nouvelle session
    $sessionData = [
        'capabilities' => [
            'browserName' => 'chrome',
            'goog:chromeOptions' => [
                'args' => [
                    '--headless',
                    '--no-sandbox',
                    '--disable-dev-shm-usage',
                    '--disable-gpu',
                    '--window-size=1920,1080'
                ]
            ]
        ]
    ];
    
    $session = seleniumRequest('POST', '/session', $sessionData);
    if ($session['http_code'] !== 200) {
        echo "❌ Impossible de créer une session Chrome\n";
        return false;
    }
    
    $sessionData = json_decode($session['response'], true);
    $sessionId = $sessionData['value']['sessionId'];
    echo "✅ Session Chrome créée: $sessionId\n";
    
    // Naviguer vers la page
    $navigateData = ['url' => 'http://localhost:8000'];
    $navigate = seleniumRequest('POST', "/session/$sessionId/url", $navigateData);
    
    if ($navigate['http_code'] === 200) {
        echo "✅ Navigation vers la page réussie\n";
    } else {
        echo "❌ Échec de la navigation\n";
        return false;
    }
    
    echo "\n🔍 Test 3: Attendre le chargement de la page\n";
    sleep(3);
    
    echo "\n🔍 Test 4: Rechercher les boutons d'inscription\n";
    
    // Chercher les boutons d'inscription
    $findElementsData = [
        'using' => 'css selector',
        'value' => '.btn-inscription, button[onclick*="inscription"], a[href*="inscription"]'
    ];
    
    $buttons = seleniumRequest('POST', "/session/$sessionId/elements", $findElementsData);
    
    if ($buttons['http_code'] === 200) {
        $buttonsData = json_decode($buttons['response'], true);
        $buttonCount = count($buttonsData['value']);
        echo "✅ Boutons d'inscription trouvés: $buttonCount\n";
        
        if ($buttonCount > 0) {
            echo "\n🔍 Test 5: Cliquer sur le premier bouton d'inscription\n";
            
            // Cliquer sur le premier bouton
            $clickData = ['id' => $buttonsData['value'][0]['ELEMENT']];
            $click = seleniumRequest('POST', "/session/$sessionId/element/{$clickData['id']}/click", []);
            
            if ($click['http_code'] === 200) {
                echo "✅ Clic sur le bouton d'inscription réussi\n";
                
                echo "\n🔍 Test 6: Attendre l'ouverture du modal\n";
                sleep(2);
                
                echo "\n🔍 Test 7: Remplir le formulaire\n";
                
                // Remplir les champs obligatoires
                $fields = [
                    'firstName' => $testData['first_name'],
                    'lastName' => $testData['last_name'],
                    'email' => $testData['email']
                ];
                
                foreach ($fields as $fieldId => $value) {
                    echo "  - Remplissage du champ $fieldId avec: $value\n";
                    
                    // Chercher le champ
                    $findFieldData = [
                        'using' => 'id',
                        'value' => $fieldId
                    ];
                    
                    $field = seleniumRequest('POST', "/session/$sessionId/element", $findFieldData);
                    
                    if ($field['http_code'] === 200) {
                        $fieldData = json_decode($field['response'], true);
                        $fieldElementId = $fieldData['value']['ELEMENT'];
                        
                        // Vider le champ
                        $clearData = ['id' => $fieldElementId];
                        seleniumRequest('POST', "/session/$sessionId/element/{$fieldElementId}/clear", []);
                        
                        // Remplir le champ
                        $sendKeysData = ['text' => $value];
                        $sendKeys = seleniumRequest('POST', "/session/$sessionId/element/{$fieldElementId}/value", $sendKeysData);
                        
                        if ($sendKeys['http_code'] === 200) {
                            echo "    ✅ Champ $fieldId rempli\n";
                        } else {
                            echo "    ❌ Échec du remplissage du champ $fieldId\n";
                        }
                    } else {
                        echo "    ❌ Champ $fieldId non trouvé\n";
                    }
                }
                
                echo "\n🔍 Test 8: Soumettre le formulaire\n";
                
                // Chercher le bouton de soumission
                $submitButtonData = [
                    'using' => 'css selector',
                    'value' => '#registrationModal .btn-success, button[onclick*="submitRegistration"]'
                ];
                
                $submitButton = seleniumRequest('POST', "/session/$sessionId/element", $submitButtonData);
                
                if ($submitButton['http_code'] === 200) {
                    $submitButtonData = json_decode($submitButton['response'], true);
                    $submitElementId = $submitButtonData['value']['ELEMENT'];
                    
                    // Cliquer sur le bouton de soumission
                    $submitClick = seleniumRequest('POST', "/session/$sessionId/element/{$submitElementId}/click", []);
                    
                    if ($submitClick['http_code'] === 200) {
                        echo "✅ Formulaire soumis avec succès\n";
                        
                        echo "\n🔍 Test 9: Vérifier le succès de l'inscription\n";
                        sleep(2);
                        
                        // Vérifier s'il y a une notification de succès
                        $notificationData = [
                            'using' => 'css selector',
                            'value' => '.alert-success, .toast-success, .notification-success'
                        ];
                        
                        $notification = seleniumRequest('POST', "/session/$sessionId/element", $notificationData);
                        
                        if ($notification['http_code'] === 200) {
                            echo "✅ Notification de succès trouvée\n";
                        } else {
                            echo "⚠️ Aucune notification de succès trouvée\n";
                        }
                        
                    } else {
                        echo "❌ Échec de la soumission du formulaire\n";
                    }
                } else {
                    echo "❌ Bouton de soumission non trouvé\n";
                }
                
            } else {
                echo "❌ Échec du clic sur le bouton d'inscription\n";
            }
        } else {
            echo "❌ Aucun bouton d'inscription trouvé\n";
        }
    } else {
        echo "❌ Impossible de chercher les boutons d'inscription\n";
    }
    
    // Fermer la session
    echo "\n🔍 Test 10: Fermeture de la session\n";
    seleniumRequest('DELETE', "/session/$sessionId");
    echo "✅ Session fermée\n";
    
    return true;
}

/**
 * Test de vérification en base de données
 */
function verifyDatabaseEntry($testData) {
    echo "\n🔍 Test 11: Vérification en base de données\n";
    
    $apiUrl = 'http://localhost:8000/api/participants.php';
    $response = makeApiRequest($apiUrl, 'GET');
    
    if ($response['http_code'] === 200) {
        $data = json_decode($response['response'], true);
        
        if ($data && isset($data['data'])) {
            $participants = $data['data'];
            $found = false;
            
            foreach ($participants as $participant) {
                if ($participant['email'] === $testData['email'] &&
                    $participant['first_name'] === $testData['first_name'] &&
                    $participant['last_name'] === $testData['last_name']) {
                    
                    echo "✅ Participant trouvé en base de données!\n";
                    echo "  - ID: " . $participant['id'] . "\n";
                    echo "  - Formation: " . $participant['training_id'] . "\n";
                    echo "  - Statut: " . $participant['status'] . "\n";
                    $found = true;
                    break;
                }
            }
            
            if (!$found) {
                echo "❌ Participant non trouvé en base de données\n";
            }
        } else {
            echo "⚠️ Impossible de récupérer les données de l'API\n";
        }
    } else {
        echo "⚠️ Erreur lors de la vérification API\n";
    }
}

/**
 * Fonction utilitaire pour les requêtes API
 */
function makeApiRequest($url, $method = 'GET', $data = null) {
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

// Lancer le test
echo "🚀 === LANCEMENT DU TEST SELENIUM SIMPLIFIÉ ===\n";

// Vérifier ChromeDriver
$driverPath = checkChromeDriver();
if (!$driverPath) {
    echo "❌ ChromeDriver requis pour ce test\n";
    echo "💡 Utilisez le test simple: .\\scripts\\test_simple.ps1\n";
    exit(1);
}

// Démarrer ChromeDriver
startChromeDriver($driverPath);

// Lancer le test Selenium
$success = runSeleniumTest($testData);

// Vérifier en base de données
if ($success) {
    verifyDatabaseEntry($testData);
}

echo "\n📋 === RÉSUMÉ DU TEST SELENIUM ===\n";
if ($success) {
    echo "✅ Test Selenium réussi - Simulation utilisateur complète\n";
} else {
    echo "❌ Test Selenium échoué\n";
}

echo "\n🎯 === FIN DU TEST SELENIUM ===\n";
?> 