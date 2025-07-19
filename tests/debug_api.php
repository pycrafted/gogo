<?php
/**
 * Script de diagnostic de l'API des participants
 */

// Simuler une requête HTTP
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['REQUEST_URI'] = '/api/participants.php';
$_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';

// Données de test
$testData = [
    'training_id' => 1,
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test@example.com',
    'phone' => '0123456789',
    'company' => 'TestCorp',
    'position' => 'Développeur'
];

echo "🔍 Diagnostic de l'API des participants...\n\n";

// Test 1: Vérifier les fonctions de validation
echo "1. Test des fonctions de validation:\n";
require_once 'src/utils/helpers.php';

$sanitizedData = sanitizeParticipantData($testData);
echo "   Données sanitizées: " . print_r($sanitizedData, true) . "\n";

$validation = validateParticipantData($sanitizedData);
echo "   Validation: " . ($validation['valid'] ? 'OK' : 'ERREUR') . "\n";
if (!$validation['valid']) {
    echo "   Erreurs: " . implode(', ', $validation['errors']) . "\n";
}

// Test 2: Vérifier la base de données
echo "\n2. Test de la base de données:\n";
try {
    require_once 'src/config.php';
    $pdo = getDatabaseConnection();
    
    // Vérifier si la formation existe
    $stmt = $pdo->prepare("SELECT id, title FROM trainings WHERE id = ?");
    $stmt->execute([1]);
    $training = $stmt->fetch();
    
    if ($training) {
        echo "   Formation trouvée: {$training['title']}\n";
    } else {
        echo "   ❌ Formation avec ID 1 non trouvée\n";
    }
    
    // Vérifier la structure de la table participants
    $stmt = $pdo->prepare("PRAGMA table_info(participants)");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Colonnes de la table participants:\n";
    foreach ($columns as $column) {
        echo "     - {$column['name']} ({$column['type']})\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur base de données: " . $e->getMessage() . "\n";
}

// Test 3: Test d'insertion directe
echo "\n3. Test d'insertion directe:\n";
try {
    $stmt = $pdo->prepare("
        INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $result = $stmt->execute([
        $sanitizedData['training_id'],
        $sanitizedData['first_name'],
        $sanitizedData['last_name'],
        $sanitizedData['email'],
        $sanitizedData['phone'],
        $sanitizedData['company'],
        $sanitizedData['position'],
        'pending'
    ]);
    
    if ($result) {
        $newId = $pdo->lastInsertId();
        echo "   ✅ Insertion réussie, ID: $newId\n";
        
        // Supprimer le test
        $pdo->exec("DELETE FROM participants WHERE id = $newId");
        echo "   ✅ Test supprimé\n";
    } else {
        echo "   ❌ Échec de l'insertion\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur insertion: " . $e->getMessage() . "\n";
}

echo "\n🎉 Diagnostic terminé !\n";
?> 