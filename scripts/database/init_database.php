<?php
/**
 * Script d'initialisation de la base de données
 */

echo "=== Initialisation de la base de données ===\n";

try {
    // Connexion à la base de données
    $dbPath = __DIR__ . '/database/training_catalog.db';
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connexion à la base de données réussie\n";
    
    // Création de la table trainings
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS trainings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            domain TEXT NOT NULL,
            title TEXT NOT NULL,
            location TEXT,
            date TEXT NOT NULL,
            duration INTEGER,
            price REAL,
            animators TEXT,
            program TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table 'trainings' créée/vérifiée\n";
    
    // Création de la table participants
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS participants (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            training_id INTEGER NOT NULL,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            phone TEXT,
            company TEXT,
            position TEXT,
            notes TEXT,
            status TEXT DEFAULT 'pending',
            registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (training_id) REFERENCES trainings(id)
        )
    ");
    echo "Table 'participants' créée/vérifiée\n";
    
    // Vérification des données existantes
    $stmt = $pdo->query("SELECT COUNT(*) FROM trainings");
    $trainingCount = $stmt->fetchColumn();
    
    if ($trainingCount == 0) {
        echo "Insertion de données de test...\n";
        
        // Insertion de formations de test
        $trainings = [
            [
                'domain' => 'Informatique',
                'title' => 'Formation PHP Avancé',
                'location' => 'Paris',
                'date' => '2024-03-15',
                'duration' => 3,
                'price' => 1200.00,
                'animators' => 'Jean Martin, Marie Dubois',
                'program' => 'Jour 1: POO et Design Patterns\nJour 2: Framework Symfony\nJour 3: Tests et déploiement'
            ],
            [
                'domain' => 'Management',
                'title' => 'Leadership et Gestion d\'équipe',
                'location' => 'Lyon',
                'date' => '2024-03-20',
                'duration' => 2,
                'price' => 800.00,
                'animators' => 'Pierre Durand',
                'program' => 'Jour 1: Techniques de leadership\nJour 2: Gestion des conflits et motivation'
            ],
            [
                'domain' => 'Marketing',
                'title' => 'Marketing Digital',
                'location' => 'Marseille',
                'date' => '2024-04-10',
                'duration' => 4,
                'price' => 1500.00,
                'animators' => 'Sophie Bernard, Marc Leroy',
                'program' => 'Jour 1: SEO et référencement\nJour 2: Réseaux sociaux\nJour 3: Email marketing\nJour 4: Analytics et ROI'
            ]
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO trainings (domain, title, location, date, duration, price, animators, program)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($trainings as $training) {
            $stmt->execute([
                $training['domain'],
                $training['title'],
                $training['location'],
                $training['date'],
                $training['duration'],
                $training['price'],
                $training['animators'],
                $training['program']
            ]);
        }
        
        echo "3 formations de test ajoutées\n";
    } else {
        echo "$trainingCount formations déjà présentes dans la base\n";
    }
    
    // Vérification des participants
    $stmt = $pdo->query("SELECT COUNT(*) FROM participants");
    $participantCount = $stmt->fetchColumn();
    echo "$participantCount participants dans la base\n";
    
    // Affichage des formations disponibles
    echo "\nFormations disponibles:\n";
    $stmt = $pdo->query("SELECT id, domain, title, location, date FROM trainings ORDER BY date");
    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($trainings as $training) {
        echo "- ID: {$training['id']} | {$training['domain']} | {$training['title']} | {$training['location']} | {$training['date']}\n";
    }
    
    echo "\n=== Base de données initialisée avec succès ===\n";
    
} catch (Exception $e) {
    echo "Erreur lors de l'initialisation: " . $e->getMessage() . "\n";
    exit(1);
}
?> 