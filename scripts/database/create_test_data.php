<?php
/**
 * Création de données de test pour l'application
 */

echo "🔧 Création de données de test...\n";

// Supprimer la base de données existante
$dbFile = 'database/catalog.db';
if (file_exists($dbFile)) {
    unlink($dbFile);
    echo "🗑️  Base de données supprimée\n";
}

try {
    $pdo = new PDO('sqlite:database/catalog.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "🔧 Création de la nouvelle base de données...\n";
    
    // Création de la table trainings
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
    
    // Création de la table participants
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
    echo "✅ Table participants créée\n";
    
    // Insertion des données de test pour les formations
    $trainings = [
        ['Informatique', 'Introduction à la programmation Python', 'Paris', '2024-02-15', 3, 1200.00, 'Jean Dupont', 'Jour 1: Introduction à Python\nJour 2: Structures de données\nJour 3: Projet pratique'],
        ['Management', 'Leadership et gestion d\'équipe', 'Lyon', '2024-02-20', 2, 800.00, 'Marie Martin', 'Jour 1: Principes du leadership\nJour 2: Techniques de motivation'],
        ['Marketing', 'Marketing digital avancé', 'Marseille', '2024-02-25', 4, 1500.00, 'Pierre Durand, Sophie Bernard', 'Jour 1: Stratégie digitale\nJour 2: SEO et SEM\nJour 3: Réseaux sociaux\nJour 4: Analytics'],
        ['Finance', 'Gestion financière pour non-financiers', 'Toulouse', '2024-03-01', 2, 900.00, 'Claude Moreau', 'Jour 1: Comprendre les états financiers\nJour 2: Analyse financière'],
        ['Ressources Humaines', 'Recrutement et sélection', 'Nantes', '2024-03-05', 3, 1100.00, 'Isabelle Leroy', 'Jour 1: Processus de recrutement\nJour 2: Techniques d\'entretien\nJour 3: Évaluation des candidats']
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO trainings (domain, title, location, date, duration, price, animators, program) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($trainings as $training) {
        $stmt->execute($training);
    }
    
    echo "✅ 5 formations de test créées\n";
    
    // Test d'insertion d'un participant
    $testParticipant = [
        1, 'Test', 'User', 'test@example.com', '0123456789', 'TestCorp', 'Développeur', 'pending'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute($testParticipant);
    echo "✅ Participant de test créé\n";
    
    // Vérifier les données
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM trainings");
    $stmt->execute();
    $trainingCount = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM participants");
    $stmt->execute();
    $participantCount = $stmt->fetchColumn();
    
    echo "\n📊 Vérification des données:\n";
    echo "   - Formations: $trainingCount\n";
    echo "   - Participants: $participantCount\n";
    
    // Afficher les formations
    $stmt = $pdo->prepare("SELECT id, title, domain FROM trainings ORDER BY id");
    $stmt->execute();
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\n📋 Formations disponibles:\n";
    foreach ($trainings as $training) {
        echo "   - ID: {$training['id']} - {$training['title']} ({$training['domain']})\n";
    }
    
    echo "\n🎉 Base de données de test créée avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "📋 Stack trace: " . $e->getTraceAsString() . "\n";
}
?> 