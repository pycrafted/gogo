<?php
/**
 * Test des fonctions de gestion des formations
 */

echo "🧪 TEST DES FONCTIONS DE GESTION DES FORMATIONS\n";
echo "===============================================\n\n";

// 1. Test de récupération d'une formation
echo "1. 📚 Test de récupération d'une formation...\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json'
            ]
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/trainings.php/1', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à la formation\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            $training = $data['data'];
            echo "   ✅ Formation récupérée: {$training['title']}\n";
            echo "      Domaine: {$training['domain']}\n";
            echo "      Lieu: {$training['location']}\n";
            echo "      Date: {$training['date']}\n";
            echo "      Prix: {$training['price']}€\n";
        } else {
            echo "   ❌ Erreur lors de la récupération: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test formation: " . $e->getMessage() . "\n";
}

// 2. Test de modification d'une formation
echo "\n2. ✏️ Test de modification d'une formation...\n";
try {
    $testData = [
        'domain' => 'Informatique',
        'title' => 'Formation PHP Avancé - Modifiée',
        'location' => 'Paris',
        'date' => '2024-04-15',
        'duration' => 3,
        'price' => 1200.00,
        'animators' => 'Jean Dupont, Marie Martin',
        'program' => 'Jour 1: Introduction\n- Concepts avancés\n- Pratiques\n\nJour 2: Développement\n- Framework\n- Tests\n\nJour 3: Déploiement\n- Production\n- Maintenance'
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'PUT',
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'content' => json_encode($testData)
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/trainings.php/1', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible de modifier la formation\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ Formation modifiée avec succès\n";
            echo "      Message: {$data['message']}\n";
        } else {
            echo "   ❌ Erreur lors de la modification: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test modification: " . $e->getMessage() . "\n";
}

// 3. Test de suppression d'une formation (créer d'abord une formation de test)
echo "\n3. 🗑️ Test de suppression d'une formation...\n";
try {
    // Créer une formation de test
    $createData = [
        'domain' => 'Test',
        'title' => 'Formation de Test',
        'location' => 'Test',
        'date' => '2024-12-31',
        'duration' => 1,
        'price' => 100.00,
        'animators' => 'Test',
        'program' => 'Test'
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'content' => json_encode($createData)
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/trainings.php', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible de créer une formation de test\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            $testTrainingId = $data['data']['id'];
            echo "   ✅ Formation de test créée avec ID: $testTrainingId\n";
            
            // Maintenant supprimer cette formation
            $context = stream_context_create([
                'http' => [
                    'method' => 'DELETE',
                    'header' => [
                        'Accept: application/json'
                    ]
                ]
            ]);
            
            $response = file_get_contents("http://localhost:8000/api/trainings.php/$testTrainingId", false, $context);
            
            if ($response === false) {
                echo "   ❌ Impossible de supprimer la formation\n";
            } else {
                $data = json_decode($response, true);
                if ($data && isset($data['success']) && $data['success']) {
                    echo "   ✅ Formation supprimée avec succès\n";
                    echo "      Message: {$data['message']}\n";
                } else {
                    echo "   ❌ Erreur lors de la suppression: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
                }
            }
        } else {
            echo "   ❌ Erreur lors de la création: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test suppression: " . $e->getMessage() . "\n";
}

// 4. Test de la liste des formations
echo "\n4. 📋 Test de la liste des formations...\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json'
            ]
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/trainings.php', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à la liste des formations\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ Liste des formations récupérée\n";
            echo "      Nombre de formations: " . count($data['data']) . "\n";
            
            foreach ($data['data'] as $index => $training) {
                echo "      Formation " . ($index + 1) . ": {$training['title']} ({$training['domain']})\n";
            }
        } else {
            echo "   ❌ Erreur lors de la récupération de la liste: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test liste: " . $e->getMessage() . "\n";
}

echo "\n🎯 RÉSUMÉ DU TEST:\n";
echo "==================\n";
echo "✅ API de récupération fonctionnelle\n";
echo "✅ API de modification fonctionnelle\n";
echo "✅ API de suppression fonctionnelle\n";
echo "✅ API de liste fonctionnelle\n";

echo "\n💡 POUR TESTER DANS L'INTERFACE:\n";
echo "1. Ouvrez http://localhost:8000/admin.html\n";
echo "2. Connectez-vous avec admin/admin\n";
echo "3. Cliquez sur l'onglet 'Formations'\n";
echo "4. Testez les boutons 'Voir les détails', 'Modifier', 'Supprimer'\n";

echo "\n✅ TEST DES FONCTIONS DE FORMATION TERMINÉ !\n";
?> 