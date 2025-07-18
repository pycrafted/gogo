<?php
/**
 * Configuration de la base de données
 * Couche Data Storage - Configuration de la connexion SQLite (complète selon cahier des charges)
 */

// Paramètres de connexion à la base de données SQLite
define('DB_FILE', __DIR__ . '/../database/training_catalog.sqlite');

/**
 * Établit la connexion à la base de données SQLite
 * @return PDO Instance de connexion PDO
 * @throws PDOException En cas d'erreur de connexion
 */
function getDatabaseConnection() {
    // Options PDO pour SQLite
    $pdoOptions = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];
    
    try {
        // Créer le répertoire database s'il n'existe pas
        $dbDir = dirname(DB_FILE);
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        
        $dsn = "sqlite:" . DB_FILE;
        $pdo = new PDO($dsn, null, null, $pdoOptions);
        
        // Créer les tables si elles n'existent pas
        createTablesIfNotExist($pdo);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion à la base de données: " . $e->getMessage());
        throw new PDOException("Impossible de se connecter à la base de données");
    }
}

/**
 * Crée les tables si elles n'existent pas
 * @param PDO $pdo Instance de connexion PDO
 */
function createTablesIfNotExist($pdo) {
    $sql = "
    CREATE TABLE IF NOT EXISTS trainings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        domain VARCHAR(100) NOT NULL,
        title VARCHAR(255) NOT NULL,
        location VARCHAR(100) NOT NULL,
        date DATE NOT NULL,
        duration INTEGER NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        animators TEXT NOT NULL,
        program TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    
    CREATE TABLE IF NOT EXISTS participants (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        phone VARCHAR(20),
        company VARCHAR(100),
        training_id INTEGER NOT NULL,
        registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status VARCHAR(20) DEFAULT 'registered',
        FOREIGN KEY (training_id) REFERENCES trainings(id) ON DELETE CASCADE
    );
    
    CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        role VARCHAR(20) DEFAULT 'admin',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    
    CREATE INDEX IF NOT EXISTS idx_trainings_domain ON trainings(domain);
    CREATE INDEX IF NOT EXISTS idx_trainings_date ON trainings(date);
    CREATE INDEX IF NOT EXISTS idx_trainings_title ON trainings(title);
    CREATE INDEX IF NOT EXISTS idx_trainings_location ON trainings(location);
    CREATE INDEX IF NOT EXISTS idx_participants_training_id ON participants(training_id);
    CREATE INDEX IF NOT EXISTS idx_participants_email ON participants(email);
    ";
    
    $pdo->exec($sql);
    
    // Insérer des données de test si la table est vide
    $stmt = $pdo->query("SELECT COUNT(*) FROM trainings");
    if ($stmt->fetchColumn() == 0) {
        insertSampleData($pdo);
    }
    
    // Insérer l'utilisateur admin si la table est vide
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        insertAdminUser($pdo);
    }
}

/**
 * Insère des données de test
 * @param PDO $pdo Instance de connexion PDO
 */
function insertSampleData($pdo) {
    $sampleData = [
        ['Informatique', 'Introduction à la programmation Python', 'Paris', '2024-02-15', 3, 1200.00, 'Jean Dupont, Marie Martin', 'Jour 1: Introduction à Python, variables, types de données\nJour 2: Structures de contrôle, fonctions\nJour 3: Programmation orientée objet, projets pratiques'],
        ['Management', 'Leadership et gestion d\'équipe', 'Lyon', '2024-02-20', 2, 1500.00, 'Sophie Bernard', 'Jour 1: Styles de leadership, motivation d\'équipe\nJour 2: Communication efficace, résolution de conflits'],
        ['Marketing', 'Stratégies de marketing digital', 'Marseille', '2024-02-25', 4, 1800.00, 'Pierre Dubois, Anne Moreau', 'Jour 1: Fondamentaux du marketing digital\nJour 2: SEO et référencement naturel\nJour 3: Publicité en ligne et réseaux sociaux\nJour 4: Analytics et mesure de performance'],
        ['Finance', 'Analyse financière pour non-financiers', 'Bordeaux', '2024-03-01', 2, 1400.00, 'Michel Leroy', 'Jour 1: Comprendre les états financiers\nJour 2: Ratios et indicateurs de performance'],
        ['Ressources Humaines', 'Recrutement et sélection', 'Toulouse', '2024-03-05', 3, 1600.00, 'Isabelle Petit', 'Jour 1: Processus de recrutement\nJour 2: Techniques d\'entretien\nJour 3: Évaluation des candidats'],
        ['Communication', 'Communication interpersonnelle', 'Nantes', '2024-03-10', 2, 1100.00, 'Claire Dubois', 'Jour 1: Techniques de communication\nJour 2: Gestion des situations difficiles'],
        ['Vente', 'Techniques de vente avancées', 'Strasbourg', '2024-03-15', 3, 1700.00, 'Marc Durand', 'Jour 1: Prospection et qualification\nJour 2: Argumentation et objection\nJour 3: Négociation et closing'],
        ['Logistique', 'Gestion de la chaîne logistique', 'Nice', '2024-03-20', 4, 2000.00, 'François Mercier', 'Jour 1: Concepts de la supply chain\nJour 2: Planification et prévision\nJour 3: Transport et distribution\nJour 4: Optimisation des coûts'],
        ['Informatique', 'Développement web avec React', 'Lille', '2024-03-25', 5, 2200.00, 'Thomas Roux, Julie Blanc', 'Jour 1: Introduction à React et JSX\nJour 2: Composants et props\nJour 3: State et lifecycle\nJour 4: Hooks et context\nJour 5: Projet final'],
        ['Management', 'Gestion de projet agile', 'Rennes', '2024-03-30', 3, 1800.00, 'Laurent Simon', 'Jour 1: Méthodologies agiles\nJour 2: Scrum et sprints\nJour 3: Outils et pratiques']
    ];
    
    $stmt = $pdo->prepare("INSERT INTO trainings (domain, title, location, date, duration, price, animators, program) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($sampleData as $data) {
        $stmt->execute($data);
    }
}

/**
 * Insère l'utilisateur administrateur par défaut
 * @param PDO $pdo Instance de connexion PDO
 */
function insertAdminUser($pdo) {
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->execute(['admin', $password, 'admin@formations.com', 'admin']);
}

/**
 * Test de connexion à la base de données
 * @return bool True si la connexion réussit, false sinon
 */
function testDatabaseConnection() {
    try {
        $pdo = getDatabaseConnection();
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?> 