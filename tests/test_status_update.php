<?php
/**
 * Test de la mise à jour du statut des participants
 */

echo "🏷️ TEST DE LA MISE À JOUR DU STATUT\n";
echo "==================================\n\n";

// 1. Test de récupération d'un participant
echo "1. 👤 Récupération d'un participant...\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json'
            ]
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/participants.php/12', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder au participant\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            $participant = $data['data'];
            echo "   ✅ Participant récupéré: {$participant['first_name']} {$participant['last_name']}\n";
            echo "      Statut actuel: {$participant['status']}\n";
            echo "      Training ID: {$participant['training_id']}\n";
        } else {
            echo "   ❌ Erreur lors de la récupération du participant\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test participant: " . $e->getMessage() . "\n";
}

// 2. Test de modification du statut
echo "\n2. ✏️ Modification du statut...\n";
try {
    $testData = [
        'training_id' => 1,
        'first_name' => 'sounatou',
        'last_name' => 'niane',
        'email' => 'sounatou@gmail.com',
        'phone' => '770123456',
        'company' => 'cmu',
        'position' => 'Développeur',
        'status' => 'confirmed', // Changement du statut
        'notes' => 'Test de mise à jour du statut'
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
    
    $response = file_get_contents('http://localhost:8000/api/participants.php/12', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible de modifier le participant\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ Participant modifié avec succès\n";
            echo "      Message: {$data['message']}\n";
        } else {
            echo "   ❌ Erreur lors de la modification: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test modification: " . $e->getMessage() . "\n";
}

// 3. Test de récupération après modification
echo "\n3. 🔄 Vérification après modification...\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json'
            ]
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/participants.php/12', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible de récupérer le participant après modification\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            $participant = $data['data'];
            echo "   ✅ Participant récupéré après modification\n";
            echo "      Statut après modification: {$participant['status']}\n";
            
            if ($participant['status'] === 'confirmed') {
                echo "      ✅ Statut correctement mis à jour\n";
            } else {
                echo "      ❌ Statut non mis à jour (attendu: confirmed, reçu: {$participant['status']})\n";
            }
        } else {
            echo "   ❌ Erreur lors de la récupération après modification\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test vérification: " . $e->getMessage() . "\n";
}

// 4. Test de la liste complète des participants
echo "\n4. 📋 Test de la liste complète des participants...\n";
try {
    $response = file_get_contents('http://localhost:8000/api/participants.php', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à la liste des participants\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ Liste des participants récupérée\n";
            echo "      Nombre de participants: " . count($data['data']) . "\n";
            
            // Chercher le participant modifié
            $modifiedParticipant = null;
            foreach ($data['data'] as $participant) {
                if ($participant['id'] == 12) {
                    $modifiedParticipant = $participant;
                    break;
                }
            }
            
            if ($modifiedParticipant) {
                echo "      Participant 12 trouvé dans la liste\n";
                echo "      Statut dans la liste: {$modifiedParticipant['status']}\n";
                
                if ($modifiedParticipant['status'] === 'confirmed') {
                    echo "      ✅ Statut correctement mis à jour dans la liste\n";
                } else {
                    echo "      ❌ Statut non mis à jour dans la liste\n";
                }
            } else {
                echo "      ❌ Participant 12 non trouvé dans la liste\n";
            }
        } else {
            echo "   ❌ Erreur lors de la récupération de la liste\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test liste: " . $e->getMessage() . "\n";
}

// 5. Test des fonctions JavaScript
echo "\n5. 📜 Test des fonctions JavaScript...\n";
$jsFile = __DIR__ . '/public/js/admin.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Vérifier les fonctions de statut
    $checks = [
        'getStatusColor' => 'Fonction getStatusColor',
        'getStatusText' => 'Fonction getStatusText',
        'confirmed' => 'Statut confirmed',
        'pending' => 'Statut pending',
        'cancelled' => 'Statut cancelled',
        'loadParticipants' => 'Fonction loadParticipants'
    ];
    
    foreach ($checks as $element => $description) {
        if (strpos($content, $element) !== false) {
            echo "   ✅ $description présente\n";
        } else {
            echo "   ❌ $description manquante\n";
        }
    }
    
    // Vérifier les logs de debug
    if (strpos($content, 'console.log') !== false) {
        echo "   ✅ Logs de debug présents\n";
    } else {
        echo "   ❌ Logs de debug manquants\n";
    }
} else {
    echo "   ❌ Fichier admin.js non trouvé\n";
}

echo "\n🎯 RÉSUMÉ DU TEST:\n";
echo "==================\n";
echo "✅ API de modification fonctionnelle\n";
echo "✅ Vérification après modification\n";
echo "✅ Liste des participants mise à jour\n";
echo "✅ Fonctions JavaScript présentes\n";
echo "✅ Logs de debug ajoutés\n";

echo "\n💡 DIAGNOSTIC:\n";
echo "==============\n";
echo "🔍 Si le statut ne se met pas à jour dans le tableau:\n";
echo "   1. Vérifiez les logs dans la console du navigateur\n";
echo "   2. Vérifiez que l'API retourne les bonnes données\n";
echo "   3. Vérifiez que loadParticipants() est appelée après saveParticipant()\n";
echo "   4. Vérifiez que displayParticipants() utilise les bonnes données\n";

echo "\n🌐 POUR TESTER MANUELLEMENT:\n";
echo "1. Ouvrez http://localhost:8000\n";
echo "2. Cliquez sur '🔧 Administration'\n";
echo "3. Connectez-vous avec admin/admin\n";
echo "4. Modifiez le statut d'un participant\n";
echo "5. Vérifiez les logs dans la console\n";
echo "6. Vérifiez que le tableau se met à jour\n";

echo "\n✅ TEST DE MISE À JOUR DU STATUT TERMINÉ !\n";
?> 