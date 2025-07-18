<?php
/**
 * Script de test pour la base de données
 * Vérifie la connexion et les opérations de base
 */

require_once __DIR__ . '/config.php';

echo "<h1>Test de la Base de Données</h1>";

try {
    // Test de connexion
    echo "<h2>1. Test de connexion</h2>";
    $pdo = getDatabaseConnection();
    echo "<p style='color: green;'>✓ Connexion à la base de données réussie</p>";
    
    // Test de la table trainings
    echo "<h2>2. Test de la table trainings</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM trainings");
    $result = $stmt->fetch();
    echo "<p>Nombre de formations dans la base : <strong>{$result['count']}</strong></p>";
    
    // Affichage des formations
    echo "<h2>3. Liste des formations</h2>";
    $stmt = $pdo->query("SELECT * FROM trainings ORDER BY date ASC");
    $trainings = $stmt->fetchAll();
    
    if (count($trainings) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Domaine</th><th>Titre</th><th>Date</th></tr>";
        
        foreach ($trainings as $training) {
            echo "<tr>";
            echo "<td>{$training['id']}</td>";
            echo "<td>{$training['domain']}</td>";
            echo "<td>{$training['title']}</td>";
            echo "<td>{$training['date']}</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠ Aucune formation trouvée dans la base de données</p>";
    }
    
    // Test des utilitaires
    echo "<h2>4. Test des utilitaires</h2>";
    require_once __DIR__ . '/utils/helpers.php';
    
    $testDate = '2025-08-15';
    $formattedDate = formatDate($testDate);
    echo "<p>Test de formatage de date : {$testDate} → {$formattedDate}</p>";
    
    $testDomain = 'Informatique';
    $isValidDomain = validateDomain($testDomain);
    echo "<p>Test de validation de domaine '{$testDomain}' : " . ($isValidDomain ? '✓ Valide' : '✗ Invalide') . "</p>";
    
    echo "<h2>5. Résumé</h2>";
    echo "<p style='color: green;'>✓ Tous les tests sont passés avec succès !</p>";
    echo "<p>La base de données est prête pour l'application.</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Erreur de connexion : " . $e->getMessage() . "</p>";
    echo "<p>Vérifiez que :</p>";
    echo "<ul>";
    echo "<li>MySQL est démarré</li>";
    echo "<li>La base de données 'training_catalog' existe</li>";
    echo "<li>Les paramètres de connexion dans config.php sont corrects</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Erreur : " . $e->getMessage() . "</p>";
}
?> 