<?php
/**
 * Script pour corriger la structure de la base de données
 * Ajoute les colonnes manquantes à la table participants
 */

echo "🔧 CORRECTION DE LA STRUCTURE DE LA BASE DE DONNÉES\n";
echo "==================================================\n\n";

try {
    // Connexion à la base de données
    $dbPath = __DIR__ . '/database/training_catalog.db';
    echo "📁 Fichier de base de données: $dbPath\n";
    
    if (!file_exists($dbPath)) {
        echo "❌ Fichier de base de données non trouvé\n";
        echo "💡 Lancez d'abord: php init_database.php\n";
        exit(1);
    }
    
    echo "✅ Fichier de base de données trouvé\n";
    echo "📊 Taille: " . filesize($dbPath) . " bytes\n\n";
    
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Connexion à la base de données réussie\n\n";
    
    // Vérifier les tables existantes
    $stmt = $pdo->prepare("SELECT name FROM sqlite_master WHERE type='table'");
    $stmt->execute();
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Tables existantes: " . implode(', ', $tables) . "\n\n";
    
    // Vérifier la structure de la table participants
    if (in_array('participants', $tables)) {
        echo "🔍 Vérification de la structure de la table 'participants':\n";
        $stmt = $pdo->prepare("PRAGMA table_info(participants)");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $columnNames = [];
        foreach ($columns as $column) {
            $columnNames[] = $column['name'];
            echo "  - {$column['name']} ({$column['type']})\n";
        }
        
        echo "\n📊 Colonnes trouvées: " . implode(', ', $columnNames) . "\n\n";
        
        // Colonnes requises
        $requiredColumns = [
            'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
            'training_id' => 'INTEGER NOT NULL',
            'first_name' => 'TEXT NOT NULL',
            'last_name' => 'TEXT NOT NULL',
            'email' => 'TEXT NOT NULL UNIQUE',
            'phone' => 'TEXT',
            'company' => 'TEXT',
            'position' => 'TEXT',
            'notes' => 'TEXT',
            'status' => 'TEXT DEFAULT "pending"',
            'registration_date' => 'DATETIME DEFAULT CURRENT_TIMESTAMP'
        ];
        
        $missingColumns = [];
        foreach ($requiredColumns as $columnName => $columnDef) {
            if (!in_array($columnName, $columnNames)) {
                $missingColumns[$columnName] = $columnDef;
            }
        }
        
        if (empty($missingColumns)) {
            echo "✅ Toutes les colonnes requises sont présentes\n";
        } else {
            echo "⚠️  Colonnes manquantes détectées:\n";
            foreach ($missingColumns as $columnName => $columnDef) {
                echo "  - $columnName\n";
            }
            
            echo "\n🔧 Ajout des colonnes manquantes...\n";
            
            foreach ($missingColumns as $columnName => $columnDef) {
                try {
                    $sql = "ALTER TABLE participants ADD COLUMN $columnName $columnDef";
                    echo "  Exécution: $sql\n";
                    $pdo->exec($sql);
                    echo "  ✅ Colonne '$columnName' ajoutée\n";
                } catch (Exception $e) {
                    echo "  ❌ Erreur lors de l'ajout de '$columnName': " . $e->getMessage() . "\n";
                }
            }
            
            echo "\n✅ Structure de la table corrigée\n";
        }
        
    } else {
        echo "❌ Table 'participants' n'existe pas\n";
        echo "🔧 Création de la table 'participants'...\n";
        
        $sql = "
        CREATE TABLE participants (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            training_id INTEGER NOT NULL,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            phone TEXT,
            company TEXT,
            position TEXT,
            notes TEXT,
            status TEXT DEFAULT 'pending',
            registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (training_id) REFERENCES trainings(id)
        )
        ";
        
        $pdo->exec($sql);
        echo "✅ Table 'participants' créée\n";
    }
    
    // Vérifier les formations
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM trainings");
    $stmt->execute();
    $trainingCount = $stmt->fetchColumn();
    echo "\n📚 Nombre de formations: $trainingCount\n";
    
    if ($trainingCount == 0) {
        echo "⚠️  Aucune formation trouvée\n";
        echo "💡 Lancez: php init_database.php\n";
    }
    
    // Test d'insertion
    echo "\n🧪 Test d'insertion d'un participant...\n";
    try {
        $timestamp = time();
        $testData = [
            'training_id' => 1,
            'first_name' => 'Test',
            'last_name' => 'Structure',
            'email' => "teststructure{$timestamp}@example.com",
            'phone' => '0123456789',
            'company' => 'TestCorp',
            'position' => 'Développeur',
            'notes' => 'Test de structure',
            'status' => 'pending'
        ];
        
        $stmt = $pdo->prepare("
            INSERT INTO participants (training_id, first_name, last_name, email, phone, company, position, notes, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $testData['training_id'],
            $testData['first_name'],
            $testData['last_name'],
            $testData['email'],
            $testData['phone'],
            $testData['company'],
            $testData['position'],
            $testData['notes'],
            $testData['status']
        ]);
        
        if ($result) {
            $newId = $pdo->lastInsertId();
            echo "✅ Test d'insertion réussi, ID: $newId\n";
            
            // Nettoyer le test
            $pdo->exec("DELETE FROM participants WHERE email = '{$testData['email']}'");
            echo "✅ Test nettoyé\n";
        } else {
            echo "❌ Échec du test d'insertion\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur lors du test d'insertion: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 CORRECTION TERMINÉE\n";
    echo "=====================\n";
    echo "✅ Base de données corrigée\n";
    echo "✅ Structure des tables mise à jour\n";
    echo "✅ Test d'insertion réussi\n";
    echo "\n💡 Vous pouvez maintenant tester l'inscription\n";
    
} catch (Exception $e) {
    echo "❌ ERREUR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
?> 