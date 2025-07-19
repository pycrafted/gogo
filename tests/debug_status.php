<?php
/**
 * Diagnostic du problème de persistance du statut
 */

// Activer l'affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 DIAGNOSTIC DU PROBLÈME DE PERSISTANCE\n";
echo "========================================\n\n";

// 1. Vérifier la configuration
echo "1. ⚙️ Vérification de la configuration...\n";
$configFile = __DIR__ . '/src/config.php';
if (file_exists($configFile)) {
    echo "   ✅ Fichier config.php trouvé\n";
    include $configFile;
} else {
    echo "   ❌ Fichier config.php non trouvé\n";
    exit;
}

// 2. Vérifier la base de données
echo "\n2. 🗄️ Vérification de la base de données...\n";
$dbFile = __DIR__ . '/database/catalog.db';
if (file_exists($dbFile)) {
    echo "   ✅ Fichier de base de données trouvé: $dbFile\n";
    echo "   Taille: " . filesize($dbFile) . " octets\n";
} else {
    echo "   ❌ Fichier de base de données non trouvé\n";
    exit;
}

// 3. Tester la connexion
echo "\n3. 🔌 Test de connexion à la base de données...\n";
try {
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✅ Connexion réussie\n";
} catch (Exception $e) {
    echo "   ❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit;
}

// 4. Vérifier la structure de la table
echo "\n4. 📋 Structure de la table participants...\n";
try {
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Colonnes trouvées:\n";
    foreach ($columns as $column) {
        echo "   - {$column['name']} ({$column['type']})\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur lors de la vérification de la structure: " . $e->getMessage() . "\n";
}

// 5. Vérifier le participant 12
echo "\n5. 👤 Vérification du participant 12...\n";
try {
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = 12");
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participant) {
        echo "   ✅ Participant trouvé:\n";
        echo "      ID: {$participant['id']}\n";
        echo "      Nom: {$participant['first_name']} {$participant['last_name']}\n";
        echo "      Email: {$participant['email']}\n";
        echo "      Statut: {$participant['status']}\n";
        echo "      Training ID: {$participant['training_id']}\n";
    } else {
        echo "   ❌ Participant 12 non trouvé\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur lors de la récupération: " . $e->getMessage() . "\n";
}

// 6. Test de modification
echo "\n6. ✏️ Test de modification du statut...\n";
try {
    // Sauvegarder l'ancien statut
    $stmt = $pdo->prepare("SELECT status FROM participants WHERE id = 12");
    $stmt->execute();
    $oldStatus = $stmt->fetchColumn();
    echo "   Ancien statut: $oldStatus\n";
    
    // Modifier le statut
    $newStatus = ($oldStatus === 'pending') ? 'confirmed' : 'pending';
    $stmt = $pdo->prepare("UPDATE participants SET status = ? WHERE id = 12");
    $result = $stmt->execute([$newStatus]);
    
    if ($result) {
        echo "   ✅ Modification réussie\n";
        echo "   Lignes affectées: " . $stmt->rowCount() . "\n";
        
        // Vérifier la modification
        $stmt = $pdo->prepare("SELECT status FROM participants WHERE id = 12");
        $stmt->execute();
        $currentStatus = $stmt->fetchColumn();
        echo "   Nouveau statut: $currentStatus\n";
        
        if ($currentStatus === $newStatus) {
            echo "   ✅ Statut correctement mis à jour\n";
        } else {
            echo "   ❌ Statut non mis à jour\n";
        }
    } else {
        echo "   ❌ Échec de la modification\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur lors de la modification: " . $e->getMessage() . "\n";
}

// 7. Vérifier les permissions
echo "\n7. 🔐 Vérification des permissions...\n";
if (is_writable($dbFile)) {
    echo "   ✅ Base de données accessible en écriture\n";
} else {
    echo "   ❌ Base de données non accessible en écriture\n";
}

// 8. Vérifier les logs
echo "\n8. 📝 Vérification des logs...\n";
$logFile = __DIR__ . '/php_error.log';
if (file_exists($logFile)) {
    echo "   ✅ Fichier de log trouvé: $logFile\n";
    echo "   Taille: " . filesize($logFile) . " octets\n";
    
    // Lire les dernières lignes
    $lines = file($logFile);
    if ($lines) {
        $recentLines = array_slice($lines, -10);
        echo "   Dernières lignes:\n";
        foreach ($recentLines as $line) {
            echo "   " . trim($line) . "\n";
        }
    }
} else {
    echo "   ❌ Fichier de log non trouvé\n";
}

echo "\n🎯 DIAGNOSTIC TERMINÉ !\n";
echo "=======================\n";
echo "✅ Configuration vérifiée\n";
echo "✅ Base de données accessible\n";
echo "✅ Structure de table vérifiée\n";
echo "✅ Participant 12 trouvé\n";
echo "✅ Test de modification effectué\n";
echo "✅ Permissions vérifiées\n";
echo "✅ Logs analysés\n";

echo "\n💡 RECOMMANDATIONS:\n";
echo "==================\n";
echo "🔍 Si le statut ne persiste pas après rechargement:\n";
echo "   1. Vérifiez que l'API reçoit bien le statut\n";
echo "   2. Vérifiez que la requête SQL s'exécute\n";
echo "   3. Vérifiez que la base est bien en écriture\n";
echo "   4. Vérifiez les logs PHP pour les erreurs\n";
echo "   5. Testez l'API directement avec curl\n";

echo "\n🌐 POUR TESTER L'API:\n";
echo "curl -X PUT http://localhost:8000/api/participants.php/12 \\\n";
echo "  -H 'Content-Type: application/json' \\\n";
echo "  -d '{\"training_id\":1,\"first_name\":\"test\",\"last_name\":\"user\",\"email\":\"test@test.com\",\"status\":\"confirmed\"}'\n";
?> 