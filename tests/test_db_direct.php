<?php
/**
 * Test direct de la base de données
 */

echo "🔍 TEST DIRECT DE LA BASE DE DONNÉES\n";
echo "====================================\n\n";

try {
    // Inclure la configuration
    require_once __DIR__ . '/src/config.php';
    
    // Connexion à la base de données
    $pdo = getDatabaseConnection();
    echo "✅ Connexion à la base de données réussie\n\n";
    
    // 1. Vérifier la structure de la table participants
    echo "1. 📋 Structure de la table participants:\n";
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        echo "   - {$column['name']} ({$column['type']})\n";
    }
    echo "\n";
    
    // 2. Vérifier le participant 12
    echo "2. 👤 Participant 12:\n";
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = 12");
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participant) {
        echo "   ✅ Participant trouvé:\n";
        foreach ($participant as $key => $value) {
            echo "      $key: $value\n";
        }
    } else {
        echo "   ❌ Participant 12 non trouvé\n";
    }
    echo "\n";
    
    // 3. Modifier le statut
    echo "3. ✏️ Modification du statut:\n";
    $stmt = $pdo->prepare("UPDATE participants SET status = 'confirmed' WHERE id = 12");
    $result = $stmt->execute();
    
    if ($result) {
        echo "   ✅ Requête exécutée\n";
        echo "   Lignes affectées: " . $stmt->rowCount() . "\n";
    } else {
        echo "   ❌ Échec de la requête\n";
    }
    echo "\n";
    
    // 4. Vérifier après modification
    echo "4. 🔍 Vérification après modification:\n";
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, status FROM participants WHERE id = 12");
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participant) {
        echo "   ✅ Participant après modification:\n";
        echo "      ID: {$participant['id']}\n";
        echo "      Nom: {$participant['first_name']} {$participant['last_name']}\n";
        echo "      Statut: {$participant['status']}\n";
    }
    echo "\n";
    
    // 5. Remettre en pending
    echo "5. 🔄 Remise en pending:\n";
    $stmt = $pdo->prepare("UPDATE participants SET status = 'pending' WHERE id = 12");
    $result = $stmt->execute();
    
    if ($result) {
        echo "   ✅ Statut remis en pending\n";
    } else {
        echo "   ❌ Échec de la remise en pending\n";
    }
    echo "\n";
    
    echo "✅ TEST DIRECT TERMINÉ !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?> 