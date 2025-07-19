<?php
/**
 * Test rapide de l'inscription après correction
 */

echo "🧪 Test de l'inscription après correction...\n";

// Test d'inscription directe
try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $timestamp = time();
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'Fix',
        'email' => "testfix{$timestamp}@example.com",
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur',
        'notes' => 'Test après correction'
    ];
    
    // Vérifier la structure de la table
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasPosition = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'position') {
            $hasPosition = true;
            break;
        }
    }
    
    echo "📊 Structure de la table:\n";
    foreach ($columns as $column) {
        echo "   - {$column['name']} ({$column['type']})\n";
    }
    
    if ($hasPosition) {
        $stmt = $pdo->prepare("
            INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, notes, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $params = [
            $testData['training_id'],
            $testData['first_name'],
            $testData['last_name'],
            $testData['email'],
            $testData['phone'],
            $testData['company'],
            $testData['position'],
            $testData['notes'],
            'pending'
        ];
    } else {
        $stmt = $pdo->prepare("
            INSERT INTO participants (training_id, first_name, last_name, email, phone, company, notes, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $params = [
            $testData['training_id'],
            $testData['first_name'],
            $testData['last_name'],
            $testData['email'],
            $testData['phone'],
            $testData['company'],
            $testData['notes'],
            'pending'
        ];
    }
    
    $result = $stmt->execute($params);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "✅ Inscription réussie ! ID: $newId\n";
        echo "📧 Email: {$testData['email']}\n";
    } else {
        echo "❌ Échec de l'inscription\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🎉 Test terminé !\n";
?> 