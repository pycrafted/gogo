<?php
/**
 * Script de test de l'API des participants
 */

echo "🧪 Test de l'API des participants...\n";

// Test 1: Vérifier les formations disponibles
echo "\n1. Vérification des formations disponibles:\n";
try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("SELECT id, title, domain FROM trainings ORDER BY id LIMIT 5");
    $stmt->execute();
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($trainings as $training) {
        echo "  - ID {$training['id']}: {$training['title']} ({$training['domain']})\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

// Test 2: Test d'insertion d'un participant
echo "\n2. Test d'insertion d'un participant:\n";
try {
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $testData['training_id'],
        $testData['first_name'],
        $testData['last_name'],
        $testData['email'],
        $testData['phone'],
        $testData['company'],
        $testData['position'],
        'pending'
    ]);
    
    $newId = $pdo->lastInsertId();
    echo "✅ Participant créé avec l'ID: $newId\n";
    
    // Vérifier que le participant a été créé
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = ?");
    $stmt->execute([$newId]);
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participant) {
        echo "✅ Participant trouvé dans la base:\n";
        echo "  - Nom: {$participant['first_name']} {$participant['last_name']}\n";
        echo "  - Email: {$participant['email']}\n";
        echo "  - Position: {$participant['position']}\n";
    }
    
    // Supprimer le participant de test
    $pdo->exec("DELETE FROM participants WHERE id = $newId");
    echo "✅ Participant de test supprimé\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test d'insertion: " . $e->getMessage() . "\n";
}

echo "\n🎉 Tests terminés !\n";
?> 