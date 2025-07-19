<?php
/**
 * Test final de l'inscription sans warnings
 */

echo "🎯 TEST FINAL DE L'INSCRIPTION\n";
echo "==============================\n\n";

// 1. Test de l'API sans warnings
echo "1. 🧪 Test de l'API d'inscription (sans warnings)...\n";
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
        'notes' => 'Test final sans warnings'
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
        echo "   ❌ Impossible d'accéder à l'API\n";
        echo "   💡 Démarrez le serveur: php -S localhost:8000 -t public\n";
    } else {
        echo "   📄 Réponse brute reçue:\n";
        echo "   " . str_repeat("-", 50) . "\n";
        echo "   " . $response . "\n";
        echo "   " . str_repeat("-", 50) . "\n";
        
        // Vérifier s'il y a des warnings PHP
        if (strpos($response, '<br />') !== false || strpos($response, '<b>Warning</b>') !== false) {
            echo "   ⚠️  Warnings PHP détectés dans la réponse\n";
        } else {
            echo "   ✅ Aucun warning PHP détecté\n";
        }
        
        // Essayer de parser le JSON
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API d'inscription fonctionnelle\n";
            echo "   📊 Participant créé avec ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
            echo "   📧 Email: " . ($data['data']['email'] ?? 'N/A') . "\n";
            echo "   📝 Message: " . ($data['message'] ?? 'N/A') . "\n";
        } else {
            echo "   ❌ Erreur dans l'API d'inscription\n";
            echo "   📄 Réponse JSON: " . json_last_error_msg() . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test API: " . $e->getMessage() . "\n";
}

// 2. Test de la base de données
echo "\n2. 🗄️  Vérification de la base de données...\n";
try {
    $pdo = new PDO('sqlite:database/training_catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier les participants
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants");
    $stmt->execute();
    $participantCount = $stmt->fetchColumn();
    echo "   📊 Nombre de participants: $participantCount\n";
    
    // Afficher les derniers participants
    $stmt = $pdo->prepare("
        SELECT p.*, t.title as training_title 
        FROM participants p 
        LEFT JOIN trainings t ON p.training_id = t.id 
        ORDER BY p.id DESC 
        LIMIT 3
    ");
    $stmt->execute();
    $recentParticipants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   📋 Derniers participants:\n";
    foreach ($recentParticipants as $participant) {
        echo "     - ID: {$participant['id']} | {$participant['first_name']} {$participant['last_name']} | {$participant['email']} | {$participant['training_title']}\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur base de données: " . $e->getMessage() . "\n";
}

// 3. Test de l'interface web
echo "\n3. 🌐 Test de l'interface web...\n";
echo "   💡 Ouvrez http://localhost:8000 dans votre navigateur\n";
echo "   💡 Essayez de vous inscrire à une formation\n";
echo "   💡 Vérifiez la console du navigateur (F12)\n";

echo "\n🎯 RÉSUMÉ DU TEST:\n";
echo "==================\n";
echo "✅ API testée\n";
echo "✅ Base de données vérifiée\n";
echo "⚠️  Interface web à tester manuellement\n";

echo "\n💡 PROCHAINES ÉTAPES:\n";
echo "1. Ouvrez http://localhost:8000\n";
echo "2. Cliquez sur 'S\'inscrire' pour une formation\n";
echo "3. Remplissez le formulaire\n";
echo "4. Vérifiez que l'inscription fonctionne\n";
echo "5. Vérifiez qu'il n'y a plus d'erreurs dans la console\n";

echo "\n🔧 Si des problèmes persistent:\n";
echo "1. Vérifiez les logs PHP\n";
echo "2. Vérifiez la console du navigateur\n";
echo "3. Relancez: php fix_database_structure.php\n";
?> 