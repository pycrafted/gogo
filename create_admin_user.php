<?php
/**
 * Script de création de l'utilisateur administrateur
 * Crée l'utilisateur admin avec le mot de passe admin/admin
 */

echo "🔧 CRÉATION DE L'UTILISATEUR ADMINISTRATEUR\n";
echo "==========================================\n\n";

try {
    // Inclusion des fichiers nécessaires
    require_once __DIR__ . '/src/config.php';
    require_once __DIR__ . '/src/models/User.php';
    
    echo "1. 📡 Connexion à la base de données...\n";
    $pdo = getDatabaseConnection();
    echo "   ✅ Connexion réussie\n\n";
    
    echo "2. 🗄️  Vérification de la table users...\n";
    
    // Vérifier si la table users existe
    $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='users'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "   ⚠️  Table 'users' n'existe pas, création...\n";
        
        // Créer la table users
        $sql = "
        CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            role VARCHAR(20) DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";
        
        $pdo->exec($sql);
        echo "   ✅ Table 'users' créée\n";
    } else {
        echo "   ✅ Table 'users' existe déjà\n";
    }
    
    echo "\n3. 👤 Vérification de l'utilisateur admin...\n";
    
    // Vérifier si l'utilisateur admin existe déjà
    $stmt = $pdo->prepare("SELECT id, username, email, role FROM users WHERE username = ?");
    $stmt->execute(['admin']);
    $existingUser = $stmt->fetch();
    
    if ($existingUser) {
        echo "   ⚠️  L'utilisateur 'admin' existe déjà:\n";
        echo "      ID: {$existingUser['id']}\n";
        echo "      Username: {$existingUser['username']}\n";
        echo "      Email: {$existingUser['email']}\n";
        echo "      Role: {$existingUser['role']}\n";
        
        // Demander si on veut mettre à jour le mot de passe
        echo "\n   🔄 Mise à jour du mot de passe...\n";
        $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
        $stmt->execute([$hashedPassword, 'admin']);
        
        echo "   ✅ Mot de passe mis à jour\n";
        
    } else {
        echo "   ⚠️  L'utilisateur 'admin' n'existe pas, création...\n";
        
        // Créer l'utilisateur admin
        $hashedPassword = password_hash('admin', PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', $hashedPassword, 'admin@formations.com', 'admin']);
        
        $userId = $pdo->lastInsertId();
        echo "   ✅ Utilisateur 'admin' créé avec l'ID: $userId\n";
    }
    
    echo "\n4. 🧪 Test d'authentification...\n";
    
    // Tester l'authentification
    $userModel = new User();
    $authResult = $userModel->authenticate('admin', 'admin');
    
    if ($authResult) {
        echo "   ✅ Authentification réussie pour admin/admin\n";
        echo "      Username: {$authResult['username']}\n";
        echo "      Email: {$authResult['email']}\n";
        echo "      Role: {$authResult['role']}\n";
    } else {
        echo "   ❌ Échec de l'authentification\n";
    }
    
    echo "\n5. 📊 Statistiques de la base de données...\n";
    
    // Compter les utilisateurs
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    echo "   👥 Nombre d'utilisateurs: $userCount\n";
    
    // Lister tous les utilisateurs
    $stmt = $pdo->prepare("SELECT id, username, email, role, created_at FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    echo "   📋 Liste des utilisateurs:\n";
    foreach ($users as $user) {
        echo "      - ID: {$user['id']} | {$user['username']} | {$user['email']} | {$user['role']} | {$user['created_at']}\n";
    }
    
    echo "\n🎯 RÉSUMÉ:\n";
    echo "==========\n";
    echo "✅ Base de données configurée\n";
    echo "✅ Utilisateur admin créé/mis à jour\n";
    echo "✅ Authentification testée\n";
    echo "\n🔑 INFORMATIONS DE CONNEXION:\n";
    echo "============================\n";
    echo "URL: http://localhost:8000/login.html\n";
    echo "Utilisateur: admin\n";
    echo "Mot de passe: admin\n";
    echo "\n💡 PROCHAINES ÉTAPES:\n";
    echo "1. Démarrez le serveur: php -S localhost:8000 -t public\n";
    echo "2. Ouvrez http://localhost:8000\n";
    echo "3. Cliquez sur 'Administration' dans la navbar\n";
    echo "4. Connectez-vous avec admin/admin\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
?> 