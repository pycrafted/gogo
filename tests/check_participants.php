<?php
/**
 * Vérification des participants dans la base de données
 */

echo "🔍 Vérification des participants...\n\n";

try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Compter les participants
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    echo "📊 Nombre total de participants: $count\n";
    
    if ($count > 0) {
        // Afficher les participants
        $stmt = $pdo->prepare("SELECT * FROM participants ORDER BY id DESC LIMIT 5");
        $stmt->execute();
        $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\n📋 Derniers participants:\n";
        foreach ($participants as $participant) {
            echo "   - ID: {$participant['id']}\n";
            echo "     Nom: {$participant['first_name']} {$participant['last_name']}\n";
            echo "     Email: {$participant['email']}\n";
            echo "     Formation: {$participant['training_id']}\n";
            echo "     Position: {$participant['position']}\n";
            echo "     Statut: {$participant['status']}\n";
            echo "     Date: {$participant['registration_date']}\n";
            echo "     ---\n";
        }
    } else {
        echo "   ℹ️  Aucun participant trouvé\n";
    }
    
    // Vérifier les formations
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM trainings");
    $stmt->execute();
    $trainingCount = $stmt->fetchColumn();
    echo "\n📊 Nombre de formations: $trainingCount\n";
    
    if ($trainingCount > 0) {
        $stmt = $pdo->prepare("SELECT id, title FROM trainings ORDER BY id LIMIT 3");
        $stmt->execute();
        $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "📋 Formations disponibles:\n";
        foreach ($trainings as $training) {
            echo "   - ID: {$training['id']} - {$training['title']}\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n🎉 Vérification terminée !\n";
?> 