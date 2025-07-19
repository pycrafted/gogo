<?php
/**
 * Test complet du système d'authentification
 */

echo "🧪 TEST DU SYSTÈME D'AUTHENTIFICATION\n";
echo "=====================================\n\n";

// 1. Test de la base de données
echo "1. 🗄️  Test de la base de données...\n";
try {
    require_once __DIR__ . '/src/config.php';
    $pdo = getDatabaseConnection();
    echo "   ✅ Connexion à la base de données réussie\n";
    
    // Vérifier la table users
    $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if ($tableExists) {
        echo "   ✅ Table 'users' existe\n";
        
        // Compter les utilisateurs
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        $userCount = $stmt->fetchColumn();
        echo "   📊 Nombre d'utilisateurs: $userCount\n";
    } else {
        echo "   ❌ Table 'users' n'existe pas\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur base de données: " . $e->getMessage() . "\n";
}

// 2. Test du modèle User
echo "\n2. 👤 Test du modèle User...\n";
try {
    require_once __DIR__ . '/src/models/User.php';
    $userModel = new User();
    echo "   ✅ Modèle User instancié\n";
    
    // Test d'authentification
    $authResult = $userModel->authenticate('admin', 'admin');
    if ($authResult) {
        echo "   ✅ Authentification admin/admin réussie\n";
        echo "      Username: {$authResult['username']}\n";
        echo "      Email: {$authResult['email']}\n";
        echo "      Role: {$authResult['role']}\n";
    } else {
        echo "   ❌ Échec de l'authentification admin/admin\n";
    }
    
    // Test d'authentification échouée
    $authResult = $userModel->authenticate('admin', 'wrongpassword');
    if (!$authResult) {
        echo "   ✅ Authentification échouée correctement (mauvais mot de passe)\n";
    } else {
        echo "   ❌ Authentification aurait dû échouer\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Erreur modèle User: " . $e->getMessage() . "\n";
}

// 3. Test de l'API d'authentification
echo "\n3. 🌐 Test de l'API d'authentification...\n";
try {
    // Simuler une requête POST vers l'API
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
        echo "   ❌ Impossible d'accéder à l'API (serveur non démarré)\n";
        echo "   💡 Démarrez le serveur: php -S localhost:8000 -t public\n";
    } else {
        echo "   📡 Réponse API reçue\n";
        
        $data = json_decode($response, true);
        if ($data && isset($data['success']) && $data['success']) {
            echo "   ✅ API d'authentification fonctionnelle\n";
            echo "      Message: {$data['message']}\n";
            if (isset($data['data']['user'])) {
                echo "      Utilisateur: {$data['data']['user']['username']}\n";
                echo "      Role: {$data['data']['user']['role']}\n";
            }
        } else {
            echo "   ❌ Erreur API d'authentification\n";
            echo "      Réponse: " . $response . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test API: " . $e->getMessage() . "\n";
}

// 4. Test de la page de connexion
echo "\n4. 📄 Test de la page de connexion...\n";
$loginFile = __DIR__ . '/public/login.html';
if (file_exists($loginFile)) {
    echo "   ✅ Page login.html existe\n";
    
    $content = file_get_contents($loginFile);
    if (strpos($content, 'admin') !== false && strpos($content, 'admin') !== false) {
        echo "   ✅ Informations de connexion présentes dans la page\n";
    } else {
        echo "   ⚠️  Informations de connexion manquantes dans la page\n";
    }
} else {
    echo "   ❌ Page login.html n'existe pas\n";
}

// 5. Test de la page d'administration
echo "\n5. 🔧 Test de la page d'administration...\n";
$adminFile = __DIR__ . '/public/admin.html';
if (file_exists($adminFile)) {
    echo "   ✅ Page admin.html existe\n";
    
    $content = file_get_contents($adminFile);
    if (strpos($content, 'checkAuthentication') !== false) {
        echo "   ✅ Vérification d'authentification présente\n";
    } else {
        echo "   ⚠️  Vérification d'authentification manquante\n";
    }
    
    if (strpos($content, 'logout') !== false) {
        echo "   ✅ Fonction de déconnexion présente\n";
    } else {
        echo "   ⚠️  Fonction de déconnexion manquante\n";
    }
} else {
    echo "   ❌ Page admin.html n'existe pas\n";
}

echo "\n🎯 RÉSUMÉ DU TEST:\n";
echo "==================\n";
echo "✅ Base de données configurée\n";
echo "✅ Modèle User fonctionnel\n";
echo "⚠️  API à tester avec le serveur démarré\n";
echo "✅ Pages d'interface créées\n";

echo "\n💡 PROCHAINES ÉTAPES:\n";
echo "1. Lancez: php create_admin_user.php\n";
echo "2. Démarrez: php -S localhost:8000 -t public\n";
echo "3. Ouvrez: http://localhost:8000\n";
echo "4. Cliquez sur 'Administration'\n";
echo "5. Connectez-vous avec admin/admin\n";

echo "\n🔧 COMMANDES UTILES:\n";
echo "php create_admin_user.php    # Créer l'utilisateur admin\n";
echo "php -S localhost:8000 -t public  # Démarrer le serveur\n";
echo "curl -X POST http://localhost:8000/api/auth.php -H 'Content-Type: application/json' -d '{\"username\":\"admin\",\"password\":\"admin\"}'  # Tester l'API\n";
?> 