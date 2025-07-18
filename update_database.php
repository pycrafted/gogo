<?php
/**
 * Script de mise à jour de la base de données
 * Ajoute la table participants et les données de test
 */

require_once __DIR__ . '/src/config.php';

try {
    $pdo = getDatabaseConnection();
    
    echo "🔧 Mise à jour de la base de données...\n";
    
    // Création de la table participants
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS participants (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            training_id INTEGER NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            phone VARCHAR(20),
            company VARCHAR(255),
            position VARCHAR(255),
            registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            status VARCHAR(50) DEFAULT 'pending',
            notes TEXT,
            FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE
        )
    ");
    echo "✅ Table participants créée\n";
    
    // Création des index
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_participants_training_id ON participants(training_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_participants_email ON participants(email)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_participants_status ON participants(status)");
    echo "✅ Index créés\n";
    
    // Vérification si des données de test existent déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants");
    $stmt->execute();
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        // Insertion des données de test pour les participants
        $participants = [
            [1, 'Alice', 'Martin', 'alice.martin@email.com', '0123456789', 'TechCorp', 'Développeuse', 'confirmed'],
            [1, 'Bob', 'Durand', 'bob.durand@email.com', '0123456790', 'StartupXYZ', 'Chef de projet', 'confirmed'],
            [2, 'Claire', 'Bernard', 'claire.bernard@email.com', '0123456791', 'ManagementPlus', 'Manager', 'pending'],
            [3, 'David', 'Petit', 'david.petit@email.com', '0123456792', 'DigitalAgency', 'Marketing Manager', 'confirmed'],
            [4, 'Emma', 'Roux', 'emma.roux@email.com', '0123456793', 'FinanceGroup', 'Analyste', 'confirmed'],
            [5, 'François', 'Moreau', 'francois.moreau@email.com', '0123456794', 'HRConsulting', 'Recruteur', 'pending']
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($participants as $participant) {
            $stmt->execute($participant);
        }
        
        echo "✅ Données de test pour les participants insérées\n";
    } else {
        echo "ℹ️  Des participants existent déjà, pas d'insertion de données de test\n";
    }
    
    echo "🎉 Base de données mise à jour avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la mise à jour de la base de données: " . $e->getMessage() . "\n";
}
?> 