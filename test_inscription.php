<?php
/**
 * Test complet de l'inscription
 */

echo "🧪 Test complet de l'inscription...\n\n";

// Test 1: Vérifier la base de données
echo "1. Vérification de la base de données:\n";
try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier les formations
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM trainings");
    $stmt->execute();
    $trainingCount = $stmt->fetchColumn();
    echo "   ✅ Formations dans la base: $trainingCount\n";
    
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
        echo "   ❌ PROBLÈME: La colonne 'position' n'existe pas!\n";
    } else {
        echo "   ✅ La colonne 'position' existe\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur base de données: " . $e->getMessage() . "\n";
}

// Test 2: Vérifier l'API des formations
echo "\n2. Test de l'API des formations:\n";
try {
    $response = file_get_contents('http://localhost:8000/api/trainings.php');
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à l'API des formations\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API des formations fonctionnelle\n";
            echo "   📊 Nombre de formations: " . count($data['data']) . "\n";
        } else {
            echo "   ❌ Erreur dans l'API des formations\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur API formations: " . $e->getMessage() . "\n";
}

// Test 3: Test d'insertion directe d'un participant
echo "\n3. Test d'insertion directe d'un participant:\n";
try {
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur',
        'notes' => 'Test d\'inscription'
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
        echo "   ✅ Insertion directe réussie, ID: $newId\n";
        
        // Vérifier que le participant a été créé
        $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = ?");
        $stmt->execute([$newId]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($participant) {
            echo "   ✅ Participant trouvé dans la base:\n";
            echo "     - Nom: {$participant['first_name']} {$participant['last_name']}\n";
            echo "     - Email: {$participant['email']}\n";
            echo "     - Position: {$participant['position']}\n";
        }
        
        // Supprimer le test
        $pdo->exec("DELETE FROM participants WHERE id = $newId");
        echo "   ✅ Test supprimé\n";
    } else {
        echo "   ❌ Échec de l'insertion directe\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur insertion directe: " . $e->getMessage() . "\n";
}

// Test 4: Test de l'API des participants
echo "\n4. Test de l'API des participants:\n";
try {
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'API',
        'email' => 'testapi@example.com',
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur',
        'notes' => 'Test API'
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
        echo "   ❌ Impossible d'accéder à l'API des participants\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API des participants fonctionnelle\n";
            echo "   📊 Participant créé avec succès\n";
        } else {
            echo "   ❌ Erreur dans l'API des participants\n";
            if ($data) {
                echo "   📋 Réponse: " . json_encode($data) . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur API participants: " . $e->getMessage() . "\n";
}

// Test 5: Vérifier les logs du serveur
echo "\n5. Vérification des logs:\n";
echo "   ℹ️  Vérifiez les logs du serveur PHP pour voir les erreurs détaillées\n";
echo "   ℹ️  Command: php -S localhost:8000 -t public\n";

echo "\n🎉 Tests terminés !\n";
?> 