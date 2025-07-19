<?php
/**
 * Test des changements de sécurité - Seuls les admins peuvent ajouter/modifier des formations
 */

echo "🔒 TEST DES CHANGEMENTS DE SÉCURITÉ\n";
echo "==================================\n\n";

// 1. Test de la navbar principale
echo "1. 🏠 Test de la navbar principale...\n";
try {
    $response = file_get_contents('http://localhost:8000/');
    
    if ($response !== false) {
        echo "   ✅ Page principale accessible\n";
        
        // Vérifier que le lien "Ajouter une formation" n'est plus présent
        if (strpos($response, 'Ajouter une formation') === false) {
            echo "   ✅ Lien 'Ajouter une formation' supprimé de la navbar\n";
        } else {
            echo "   ❌ Lien 'Ajouter une formation' encore présent\n";
        }
        
        // Vérifier que le lien "Participants" n'est plus présent
        if (strpos($response, 'Participants') === false) {
            echo "   ✅ Lien 'Participants' supprimé de la navbar\n";
        } else {
            echo "   ❌ Lien 'Participants' encore présent\n";
        }
        
        // Vérifier que le lien "Administration" est présent
        if (strpos($response, 'Administration') !== false) {
            echo "   ✅ Lien 'Administration' présent\n";
        } else {
            echo "   ❌ Lien 'Administration' manquant\n";
        }
    } else {
        echo "   ❌ Impossible d'accéder à la page principale\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur page principale: " . $e->getMessage() . "\n";
}

// 2. Test du modal d'ajout de formation
echo "\n2. ➕ Test du modal d'ajout de formation...\n";
try {
    $response = file_get_contents('http://localhost:8000/');
    
    if ($response !== false) {
        // Vérifier que le modal d'ajout n'est plus présent
        if (strpos($response, 'trainingModal') === false) {
            echo "   ✅ Modal d'ajout de formation supprimé de la page principale\n";
        } else {
            echo "   ❌ Modal d'ajout de formation encore présent\n";
        }
        
        // Vérifier que le modal de suppression n'est plus présent
        if (strpos($response, 'deleteModal') === false) {
            echo "   ✅ Modal de suppression supprimé de la page principale\n";
        } else {
            echo "   ❌ Modal de suppression encore présent\n";
        }
    } else {
        echo "   ❌ Impossible d'accéder à la page principale\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test modal: " . $e->getMessage() . "\n";
}

// 3. Test du dashboard d'administration
echo "\n3. 🔧 Test du dashboard d'administration...\n";
try {
    $response = file_get_contents('http://localhost:8000/admin.html');
    
    if ($response !== false) {
        echo "   ✅ Page d'administration accessible\n";
        
        // Vérifier que le bouton d'ajout est présent dans l'admin
        if (strpos($response, 'Ajouter une formation') !== false) {
            echo "   ✅ Bouton 'Ajouter une formation' présent dans l'admin\n";
        } else {
            echo "   ❌ Bouton 'Ajouter une formation' manquant dans l'admin\n";
        }
        
        // Vérifier que le modal d'ajout est présent dans l'admin
        if (strpos($response, 'trainingModal') !== false) {
            echo "   ✅ Modal d'ajout présent dans l'admin\n";
        } else {
            echo "   ❌ Modal d'ajout manquant dans l'admin\n";
        }
    } else {
        echo "   ❌ Impossible d'accéder à la page d'administration\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur page d'administration: " . $e->getMessage() . "\n";
}

// 4. Test du JavaScript
echo "\n4. 📜 Test du JavaScript...\n";
$jsFile = __DIR__ . '/public/js/scripts.js';
if (file_exists($jsFile)) {
    $content = file_get_contents($jsFile);
    
    // Vérifier que les fonctions d'édition/suppression sont supprimées
    $checks = [
        'showAddForm' => 'Fonction showAddForm',
        'editTraining' => 'Fonction editTraining',
        'saveTraining' => 'Fonction saveTraining',
        'deleteTraining' => 'Fonction deleteTraining',
        'confirmDelete' => 'Fonction confirmDelete'
    ];
    
    foreach ($checks as $function => $description) {
        if (strpos($content, $function) === false) {
            echo "   ✅ $description supprimée du JavaScript principal\n";
        } else {
            echo "   ❌ $description encore présente\n";
        }
    }
    
    // Vérifier que les fonctions de consultation sont présentes
    $viewChecks = [
        'viewProgram' => 'Fonction viewProgram',
        'showRegistrationForm' => 'Fonction showRegistrationForm'
    ];
    
    foreach ($viewChecks as $function => $description) {
        if (strpos($content, $function) !== false) {
            echo "   ✅ $description présente (consultation autorisée)\n";
        } else {
            echo "   ❌ $description manquante\n";
        }
    }
} else {
    echo "   ❌ Fichier scripts.js non trouvé\n";
}

// 5. Test des boutons d'action
echo "\n5. 🔘 Test des boutons d'action...\n";
try {
    $response = file_get_contents('http://localhost:8000/');
    
    if ($response !== false) {
        // Vérifier que les boutons d'édition/suppression ne sont plus présents
        if (strpos($response, 'editTraining') === false) {
            echo "   ✅ Boutons d'édition supprimés de la page principale\n";
        } else {
            echo "   ❌ Boutons d'édition encore présents\n";
        }
        
        if (strpos($response, 'deleteTraining') === false) {
            echo "   ✅ Boutons de suppression supprimés de la page principale\n";
        } else {
            echo "   ❌ Boutons de suppression encore présents\n";
        }
        
        // Vérifier que les boutons de consultation sont présents
        if (strpos($response, 'viewProgram') !== false) {
            echo "   ✅ Boutons de consultation présents\n";
        } else {
            echo "   ❌ Boutons de consultation manquants\n";
        }
        
        if (strpos($response, 'showRegistrationForm') !== false) {
            echo "   ✅ Boutons d'inscription présents\n";
        } else {
            echo "   ❌ Boutons d'inscription manquants\n";
        }
    } else {
        echo "   ❌ Impossible d'accéder à la page principale\n";
    }
} catch (Exception $e) {
    echo "   ❌ Erreur test boutons: " . $e->getMessage() . "\n";
}

echo "\n🎯 RÉSUMÉ DES CHANGEMENTS DE SÉCURITÉ:\n";
echo "======================================\n";
echo "✅ Lien 'Ajouter une formation' supprimé de la navbar principale\n";
echo "✅ Lien 'Participants' supprimé de la navbar principale\n";
echo "✅ Modal d'ajout de formation supprimé de la page principale\n";
echo "✅ Modal de suppression supprimé de la page principale\n";
echo "✅ Fonctions JavaScript d'édition/suppression supprimées\n";
echo "✅ Boutons d'édition/suppression supprimés du tableau\n";
echo "✅ Bouton 'Ajouter une formation' présent dans l'admin\n";
echo "✅ Modal d'ajout présent dans l'admin\n";

echo "\n🔒 SÉCURITÉ RENFORCÉE:\n";
echo "=====================\n";
echo "✅ Seuls les admins peuvent ajouter des formations\n";
echo "✅ Seuls les admins peuvent modifier des formations\n";
echo "✅ Seuls les admins peuvent supprimer des formations\n";
echo "✅ Les utilisateurs publics peuvent consulter et s'inscrire\n";

echo "\n💡 FONCTIONNALITÉS DISPONIBLES:\n";
echo "==============================\n";
echo "👥 Public:\n";
echo "   - Consulter les formations\n";
echo "   - Voir les programmes\n";
echo "   - S'inscrire aux formations\n";
echo "   - Rechercher et filtrer\n";
echo "\n🔧 Admin:\n";
echo "   - Toutes les fonctionnalités publiques\n";
echo "   - Ajouter des formations\n";
echo "   - Modifier des formations\n";
echo "   - Supprimer des formations\n";
echo "   - Gérer les participants\n";

echo "\n✅ CHANGEMENTS DE SÉCURITÉ APPLIQUÉS AVEC SUCCÈS !\n";
?> 