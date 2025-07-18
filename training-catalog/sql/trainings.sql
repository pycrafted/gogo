-- Script SQL pour PostgreSQL
-- Création de la base de données et des tables pour le catalogue de formations

-- Création de la base de données (à exécuter en tant que super-utilisateur)
-- CREATE DATABASE training_catalog;

-- Connexion à la base de données
-- \c training_catalog;

-- Création de la table des formations (complète selon cahier des charges)
CREATE TABLE IF NOT EXISTS trainings (
    id SERIAL PRIMARY KEY,
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

-- Création de la table des participants
CREATE TABLE IF NOT EXISTS participants (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    company VARCHAR(100),
    training_id INTEGER NOT NULL REFERENCES trainings(id) ON DELETE CASCADE,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'registered'
);

-- Création de la table des utilisateurs (administrateurs)
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création des index pour optimiser les performances
CREATE INDEX IF NOT EXISTS idx_trainings_domain ON trainings(domain);
CREATE INDEX IF NOT EXISTS idx_trainings_date ON trainings(date);
CREATE INDEX IF NOT EXISTS idx_trainings_title ON trainings(title);
CREATE INDEX IF NOT EXISTS idx_trainings_location ON trainings(location);
CREATE INDEX IF NOT EXISTS idx_participants_training_id ON participants(training_id);
CREATE INDEX IF NOT EXISTS idx_participants_email ON participants(email);

-- Insertion de données de test pour les formations
INSERT INTO trainings (domain, title, location, date, duration, price, animators, program) VALUES
('Informatique', 'Introduction à la programmation Python', 'Paris', '2024-02-15', 3, 1200.00, 'Jean Dupont, Marie Martin', 'Jour 1: Introduction à Python, variables, types de données\nJour 2: Structures de contrôle, fonctions\nJour 3: Programmation orientée objet, projets pratiques'),
('Management', 'Leadership et gestion d''équipe', 'Lyon', '2024-02-20', 2, 1500.00, 'Sophie Bernard', 'Jour 1: Styles de leadership, motivation d''équipe\nJour 2: Communication efficace, résolution de conflits'),
('Marketing', 'Stratégies de marketing digital', 'Marseille', '2024-02-25', 4, 1800.00, 'Pierre Dubois, Anne Moreau', 'Jour 1: Fondamentaux du marketing digital\nJour 2: SEO et référencement naturel\nJour 3: Publicité en ligne et réseaux sociaux\nJour 4: Analytics et mesure de performance'),
('Finance', 'Analyse financière pour non-financiers', 'Bordeaux', '2024-03-01', 2, 1400.00, 'Michel Leroy', 'Jour 1: Comprendre les états financiers\nJour 2: Ratios et indicateurs de performance'),
('Ressources Humaines', 'Recrutement et sélection', 'Toulouse', '2024-03-05', 3, 1600.00, 'Isabelle Petit', 'Jour 1: Processus de recrutement\nJour 2: Techniques d''entretien\nJour 3: Évaluation des candidats'),
('Communication', 'Communication interpersonnelle', 'Nantes', '2024-03-10', 2, 1100.00, 'Claire Dubois', 'Jour 1: Techniques de communication\nJour 2: Gestion des situations difficiles'),
('Vente', 'Techniques de vente avancées', 'Strasbourg', '2024-03-15', 3, 1700.00, 'Marc Durand', 'Jour 1: Prospection et qualification\nJour 2: Argumentation et objection\nJour 3: Négociation et closing'),
('Logistique', 'Gestion de la chaîne logistique', 'Nice', '2024-03-20', 4, 2000.00, 'François Mercier', 'Jour 1: Concepts de la supply chain\nJour 2: Planification et prévision\nJour 3: Transport et distribution\nJour 4: Optimisation des coûts'),
('Informatique', 'Développement web avec React', 'Lille', '2024-03-25', 5, 2200.00, 'Thomas Roux, Julie Blanc', 'Jour 1: Introduction à React et JSX\nJour 2: Composants et props\nJour 3: State et lifecycle\nJour 4: Hooks et context\nJour 5: Projet final'),
('Management', 'Gestion de projet agile', 'Rennes', '2024-03-30', 3, 1800.00, 'Laurent Simon', 'Jour 1: Méthodologies agiles\nJour 2: Scrum et sprints\nJour 3: Outils et pratiques');

-- Insertion d'un utilisateur administrateur par défaut
-- Mot de passe: admin123 (hashé avec password_hash)
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@formations.com', 'admin');

-- Création d'une fonction pour mettre à jour automatiquement updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Création des triggers pour mettre à jour automatiquement updated_at
CREATE TRIGGER update_trainings_updated_at 
    BEFORE UPDATE ON trainings 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();

-- Affichage des données insérées
SELECT 'Formations créées:' as info;
SELECT id, domain, title, location, date, duration, price FROM trainings ORDER BY date;

SELECT 'Utilisateur admin créé:' as info;
SELECT username, email, role FROM users; 