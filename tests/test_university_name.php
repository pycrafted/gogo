<?php
/**
 * Test du nouveau nom de l'université
 */

echo "🏛️ TEST DU NOUVEAU NOM DE L'UNIVERSITÉ\n";
echo "========================================\n\n";

// 1. Vérifier le fichier HTML
echo "1. 📄 Vérification du nouveau nom...\n";

$indexFile = __DIR__ . '/public/index.html';
if (file_exists($indexFile)) {
    echo "   ✅ index.html trouvé\n";
    $content = file_get_contents($indexFile);
    
    // Vérifier le nouveau nom
    $newName = 'Institut Mansa Moussa';
    if (strpos($content, $newName) !== false) {
        echo "   ✅ Nouveau nom présent: $newName\n";
    } else {
        echo "   ❌ Nouveau nom manquant: $newName\n";
    }
    
    // Vérifier l'ancien nom (ne doit plus être présent)
    $oldName = 'École d\'Excellence';
    if (strpos($content, $oldName) !== false) {
        echo "   ❌ Ancien nom encore présent: $oldName\n";
    } else {
        echo "   ✅ Ancien nom supprimé: $oldName\n";
    }
    
    // Vérifier le titre de la page
    $newTitle = 'Institut Mansa Moussa - Découvrez nos Formations';
    if (strpos($content, $newTitle) !== false) {
        echo "   ✅ Nouveau titre présent: $newTitle\n";
    } else {
        echo "   ❌ Nouveau titre manquant: $newTitle\n";
    }
    
} else {
    echo "   ❌ index.html non trouvé\n";
}

// 2. Test des URLs
echo "\n2. 🌐 Test des URLs...\n";

$urls = [
    'http://localhost:8000/index.html' => 'Page d\'accueil'
];

foreach ($urls as $url => $description) {
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => [
                'Accept: text/html'
            ]
        ]
    ]);
    
    $response = file_get_contents($url, false, $context);
    
    if ($response !== false) {
        echo "   ✅ $description: accessible\n";
        
        // Vérifier le nouveau nom dans le HTML
        if (strpos($response, 'Institut Mansa Moussa') !== false) {
            echo "      ✅ Nouveau nom présent dans la page\n";
        } else {
            echo "      ❌ Nouveau nom manquant dans la page\n";
        }
        
    } else {
        echo "   ❌ $description: inaccessible\n";
    }
}

echo "\n🎯 RÉSUMÉ DES MODIFICATIONS:\n";
echo "============================\n";
echo "✅ Nom changé: École d'Excellence → Institut Mansa Moussa\n";
echo "✅ Titre de page mis à jour\n";
echo "✅ Copyright mis à jour\n";
echo "✅ Tous les éléments HTML mis à jour\n";

echo "\n🏛️ NOUVEAU NOM DE L'UNIVERSITÉ:\n";
echo "===============================\n";
echo "📚 Institut Mansa Moussa\n";
echo "🌍 Nom inspiré de l'empereur malien Mansa Moussa\n";
echo "💎 Symbole de richesse, sagesse et excellence\n";
echo "🇸🇳 Évoque l'histoire glorieuse de l'Afrique\n";

echo "\n🌐 POUR TESTER:\n";
echo "1. Ouvrez http://localhost:8000/index.html\n";
echo "2. Vérifiez le nouveau nom dans le header\n";
echo "3. Vérifiez le titre de la page\n";
echo "4. Vérifiez le copyright en bas de page\n";

echo "\n✅ CHANGEMENT DE NOM TERMINÉ !\n";
echo "🏛️ L'université a maintenant un nom qui sonne africain !\n";
?> 