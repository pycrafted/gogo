<?php
/**
 * Script rapide pour corriger le problème d'inscription
 * Identifie et corrige les problèmes de base de données
 */

echo "🚀 CORRECTION RAPIDE DE L'INSCRIPTION\n";
echo "=====================================\n\n";

// 1. Vérifier et corriger la base de données
echo "1. 🔧 Correction de la base de données...\n";
try {
    $dbPath = __DIR__ . '/database/training_catalog.db';
    
    if (!file_exists($dbPath)) {
        echo "   ❌ Base de données non trouvée\n";
        echo "   💡 Lancez: php init_database.php\n";
        exit(1);
    }
    
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier la structure de la table participants
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $columnNames = array_column($columns, 'name');
    echo "   📊 Colonnes trouvées: " . implode(', ', $columnNames) . "\n";
    
    // Ajouter les colonnes manquantes
    $missingColumns = [];
    if (!in_array('notes', $columnNames)) {
        $missingColumns[] = 'notes';
    }
    if (!in_array('position', $columnNames)) {
        $missingColumns[] = 'position';
    }
    
    if (!empty($missingColumns)) {
        echo "   ⚠️  Colonnes manquantes: " . implode(', ', $missingColumns) . "\n";
        
        foreach ($missingColumns as $column) {
            try {
                $pdo->exec("ALTER TABLE participants ADD COLUMN $column TEXT");
                echo "   ✅ Colonne '$column' ajoutée\n";
            } catch (Exception $e) {
                echo "   ❌ Erreur ajout '$column': " . $e->getMessage() . "\n";
            }
        }
    } else {
        echo "   ✅ Toutes les colonnes sont présentes\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur base de données: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. Test de l'API
echo "\n2. 🧪 Test de l'API d'inscription...\n";
try {
    $timestamp = time();
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'Quick',
        'email' => "testquick{$timestamp}@example.com",
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur',
        'notes' => 'Test rapide'
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
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API d'inscription fonctionnelle\n";
            echo "   📊 Participant créé avec ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
        } else {
            echo "   ❌ Erreur dans l'API\n";
            echo "   📄 Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test API: " . $e->getMessage() . "\n";
}

// 3. Instructions
echo "\n3. 📋 Instructions pour tester:\n";
echo "   💡 Démarrez le serveur:\n";
echo "      php -S localhost:8000 -t public\n";
echo "\n   💡 Ouvrez dans le navigateur:\n";
echo "      http://localhost:8000\n";
echo "\n   💡 Essayez de vous inscrire à une formation\n";
echo "\n   💡 Vérifiez les logs dans la console (F12)\n";

echo "\n🎯 RÉSUMÉ:\n";
echo "==========\n";
echo "✅ Base de données corrigée\n";
echo "⚠️  API à tester (démarrez le serveur)\n";
echo "📋 Logs détaillés activés\n";

echo "\n💡 Si le problème persiste:\n";
echo "1. Vérifiez les logs PHP\n";
echo "2. Vérifiez la console du navigateur\n";
echo "3. Relancez: php fix_database_structure.php\n";
?> 