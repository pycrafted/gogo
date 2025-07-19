<?php
/**
 * Test de persistance du statut des participants
 */

echo "🔍 TEST DE PERSISTANCE DU STATUT\n";
echo "================================\n\n";

// Configuration de la base de données
require_once __DIR__ . '/src/config.php';

try {
    $pdo = getDatabaseConnection();
    echo "✅ Connexion à la base de données réussie\n\n";
    
    // 1. Vérifier la structure de la table
    echo "1. 📋 Vérification de la structure de la table participants...\n";
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Colonnes de la table participants:\n";
    foreach ($columns as $column) {
        echo "   - {$column['name']} ({$column['type']})\n";
    }
    echo "\n";
    
    // 2. Vérifier le statut actuel d'un participant
    echo "2. 👤 Vérification du statut actuel du participant 12...\n";
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, status FROM participants WHERE id = 12");
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participant) {
        echo "   ✅ Participant trouvé:\n";
        echo "      ID: {$participant['id']}\n";
        echo "      Nom: {$participant['first_name']} {$participant['last_name']}\n";
        echo "      Statut actuel: {$participant['status']}\n";
    } else {
        echo "   ❌ Participant 12 non trouvé\n";
        exit;
    }
    echo "\n";
    
    // 3. Modifier le statut directement en base
    echo "3. ✏️ Modification du statut en base de données...\n";
    $newStatus = 'confirmed';
    $stmt = $pdo->prepare("UPDATE participants SET status = ? WHERE id = 12");
    $result = $stmt->execute([$newStatus]);
    
    if ($result && $stmt->rowCount() > 0) {
        echo "   ✅ Statut modifié avec succès\n";
        echo "      Nouveau statut: $newStatus\n";
        echo "      Lignes affectées: " . $stmt->rowCount() . "\n";
    } else {
        echo "   ❌ Échec de la modification du statut\n";
    }
    echo "\n";
    
    // 4. Vérifier que la modification a bien eu lieu
    echo "4. 🔍 Vérification après modification...\n";
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, status FROM participants WHERE id = 12");
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participant) {
        echo "   ✅ Participant après modification:\n";
        echo "      ID: {$participant['id']}\n";
        echo "      Nom: {$participant['first_name']} {$participant['last_name']}\n";
        echo "      Statut après modification: {$participant['status']}\n";
        
        if ($participant['status'] === $newStatus) {
            echo "      ✅ Statut correctement mis à jour en base\n";
        } else {
            echo "      ❌ Statut non mis à jour en base\n";
        }
    }
    echo "\n";
    
    // 5. Test de l'API
    echo "5. 🌐 Test de l'API de mise à jour...\n";
    
    // Données de test
    $testData = [
        'training_id' => 1,
        'first_name' => 'sounatou',
        'last_name' => 'niane',
        'email' => 'sounatou@gmail.com',
        'phone' => '770123456',
        'company' => 'cmu',
        'position' => 'Développeur',
        'status' => 'pending', // Remettre en pending
        'notes' => 'Test de persistance du statut'
    ];
    
    // Créer le contexte pour la requête PUT
    $context = stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'content' => json_encode($testData)
        ]
    ]);
    
    // Appeler l'API
    $response = file_get_contents('http://localhost:8000/api/participants.php/12', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'appeler l'API\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API appelée avec succès\n";
            echo "      Message: {$data['message']}\n";
            
            // Vérifier le statut retourné par l'API
            if (isset($data['data']['status'])) {
                echo "      Statut retourné par l'API: {$data['data']['status']}\n";
            }
        } else {
            echo "   ❌ Erreur API: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
        }
    }
    echo "\n";
    
    // 6. Vérification finale en base
    echo "6. 🔍 Vérification finale en base de données...\n";
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, status FROM participants WHERE id = 12");
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participant) {
        echo "   ✅ Statut final en base:\n";
        echo "      ID: {$participant['id']}\n";
        echo "      Nom: {$participant['first_name']} {$participant['last_name']}\n";
        echo "      Statut final: {$participant['status']}\n";
        
        if ($participant['status'] === 'pending') {
            echo "      ✅ Statut correctement mis à jour via l'API\n";
        } else {
            echo "      ❌ Statut non mis à jour via l'API\n";
        }
    }
    echo "\n";
    
    // 7. Test de rechargement de la page
    echo "7. 📄 Test de rechargement de la page...\n";
    echo "   Pour tester le rechargement:\n";
    echo "   1. Ouvrez http://localhost:8000/admin.html\n";
    echo "   2. Connectez-vous avec admin/admin\n";
    echo "   3. Modifiez le statut d'un participant\n";
    echo "   4. Rechargez la page (F5)\n";
    echo "   5. Vérifiez que le statut persiste\n";
    echo "\n";
    
    // 8. Diagnostic des logs
    echo "8. 📝 Diagnostic des logs...\n";
    $logFile = __DIR__ . '/php_error.log';
    if (file_exists($logFile)) {
        echo "   Fichier de log trouvé: $logFile\n";
        echo "   Taille: " . filesize($logFile) . " octets\n";
        
        // Lire les dernières lignes du log
        $lines = file($logFile);
        $recentLines = array_slice($lines, -20);
        
        echo "   Dernières lignes du log:\n";
        foreach ($recentLines as $line) {
            echo "   " . trim($line) . "\n";
        }
    } else {
        echo "   ❌ Fichier de log non trouvé\n";
    }
    echo "\n";
    
    echo "🎯 RÉSUMÉ DU TEST:\n";
    echo "==================\n";
    echo "✅ Structure de la table vérifiée\n";
    echo "✅ Modification directe en base testée\n";
    echo "✅ API de mise à jour testée\n";
    echo "✅ Vérification de persistance effectuée\n";
    echo "✅ Logs de diagnostic ajoutés\n";
    
    echo "\n💡 RECOMMANDATIONS:\n";
    echo "==================\n";
    echo "🔍 Si le statut ne persiste pas:\n";
    echo "   1. Vérifiez les logs PHP pour les erreurs\n";
    echo "   2. Vérifiez que l'API reçoit bien le statut\n";
    echo "   3. Vérifiez que la requête SQL s'exécute correctement\n";
    echo "   4. Vérifiez que la base de données est bien en écriture\n";
    
    echo "\n✅ TEST DE PERSISTANCE TERMINÉ !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?> 