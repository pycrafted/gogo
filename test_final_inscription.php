<?php
/**
 * Test final de l'inscription
 */

echo "🎯 Test final de l'inscription...\n\n";

// Test 1: Vérifier que la base de données a des formations
echo "1. Vérification des formations:\n";
try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM trainings");
    $stmt->execute();
    $trainingCount = $stmt->fetchColumn();
    echo "   ✅ Formations dans la base: $trainingCount\n";
    
    if ($trainingCount > 0) {
        $stmt = $pdo->prepare("SELECT id, title FROM trainings ORDER BY id LIMIT 3");
        $stmt->execute();
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   📋 Formations disponibles:\n";
        foreach ($trainings as $training) {
            echo "     - ID: {$training['id']} - {$training['title']}\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur base de données: " . $e->getMessage() . "\n";
}

// Test 2: Test d'inscription directe
echo "\n2. Test d'inscription directe:\n";
try {
    $timestamp = time();
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'Final',
        'email' => "testfinal{$timestamp}@example.com",
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur',
        'notes' => 'Test final'
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
    } else {
        echo "   ❌ Échec de l'inscription directe\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur inscription directe: " . $e->getMessage() . "\n";
}

// Test 3: Test de l'API via HTTP
echo "\n3. Test de l'API HTTP:\n";
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
        'notes' => 'Test API HTTP'
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
        echo "   ❌ Impossible d'accéder à l'API\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API HTTP fonctionnelle\n";
            echo "   📊 Participant créé avec succès\n";
        } else {
            echo "   ❌ Erreur dans l'API HTTP\n";
            if ($data) {
                echo "   📋 Réponse: " . json_encode($data) . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur API HTTP: " . $e->getMessage() . "\n";
}

// Test 4: Vérifier le nombre total de participants
echo "\n4. Vérification finale:\n";
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants");
    $stmt->execute();
    $participantCount = $stmt->fetchColumn();
    echo "   📊 Nombre total de participants: $participantCount\n";
    
    if ($participantCount > 0) {
        $stmt = $pdo->prepare("SELECT * FROM participants ORDER BY id DESC LIMIT 3");
        $stmt->execute();
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "   📋 Derniers participants:\n";
        foreach ($participants as $participant) {
            echo "     - ID: {$participant['id']} - {$participant['first_name']} {$participant['last_name']} ({$participant['email']})\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur vérification: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test final terminé !\n";
echo "\n📋 Instructions pour tester l'inscription:\n";
echo "1. Ouvrez http://localhost:8000/index.html dans votre navigateur\n";
echo "2. Cliquez sur '📖' pour voir le programme d'une formation\n";
echo "3. Cliquez sur 'S'inscrire' dans le modal\n";
echo "4. Remplissez le formulaire et cliquez sur 'S'inscrire'\n";
echo "5. Vérifiez que vous recevez un message de succès\n";
echo "\n🔧 Interface d'administration: http://localhost:8000/admin.html\n";
?> 