<?php
/**
 * Script de recréation forcée de la base de données
 */

// Supprimer complètement le fichier de base de données
$dbFile = 'database/catalog.db';
if (file_exists($dbFile)) {
    unlink($dbFile);
    echo "🗑️  Base de données supprimée\n";
}

// Supprimer aussi le dossier database s'il existe
if (is_dir('database')) {
    rmdir('database');
    echo "🗑️  Dossier database supprimé\n";
}

// Recréer le dossier
mkdir('database', 0777, true);
echo "📁 Dossier database recréé\n";

// Attendre un peu pour s'assurer que le fichier est bien supprimé
sleep(1);

try {
    // Créer une nouvelle connexion PDO
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
    
    // Création des index
    $pdo->exec("CREATE INDEX idx_trainings_domain ON trainings(domain)");
    $pdo->exec("CREATE INDEX idx_trainings_date ON trainings(date)");
    $pdo->exec("CREATE INDEX idx_participants_training_id ON participants(training_id)");
    $pdo->exec("CREATE INDEX idx_participants_email ON participants(email)");
    $pdo->exec("CREATE INDEX idx_participants_status ON participants(status)");
    echo "✅ Index créés\n";
    
    // Insertion des données de test pour les formations
    $trainings = [
        ['Informatique', 'Introduction à la programmation Python', 'Paris', '2024-02-15', 3, 1200.00, 'Jean Dupont', 'Jour 1: Introduction à Python\nJour 2: Structures de données\nJour 3: Projet pratique'],
        ['Management', 'Leadership et gestion d\'équipe', 'Lyon', '2024-02-20', 2, 800.00, 'Marie Martin', 'Jour 1: Principes du leadership\nJour 2: Techniques de motivation'],
        ['Marketing', 'Marketing digital avancé', 'Marseille', '2024-02-25', 4, 1500.00, 'Pierre Durand, Sophie Bernard', 'Jour 1: Stratégie digitale\nJour 2: SEO et SEM\nJour 3: Réseaux sociaux\nJour 4: Analytics'],
        ['Finance', 'Gestion financière pour non-financiers', 'Toulouse', '2024-03-01', 2, 900.00, 'Claude Moreau', 'Jour 1: Comprendre les états financiers\nJour 2: Analyse financière'],
        ['Ressources Humaines', 'Recrutement et sélection', 'Nantes', '2024-03-05', 3, 1100.00, 'Isabelle Leroy', 'Jour 1: Processus de recrutement\nJour 2: Techniques d\'entretien\nJour 3: Évaluation des candidats'],
        ['Communication', 'Communication interpersonnelle', 'Bordeaux', '2024-03-10', 2, 700.00, 'François Petit', 'Jour 1: Techniques de communication\nJour 2: Gestion des conflits'],
        ['Vente', 'Techniques de vente avancées', 'Strasbourg', '2024-03-15', 3, 1300.00, 'Laurent Dubois', 'Jour 1: Prospection\nJour 2: Négociation\nJour 3: Fidélisation'],
        ['Logistique', 'Gestion de la chaîne logistique', 'Nice', '2024-03-20', 4, 1600.00, 'Nathalie Roux', 'Jour 1: Supply Chain Management\nJour 2: Transport et distribution\nJour 3: Gestion des stocks\nJour 4: Optimisation'],
        ['Informatique', 'Développement web avec React', 'Lille', '2024-03-25', 5, 2000.00, 'Thomas Blanc', 'Jour 1: Introduction à React\nJour 2: Composants et props\nJour 3: State et lifecycle\nJour 4: Hooks\nJour 5: Projet final'],
        ['Management', 'Gestion de projet agile', 'Rennes', '2024-03-30', 3, 1400.00, 'Caroline Simon', 'Jour 1: Méthodologies agiles\nJour 2: Scrum et Kanban\nJour 3: Outils et pratiques']
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO trainings (domain, title, location, date, duration, price, animators, program) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    foreach ($trainings as $training) {
        $stmt->execute($training);
    }
    
    echo "✅ Données de test pour les formations insérées\n";
    
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
    
    echo "🎉 Base de données recréée avec succès !\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la recréation de la base de données: " . $e->getMessage() . "\n";
}
?> 