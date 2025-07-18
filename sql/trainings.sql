-- Script SQL pour PostgreSQL
-- Création de la base de données et des tables pour le catalogue de formations

-- Création de la base de données (à exécuter en tant que super-utilisateur)
-- CREATE DATABASE training_catalog;

-- Connexion à la base de données
-- \c training_catalog;

-- Création de la table des formations
CREATE TABLE IF NOT EXISTS trainings (
    id SERIAL PRIMARY KEY,
    domain VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création d'un index sur le domaine pour optimiser les recherches
CREATE INDEX IF NOT EXISTS idx_trainings_domain ON trainings(domain);

-- Création d'un index sur la date pour optimiser les tris
CREATE INDEX IF NOT EXISTS idx_trainings_date ON trainings(date);

-- Création d'un index sur le titre pour optimiser les recherches
CREATE INDEX IF NOT EXISTS idx_trainings_title ON trainings(title);

-- Insertion de données de test
INSERT INTO trainings (domain, title, date) VALUES
('Informatique', 'Introduction à la programmation Python', '2024-02-15'),
('Management', 'Leadership et gestion d\'équipe', '2024-02-20'),
('Marketing', 'Stratégies de marketing digital', '2024-02-25'),
('Finance', 'Analyse financière pour non-financiers', '2024-03-01'),
('Ressources Humaines', 'Recrutement et sélection', '2024-03-05'),
('Communication', 'Communication interpersonnelle', '2024-03-10'),
('Vente', 'Techniques de vente avancées', '2024-03-15'),
('Logistique', 'Gestion de la chaîne logistique', '2024-03-20'),
('Informatique', 'Développement web avec React', '2024-03-25'),
('Management', 'Gestion de projet agile', '2024-03-30'),
('Marketing', 'Marketing automation', '2024-04-05'),
('Finance', 'Investissement et gestion de portefeuille', '2024-04-10'),
('Ressources Humaines', 'Gestion des performances', '2024-04-15'),
('Communication', 'Présentation et prise de parole', '2024-04-20'),
('Vente', 'Négociation commerciale', '2024-04-25'),
('Logistique', 'Optimisation des processus', '2024-04-30');

-- Création d'une fonction pour mettre à jour automatiquement updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Création du trigger pour mettre à jour automatiquement updated_at
CREATE TRIGGER update_trainings_updated_at 
    BEFORE UPDATE ON trainings 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();

-- Affichage des données insérées
SELECT * FROM trainings ORDER BY date; 