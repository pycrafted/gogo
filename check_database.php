<?php
/**
 * Script de vérification de la structure de la base de données
 */

echo "🔍 Vérification de la structure de la base de données...\n";

try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "📊 Structure de la table trainings:\n";
    $stmt = $pdo->prepare("PRAGMA table_info(trainings)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "  - {$column['name']} ({$column['type']})\n";
    }
    
    echo "\n📊 Structure de la table participants:\n";
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "  - {$column['name']} ({$column['type']})\n";
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
        echo "\n❌ PROBLÈME: La colonne 'position' n'existe pas dans la table participants!\n";
        echo "🔧 Ajout de la colonne position...\n";
        
        $pdo->exec("ALTER TABLE participants ADD COLUMN position VARCHAR(255)");
        echo "✅ Colonne position ajoutée\n";
        
        // Vérifier à nouveau
        $stmt = $pdo->prepare("PRAGMA table_info(participants)");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "\n📊 Nouvelle structure de la table participants:\n";
        foreach ($columns as $column) {
            echo "  - {$column['name']} ({$column['type']})\n";
        }
    } else {
        echo "\n✅ La colonne 'position' existe dans la table participants\n";
    }
    
    // Test d'insertion
    echo "\n🧪 Test d'insertion d'un participant...\n";
    $stmt = $pdo->prepare("
        INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([1, 'Test', 'User', 'test@example.com', '0123456789', 'TestCorp', 'Développeur', 'pending']);
    echo "✅ Test d'insertion réussi\n";
    
    // Supprimer le test
    $pdo->exec("DELETE FROM participants WHERE email = 'test@example.com'");
    echo "✅ Test supprimé\n";
    
    echo "\n🎉 Base de données vérifiée et corrigée !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?> 