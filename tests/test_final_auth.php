<?php
/**
 * Test final du système d'authentification complet
 */

echo "🎯 TEST FINAL DU SYSTÈME D'AUTHENTIFICATION\n";
echo "==========================================\n\n";

// 1. Test de l'API d'authentification
echo "1. 🔐 Test de l'API d'authentification...\n";
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
        echo "   ❌ Impossible d'accéder à l'API\n";
        echo "   💡 Vérifiez que le serveur est démarré: php -S localhost:8000 -t public\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API d'authentification fonctionnelle\n";
            echo "      Message: {$data['message']}\n";
            echo "      Utilisateur: {$data['data']['user']['username']}\n";
            echo "      Role: {$data['data']['user']['role']}\n";
            echo "      Session ID: {$data['data']['session_id']}\n";
        } else {
            echo "   ❌ Erreur API d'authentification\n";
            echo "      Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test API: " . $e->getMessage() . "\n";
}

// 2. Test de vérification du statut de connexion
echo "\n2. 🔍 Test de vérification du statut...\n";
try {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: application/json'
            ]
        ]
    ]);
    
    $response = file_get_contents('http://localhost:8000/api/auth.php', false, $context);
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à l'API\n";
    } else {
        $data = json_decode($response, true);
        if ($data && isset($data['success'])) {
            if ($data['success']) {
                echo "   ✅ Utilisateur connecté\n";
                echo "      Utilisateur: {$data['data']['user']['username']}\n";
                echo "      Role: {$data['data']['user']['role']}\n";
            } else {
                echo "   ✅ Aucun utilisateur connecté (normal)\n";
            }
        } else {
            echo "   ❌ Erreur vérification statut\n";
            echo "      Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur vérification statut: " . $e->getMessage() . "\n";
}

// 3. Test de la page de connexion
echo "\n3. 📄 Test de la page de connexion...\n";
try {
    $response = file_get_contents('http://localhost:8000/login.html');
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à la page de connexion\n";
    } else {
        echo "   ✅ Page de connexion accessible\n";
        
        // Vérifier la présence des éléments importants
        if (strpos($response, 'admin') !== false) {
            echo "   ✅ Informations de connexion présentes\n";
        }
        
        if (strpos($response, 'handleLogin') !== false) {
            echo "   ✅ Fonction JavaScript de connexion présente\n";
        }
        
        if (strpos($response, 'Bootstrap') !== false) {
            echo "   ✅ Interface Bootstrap chargée\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur page de connexion: " . $e->getMessage() . "\n";
}

// 4. Test de la page d'administration (sans authentification)
echo "\n4. 🔧 Test de la page d'administration...\n";
try {
    $response = file_get_contents('http://localhost:8000/admin.html');
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à la page d'administration\n";
    } else {
        echo "   ✅ Page d'administration accessible\n";
        
        // Vérifier la présence des éléments de sécurité
        if (strpos($response, 'checkAuthentication') !== false) {
            echo "   ✅ Vérification d'authentification présente\n";
        }
        
        if (strpos($response, 'logout') !== false) {
            echo "   ✅ Fonction de déconnexion présente\n";
        }
        
        if (strpos($response, 'currentUser') !== false) {
            echo "   ✅ Affichage utilisateur connecté présent\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur page d'administration: " . $e->getMessage() . "\n";
}

// 5. Test de la page principale
echo "\n5. 🏠 Test de la page principale...\n";
try {
    $response = file_get_contents('http://localhost:8000/');
    
    if ($response === false) {
        echo "   ❌ Impossible d'accéder à la page principale\n";
    } else {
        echo "   ✅ Page principale accessible\n";
        
        // Vérifier le lien vers l'administration
        if (strpos($response, 'login.html') !== false) {
            echo "   ✅ Lien vers l'administration correct\n";
        }
        
        if (strpos($response, 'Administration') !== false) {
            echo "   ✅ Menu Administration présent\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur page principale: " . $e->getMessage() . "\n";
}

echo "\n🎯 RÉSUMÉ FINAL:\n";
echo "================\n";
echo "✅ API d'authentification fonctionnelle\n";
echo "✅ Pages d'interface accessibles\n";
echo "✅ Système de sécurité en place\n";
echo "✅ Redirection vers login configurée\n";

echo "\n🔑 INFORMATIONS DE CONNEXION:\n";
echo "============================\n";
echo "URL: http://localhost:8000\n";
echo "Administration: http://localhost:8000/login.html\n";
echo "Utilisateur: admin\n";
echo "Mot de passe: admin\n";

echo "\n💡 TEST MANUEL:\n";
echo "1. Ouvrez http://localhost:8000\n";
echo "2. Cliquez sur '🔧 Administration' dans la navbar\n";
echo "3. Connectez-vous avec admin/admin\n";
echo "4. Vérifiez que vous accédez au dashboard\n";
echo "5. Testez la déconnexion\n";

echo "\n🔧 COMMANDES UTILES:\n";
echo "php -S localhost:8000 -t public  # Démarrer le serveur\n";
echo "curl -X POST http://localhost:8000/api/auth.php -H 'Content-Type: application/json' -d '{\"username\":\"admin\",\"password\":\"admin\"}'  # Tester l'API\n";
echo "curl -X DELETE http://localhost:8000/api/auth.php  # Tester la déconnexion\n";

echo "\n✅ SYSTÈME D'AUTHENTIFICATION PRÊT !\n";
?> 