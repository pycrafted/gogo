<?php
echo "🔧 Correction de la base de données...\n";

// Supprimer la base de données
$dbFile = 'database/catalog.db';
if (file_exists($dbFile)) {
    unlink($dbFile);
    echo "🗑️  Base de données supprimée\n";
}

// Créer le dossier s'il n'existe pas
if (!is_dir('database')) {
    mkdir('database', 0777, true);
}

try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la table trainings
    $pdo->exec("
        CREATE TABLE trainings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            domain VARCHAR(100) NOT NULL,
            title VARCHAR(255) NOT NULL,
            location VARCHAR(100) NOT NULL,
            date DATE NOT NULL,
            duration INTEGER NOT NULL,
            price DECIMAL(10,2) NOT NULL,
            animators TEXT NOT NULL,
            program TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "✅ Table trainings créée\n";
    
    // Créer la table participants avec la colonne position
    $pdo->exec("
        CREATE TABLE participants (
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
    echo "✅ Table participants créée avec position\n";
    
    // Test d'insertion
    $stmt = $pdo->prepare("
        INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([1, 'Test', 'User', 'test@example.com', '0123456789', 'TestCorp', 'Développeur', 'pending']);
    echo "✅ Test d'insertion réussi\n";
    
    // Supprimer le test
    $pdo->exec("DELETE FROM participants WHERE email = 'test@example.com'");
    echo "✅ Test supprimé\n";
    
    echo "🎉 Base de données corrigée !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}
?> 