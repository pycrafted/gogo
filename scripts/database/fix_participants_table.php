<?php
/**
 * Script pour corriger la table participants
 */

echo "🔧 Correction de la table participants...\n";

try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Vérifier la structure actuelle
    echo "📊 Structure actuelle de la table participants:\n";
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
        echo "\n🔧 Ajout de la colonne position...\n";
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
        echo "\n✅ La colonne position existe déjà\n";
    }
    
    // Test d'insertion avec la colonne position
    echo "\n🧪 Test d'insertion avec la colonne position...\n";
    $stmt = $pdo->prepare("
        INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        1, 'Test', 'User', 'test@example.com', '0123456789', 'TestCorp', 'Développeur', 'pending'
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "✅ Insertion réussie, ID: $newId\n";
        
        // Vérifier que le participant a été créé avec la position
        $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = ?");
        $stmt->execute([$newId]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($participant) {
            echo "✅ Participant trouvé:\n";
            echo "  - Nom: {$participant['first_name']} {$participant['last_name']}\n";
            echo "  - Email: {$participant['email']}\n";
            echo "  - Position: {$participant['position']}\n";
        }
        
        // Supprimer le test
        $pdo->exec("DELETE FROM participants WHERE id = $newId");
        echo "✅ Test supprimé\n";
    } else {
        echo "❌ Échec de l'insertion\n";
    }
    
    echo "\n🎉 Table participants corrigée avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?> 