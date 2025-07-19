<?php
/**
 * Script pour vérifier rapidement l'endpoint d'inscription
 */

echo "🔍 VÉRIFICATION RAPIDE DE L'ENDPOINT\n";
echo "====================================\n\n";

// 1. Vérifier si le serveur est démarré
echo "1. 🌐 Test de connexion au serveur...\n";
try {
    $response = file_get_contents('http://localhost:8000/api/trainings.php');
    if ($response === false) {
        echo "   ❌ Serveur non accessible\n";
        echo "   💡 Démarrez le serveur: php -S localhost:8000 -t public\n";
        exit(1);
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ Serveur accessible\n";
            echo "   📊 Formations disponibles: " . count($data['data']) . "\n";
        } else {
            echo "   ⚠️  Serveur accessible mais API en erreur\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur de connexion: " . $e->getMessage() . "\n";
    echo "   💡 Démarrez le serveur: php -S localhost:8000 -t public\n";
    exit(1);
}

// 2. Test de l'endpoint participants
echo "\n2. 🧪 Test de l'endpoint participants...\n";
try {
    $timestamp = time();
    $testData = [
        'training_id' => 1,
        'first_name' => 'Test',
        'last_name' => 'Endpoint',
        'email' => "testendpoint{$timestamp}@example.com",
        'phone' => '0123456789',
        'company' => 'TestCorp',
        'position' => 'Développeur',
        'notes' => 'Test endpoint'
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
        echo "   ❌ Impossible d'accéder à l'endpoint participants\n";
        echo "   📄 Vérifiez que le fichier public/api/participants.php existe\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ Endpoint participants fonctionnel\n";
            echo "   📊 Participant créé avec ID: " . ($data['data']['id'] ?? 'N/A') . "\n";
        } else {
            echo "   ❌ Erreur dans l'endpoint participants\n";
            echo "   📄 Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test endpoint: " . $e->getMessage() . "\n";
}

// 3. Vérifier les fichiers
echo "\n3. 📁 Vérification des fichiers...\n";
$files = [
    'public/api/participants.php' => 'Endpoint participants',
    'src/controllers/ParticipantController.php' => 'Contrôleur participants',
    'src/models/Participant.php' => 'Modèle participant',
    'src/config.php' => 'Configuration',
    'database/training_catalog.db' => 'Base de données'
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        echo "   ✅ $description: $file\n";
    } else {
        echo "   ❌ $description: $file (MANQUANT)\n";
    }
}

// 4. Vérifier la structure de la base de données
echo "\n4. 🗄️  Vérification de la base de données...\n";
try {
    $pdo = new PDO('sqlite:database/training_catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier les tables
    $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table'");
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "   📋 Tables: " . implode(', ', $tables) . "\n";
    
    // Vérifier la structure de participants
    if (in_array('participants', $tables)) {
        $stmt = $pdo->prepare("PRAGMA table_info(participants)");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $columnNames = array_column($columns, 'name');
        $requiredColumns = ['id', 'training_id', 'first_name', 'last_name', 'email', 'phone', 'company', 'position', 'notes', 'status', 'registration_date'];
        
        $missingColumns = array_diff($requiredColumns, $columnNames);
        if (empty($missingColumns)) {
            echo "   ✅ Structure de la table participants OK\n";
        } else {
            echo "   ⚠️  Colonnes manquantes: " . implode(', ', $missingColumns) . "\n";
            echo "   💡 Lancez: php fix_database_structure.php\n";
        }
    } else {
        echo "   ❌ Table participants manquante\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur base de données: " . $e->getMessage() . "\n";
}

echo "\n🎯 RÉSUMÉ:\n";
echo "==========\n";
echo "✅ Serveur accessible\n";
echo "⚠️  Endpoint à tester\n";
echo "📁 Fichiers vérifiés\n";
echo "🗄️  Base de données vérifiée\n";

echo "\n💡 PROCHAINES ÉTAPES:\n";
echo "1. Si l'endpoint ne fonctionne pas, vérifiez les logs\n";
echo "2. Relancez: php fix_database_structure.php\n";
echo "3. Vérifiez la console du navigateur (F12)\n";
?> 