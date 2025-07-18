<?php
/**
 * Script de correction forcée de la base de données
 */

echo "🔧 Correction forcée de la base de données...\n";

// Arrêter le serveur PHP s'il tourne
echo "🛑 Arrêt du serveur PHP...\n";
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    exec('taskkill /f /im php.exe 2>nul', $output, $return);
} else {
    exec('pkill -f php 2>/dev/null', $output, $return);
}

sleep(2);

// Supprimer complètement la base de données
$dbFile = 'database/catalog.db';
if (file_exists($dbFile)) {
    unlink($dbFile);
    echo "🗑️  Base de données supprimée\n";
}

// Supprimer le dossier database
if (is_dir('database')) {
    $files = glob('database/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    rmdir('database');
    echo "🗑️  Dossier database supprimé\n";
}

// Recréer le dossier
mkdir('database', 0777, true);
echo "📁 Dossier database recréé\n";

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
    
    // Création de la table participants avec TOUS les champs
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
    echo "✅ Table participants créée avec tous les champs\n";
    
    // Vérifier que la colonne position existe
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasPosition = false;
    foreach ($columns as $column) {
        if ($column['name'] === 'position') {
            $hasPosition = true;
            break;
        }
    }
    
    if (!$hasPosition) {
        throw new Exception("La colonne 'position' n'a pas été créée");
    }
    
    echo "✅ Vérification de la structure de la table participants OK\n";
    
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
    
    // Test d'insertion d'un participant pour vérifier que tout fonctionne
    $testParticipant = [
        1, 'Test', 'User', 'test@example.com', '0123456789', 'TestCorp', 'Développeur', 'pending'
    ];
    
    $stmt = $pdo->prepare("
        INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute($testParticipant);
    echo "✅ Test d'insertion d'un participant réussi\n";
    
    // Supprimer le participant de test
    $pdo->exec("DELETE FROM participants WHERE email = 'test@example.com'");
    echo "✅ Participant de test supprimé\n";
    
    echo "🎉 Base de données corrigée avec succès !\n";
    echo "✅ La table participants a maintenant la colonne 'position'\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la correction de la base de données: " . $e->getMessage() . "\n";
    echo "🔍 Détails de l'erreur: " . $e->getTraceAsString() . "\n";
}
?> 