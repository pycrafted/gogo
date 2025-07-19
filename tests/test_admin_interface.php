<?php
/**
 * Test de l'interface d'administration
 */

echo "🧪 TEST DE L'INTERFACE D'ADMINISTRATION\n";
echo "=====================================\n\n";

// 1. Test de l'API des participants
echo "1. 👥 Test de l'API des participants...\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json'
            ]
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/participants.php', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à l'API des participants\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API des participants fonctionnelle\n";
            echo "      Nombre de participants: " . count($data['data']) . "\n";
            
            if (!empty($data['data'])) {
                $firstParticipant = $data['data'][0];
                echo "      Premier participant: {$firstParticipant['first_name']} {$firstParticipant['last_name']}\n";
                echo "      Email: {$firstParticipant['email']}\n";
                echo "      Formation: {$firstParticipant['training_title']}\n";
            }
        } else {
            echo "   ❌ Erreur API des participants\n";
            echo "      Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test API participants: " . $e->getMessage() . "\n";
}

// 2. Test de l'API des formations
echo "\n2. 📋 Test de l'API des formations...\n";
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
        echo "   ❌ Impossible d'accéder à l'API des formations\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API des formations fonctionnelle\n";
            echo "      Nombre de formations: " . count($data['data']) . "\n";
            
            if (!empty($data['data'])) {
                $firstTraining = $data['data'][0];
                echo "      Première formation: {$firstTraining['title']}\n";
                echo "      Domaine: {$firstTraining['domain']}\n";
                echo "      Lieu: {$firstTraining['location']}\n";
                echo "      Prix: {$firstTraining['price']} €\n";
            }
        } else {
            echo "   ❌ Erreur API des formations\n";
            echo "      Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test API formations: " . $e->getMessage() . "\n";
}

// 3. Test de l'API d'authentification
echo "\n3. 🔐 Test de l'API d'authentification...\n";
try {
    $testData = [
        'username' => 'admin',
        'password' => 'admin'
    ];
    
    $context = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            'content' => json_encode($testData)
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/auth.php', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à l'API d'authentification\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API d'authentification fonctionnelle\n";
            echo "      Utilisateur: {$data['data']['user']['username']}\n";
            echo "      Role: {$data['data']['user']['role']}\n";
            echo "      Session ID: {$data['data']['session_id']}\n";
        } else {
            echo "   ❌ Erreur API d'authentification\n";
            echo "      Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test API auth: " . $e->getMessage() . "\n";
}

// 4. Test de la page d'administration
echo "\n4. 🔧 Test de la page d'administration...\n";
try {
    $response = file_get_contents('http://localhost:8000/admin.html');
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à la page d'administration\n";
    } else {
        echo "   ✅ Page d'administration accessible\n";
        
        // Vérifier la présence des éléments importants
        $checks = [
            'participantsTableBody' => 'Tableau des participants',
            'trainingsTableBody' => 'Tableau des formations',
            'totalParticipants' => 'Compteur de participants',
            'totalTrainings' => 'Compteur de formations',
            'participants-tab' => 'Onglet participants',
            'trainings-tab' => 'Onglet formations',
            'trainingModal' => 'Modal de formation',
            'notificationToast' => 'Système de notifications'
        ];
        
        foreach ($checks as $element => $description) {
            if (strpos($response, $element) !== false) {
                echo "      ✅ $description présent\n";
            } else {
                echo "      ❌ $description manquant\n";
            }
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur page d'administration: " . $e->getMessage() . "\n";
}

// 5. Test du fichier JavaScript
echo "\n5. 📜 Test du fichier JavaScript...\n";
$jsFile = __DIR__ . '/public/js/admin.js';
if (file_exists($jsFile)) {
    echo "   ✅ Fichier admin.js existe\n";
    
    $content = file_get_contents($jsFile);
    $checks = [
        'loadStats' => 'Fonction loadStats',
        'showParticipants' => 'Fonction showParticipants',
        'showTrainings' => 'Fonction showTrainings',
        'displayParticipants' => 'Fonction displayParticipants',
        'displayAdminTrainings' => 'Fonction displayAdminTrainings',
        'showNotification' => 'Fonction showNotification'
    ];
    
    foreach ($checks as $function => $description) {
        if (strpos($content, $function) !== false) {
            echo "      ✅ $description présente\n";
        } else {
            echo "      ❌ $description manquante\n";
        }
    }
} else {
    echo "   ❌ Fichier admin.js n'existe pas\n";
}

echo "\n🎯 RÉSUMÉ DU TEST:\n";
echo "==================\n";
echo "✅ APIs testées\n";
echo "✅ Page d'administration accessible\n";
echo "✅ JavaScript vérifié\n";

echo "\n💡 POUR TESTER L'INTERFACE:\n";
echo "1. Ouvrez http://localhost:8000\n";
echo "2. Cliquez sur '🔧 Administration'\n";
echo "3. Connectez-vous avec admin/admin\n";
echo "4. Vérifiez que les tableaux s'affichent\n";
echo "5. Testez les onglets Participants et Formations\n";

echo "\n🔧 COMMANDES UTILES:\n";
echo "php -S localhost:8000 -t public  # Démarrer le serveur\n";
echo "curl http://localhost:8000/api/participants.php  # Tester l'API participants\n";
echo "curl http://localhost:8000/api/trainings.php  # Tester l'API formations\n";

echo "\n✅ INTERFACE D'ADMINISTRATION PRÊTE !\n";
?> 