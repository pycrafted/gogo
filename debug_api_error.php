<?php
/**
 * Diagnostic de l'erreur 500 de l'API
 */

echo "🔍 Diagnostic de l'erreur 500...\n\n";

// Test 1: Vérifier si le serveur répond
echo "1. Test de connexion au serveur:\n";
try {
    $response = file_get_contents('http://localhost:8000/api/trainings.php');
    if ($response !== false) {
        echo "   ✅ Serveur accessible\n";
    } else {
        echo "   ❌ Serveur inaccessible\n";
        exit;
    }
} catch (Exception $e) {
    echo "   ❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Vérifier l'API des participants avec GET
echo "\n2. Test GET de l'API des participants:\n";
try {
    $response = file_get_contents('http://localhost:8000/api/participants.php');
    if ($response !== false) {
        $data = json_decode($response, true);
        if ($data) {
            echo "   ✅ GET fonctionne\n";
            echo "   📊 Réponse: " . substr(json_encode($data), 0, 200) . "...\n";
        } else {
            echo "   ❌ Réponse JSON invalide\n";
        }
    } else {
        echo "   ❌ GET échoue\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur GET: " . $e->getMessage() . "\n";
}

// Test 3: Test POST simple
echo "\n3. Test POST simple:\n";
try {
    $testData = ['test' => 'data'];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => json_encode($testData)
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/participants.php', false, $context);
    
    if ($response !== false) {
        echo "   📊 Réponse POST: " . substr($response, 0, 500) . "\n";
    } else {
        echo "   ❌ POST échoue complètement\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur POST: " . $e->getMessage() . "\n";
}

// Test 4: Vérifier les fichiers PHP
echo "\n4. Vérification des fichiers PHP:\n";

$files = [
    'public/api/participants.php',
    'src/controllers/ParticipantController.php',
    'src/models/Participant.php',
    'src/config.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        echo "   ✅ $file existe\n";
    } else {
        echo "   ❌ $file manquant\n";
    }
}

// Test 5: Vérifier la syntaxe PHP
echo "\n5. Vérification de la syntaxe PHP:\n";
foreach ($files as $file) {
    if (file_exists($file)) {
        $output = shell_exec("php -l $file 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "   ✅ $file - Syntaxe OK\n";
        } else {
            echo "   ❌ $file - Erreur de syntaxe:\n";
            echo "      $output\n";
        }
    }
}

echo "\n🎉 Diagnostic terminé !\n";
?> 