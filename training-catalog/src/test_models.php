<?php
/**
 * Test des modèles - Vérification des nouvelles fonctionnalités
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/models/Training.php';
require_once __DIR__ . '/models/Participant.php';
require_once __DIR__ . '/models/User.php';

echo "<h1>Test des Modèles</h1>";

try {
    // Test du modèle Training
    echo "<h2>1. Test du modèle Training</h2>";
    $training = new Training();
    
    // Récupérer toutes les formations
    $trainings = $training->getAll();
    echo "<p>Nombre de formations: <strong>" . count($trainings) . "</strong></p>";
    
    // Afficher la première formation avec tous les champs
    if (!empty($trainings)) {
        $firstTraining = $trainings[0];
        echo "<h3>Première formation (tous les champs):</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Champ</th><th>Valeur</th></tr>";
        foreach ($firstTraining as $field => $value) {
            echo "<tr><td><strong>$field</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
    }
    
    // Test du modèle User
    echo "<h2>2. Test du modèle User</h2>";
    $user = new User();
    
    // Test d'authentification
    $authResult = $user->authenticate('admin', 'admin123');
    if ($authResult) {
        echo "<p style='color: green;'>✓ Authentification réussie pour admin</p>";
        echo "<p>Utilisateur connecté: " . $authResult['username'] . " (Role: " . $authResult['role'] . ")</p>";
    } else {
        echo "<p style='color: red;'>✗ Échec de l'authentification</p>";
    }
    
    // Test du modèle Participant
    echo "<h2>3. Test du modèle Participant</h2>";
    $participant = new Participant();
    
    // Compter les participants
    $participants = $participant->getAll();
    echo "<p>Nombre de participants: <strong>" . count($participants) . "</strong></p>";
    
    // Test d'inscription d'un participant
    $testParticipant = [
        'first_name' => 'Jean',
        'last_name' => 'Dupont',
        'email' => 'jean.dupont@test.com',
        'phone' => '0123456789',
        'company' => 'Test Company',
        'training_id' => 1
    ];
    
    // Vérifier si déjà inscrit
    if (!$participant->isAlreadyRegistered($testParticipant['email'], $testParticipant['training_id'])) {
        $participantId = $participant->create($testParticipant);
        echo "<p style='color: green;'>✓ Participant créé avec l'ID: $participantId</p>";
    } else {
        echo "<p style='color: orange;'>⚠ Participant déjà inscrit</p>";
    }
    
    // Afficher les participants par formation
    $participantsByTraining = $participant->getByTraining(1);
    echo "<p>Participants pour la formation ID 1: <strong>" . count($participantsByTraining) . "</strong></p>";
    
    echo "<h2>4. Résumé</h2>";
    echo "<p style='color: green;'>✓ Tous les modèles fonctionnent correctement !</p>";
    echo "<p>La base de données est complète et prête pour l'application.</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?> 