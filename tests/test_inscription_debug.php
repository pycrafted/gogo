<?php
/**
 * Script de test pour déboguer l'inscription
 * Teste tous les aspects de l'inscription avec des logs détaillés
 */

echo "🔍 TEST DE DÉBOGAGE DE L'INSCRIPTION\n";
echo "=====================================\n\n";

// 1. Test de la base de données
echo "1. Vérification de la base de données:\n";
try {
    $pdo = new PDO('sqlite:database/training_catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier les formations
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM trainings");
    $stmt->execute();
    $trainingCount = $stmt->fetchColumn();
    echo "   ✅ Formations dans la base: $trainingCount\n";
    
    if ($trainingCount == 0) {
        echo "   ❌ Aucune formation trouvée - problème de base de données\n";
        exit(1);
    }
    
    // Vérifier la structure de la table participants
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "   📊 Structure de la table participants:\n";
    foreach ($columns as $column) {
        echo "     - {$column['name']} ({$column['type']})\n";
    }
    
    // Vérifier si la colonne position existe
    $hasPosition = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'position') {
            $hasPosition = true;
            break;
        }
    }
    
    if (!$hasPosition) {
        echo "   ⚠️  La colonne 'position' n'existe pas - ajout en cours...\n";
        try {
            $pdo->exec("ALTER TABLE participants ADD COLUMN position VARCHAR(255)");
            echo "   ✅ Colonne 'position' ajoutée\n";
        } catch (Exception $e) {
            echo "   ❌ Erreur lors de l'ajout de la colonne: " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ✅ La colonne 'position' existe\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur base de données: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Test de l'API des formations
echo "\n2. Test de l'API des formations:\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => 'Content-Type: application/json'
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/trainings.php', false, $context);
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à l'API des formations\n";
        echo "   💡 Vérifiez que le serveur PHP est démarré: php -S localhost:8000\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API des formations fonctionnelle\n";
            echo "   📊 Nombre de formations: " . count($data['data']) . "\n";
        } else {
            echo "   ❌ Erreur dans l'API des formations\n";
            echo "   📄 Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur API formations: " . $e->getMessage() . "\n";
}

// 3. Test d'insertion directe d'un participant
echo "\n3. Test d'insertion directe d'un participant:\n";
try {
    $timestamp = time();
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'Debug',
        'email' => "testdebug{$timestamp}@example.com",
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur',
        'notes' => 'Test de débogage'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, notes, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $testData['training_id'],
        $testData['first_name'],
        $testData['last_name'],
        $testData['email'],
        $testData['phone'],
        $testData['company'],
        $testData['position'],
        $testData['notes'],
        'pending'
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "   ✅ Inscription directe réussie, ID: $newId\n";
        
        // Vérifier que le participant a été créé
        $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = ?");
        $stmt->execute([$newId]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($participant) {
            echo "   ✅ Participant trouvé dans la base:\n";
            echo "     - Nom: {$participant['first_name']} {$participant['last_name']}\n";
            echo "     - Email: {$participant['email']}\n";
            echo "     - Formation: {$participant['training_id']}\n";
            echo "     - Statut: {$participant['status']}\n";
        }
        
        // Nettoyer le test
        $pdo->exec("DELETE FROM participants WHERE email = '{$testData['email']}'");
        echo "   ✅ Test nettoyé\n";
    } else {
        echo "   ❌ Échec de l'inscription directe\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur inscription directe: " . $e->getMessage() . "\n";
}

// 4. Test de l'API d'inscription
echo "\n4. Test de l'API d'inscription:\n";
try {
    $timestamp = time();
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'API',
        'email' => "testapi{$timestamp}@example.com",
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur',
        'notes' => 'Test API'
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'content' => json_encode($testData)
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/participants.php', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à l'API des participants\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API d'inscription fonctionnelle\n";
            echo "   📊 Participant créé avec ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
        } else {
            echo "   ❌ Erreur dans l'API d'inscription\n";
            echo "   📄 Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur API inscription: " . $e->getMessage() . "\n";
}

// 5. Vérification des logs
echo "\n5. Vérification des logs:\n";
$logFile = ini_get('error_log');
if ($logFile && file_exists($logFile)) {
    echo "   📄 Fichier de log: $logFile\n";
    
    // Lire les dernières lignes du log
    $lines = file($logFile);
    $recentLines = array_slice($lines, -20); // 20 dernières lignes
    
    echo "   📋 Dernières lignes du log:\n";
    foreach ($recentLines as $line) {
        if (strpos($line, 'PARTICIPANT') !== false || strpos($line, 'HELPERS') !== false) {
            echo "     " . trim($line) . "\n";
        }
    }
} else {
    echo "   ⚠️  Fichier de log non trouvé\n";
    echo "   💡 Vérifiez la configuration error_log dans php.ini\n";
}

echo "\n🎯 RÉSUMÉ DU DÉBOGAGE:\n";
echo "=====================\n";
echo "✅ Base de données: OK\n";
echo "✅ Structure des tables: OK\n";
echo "⚠️  API formations: À vérifier (démarrez le serveur PHP)\n";
echo "⚠️  API inscription: À vérifier (démarrez le serveur PHP)\n";
echo "📋 Logs: Vérifiez le fichier error_log pour les détails\n\n";

echo "💡 PROCHAINES ÉTAPES:\n";
echo "1. Démarrez le serveur PHP: php -S localhost:8000\n";
echo "2. Ouvrez http://localhost:8000 dans votre navigateur\n";
echo "3. Essayez de vous inscrire à une formation\n";
echo "4. Vérifiez les logs pour identifier le problème\n";
echo "5. Consultez la console du navigateur (F12) pour les erreurs JavaScript\n";
?> 