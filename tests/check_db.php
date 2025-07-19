<?php
echo "🔍 VÉRIFICATION DE LA BASE DE DONNÉES\n";
echo "====================================\n\n";

// Chemin vers la base de données
$dbFile = __DIR__ . '/database/catalog.db';

echo "1. Vérification du fichier de base de données...\n";
if (file_exists($dbFile)) {
    echo "   ✅ Fichier trouvé: $dbFile\n";
    echo "   Taille: " . filesize($dbFile) . " octets\n";
} else {
    echo "   ❌ Fichier non trouvé\n";
    exit;
}

echo "\n2. Connexion à la base de données...\n";
try {
    $pdo = new PDO("sqlite:$dbFile");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✅ Connexion réussie\n";
} catch (Exception $e) {
    echo "   ❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit;
}

echo "\n3. Vérification du participant 12...\n";
try {
    $stmt = $pdo->prepare("SELECT * FROM participants WHERE id = 12");
    $stmt->execute();
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($participant) {
        echo "   ✅ Participant trouvé:\n";
        echo "      ID: {$participant['id']}\n";
        echo "      Nom: {$participant['first_name']} {$participant['last_name']}\n";
        echo "      Email: {$participant['email']}\n";
        echo "      Statut: {$participant['status']}\n";
        echo "      Training ID: {$participant['training_id']}\n";
    } else {
        echo "   ❌ Participant 12 non trouvé\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n4. Test de modification du statut...\n";
try {
    // Lire le statut actuel
    $stmt = $pdo->prepare("SELECT status FROM participants WHERE id = 12");
    $stmt->execute();
    $currentStatus = $stmt->fetchColumn();
    echo "   Statut actuel: $currentStatus\n";
    
    // Modifier le statut
    $newStatus = ($currentStatus === 'pending') ? 'confirmed' : 'pending';
    $stmt = $pdo->prepare("UPDATE participants SET status = ? WHERE id = 12");
    $result = $stmt->execute([$newStatus]);
    
    if ($result) {
        echo "   ✅ Modification réussie\n";
        echo "   Lignes affectées: " . $stmt->rowCount() . "\n";
        
        // Vérifier la modification
        $stmt = $pdo->prepare("SELECT status FROM participants WHERE id = 12");
        $stmt->execute();
        $updatedStatus = $stmt->fetchColumn();
        echo "   Nouveau statut: $updatedStatus\n";
        
        if ($updatedStatus === $newStatus) {
            echo "   ✅ Statut correctement mis à jour en base\n";
        } else {
            echo "   ❌ Statut non mis à jour en base\n";
        }
    } else {
        echo "   ❌ Échec de la modification\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n5. Tous les participants avec leurs statuts...\n";
try {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, status FROM participants ORDER BY id");
    $stmt->execute();
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "   Nombre de participants: " . count($participants) . "\n";
    foreach ($participants as $participant) {
        echo "   ID {$participant['id']}: {$participant['first_name']} {$participant['last_name']} - Statut: {$participant['status']}\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n✅ VÉRIFICATION TERMINÉE !\n";
?> 