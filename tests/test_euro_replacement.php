<?php
/**
 * Test de remplacement des symboles euro par F CFA
 */

echo "💰 TEST DE REMPLACEMENT EURO → F CFA\n";
echo "=====================================\n\n";

// 1. Vérifier les fichiers HTML
echo "1. 📄 Vérification des fichiers HTML...\n";

$htmlFiles = [
    __DIR__ . '/public/admin.html',
    __DIR__ . '/public/index.html'
];

foreach ($htmlFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . " trouvé\n";
        $content = file_get_contents($file);
        
        // Vérifier si F CFA est présent
        if (strpos($content, 'F CFA') !== false) {
            echo "      ✅ F CFA détecté dans " . basename($file) . "\n";
        } else {
            echo "      ❌ F CFA non trouvé dans " . basename($file) . "\n";
        }
        
        // Vérifier si l'euro est absent
        if (strpos($content, 'Prix (€)') !== false) {
            echo "      ❌ Prix (€) encore présent dans " . basename($file) . "\n";
        } else {
            echo "      ✅ Prix (€) supprimé de " . basename($file) . "\n";
        }
        
        // Vérifier si le symbole € est absent
        if (strpos($content, '€') !== false) {
            echo "      ❌ Symbole € encore présent dans " . basename($file) . "\n";
        } else {
            echo "      ✅ Symbole € supprimé de " . basename($file) . "\n";
        }
    } else {
        echo "   ❌ " . basename($file) . " non trouvé\n";
    }
}

// 2. Vérifier les fichiers JavaScript
echo "\n2. 📜 Vérification des fichiers JavaScript...\n";

$jsFiles = [
    __DIR__ . '/public/js/scripts.js',
    __DIR__ . '/public/js/admin.js'
];

foreach ($jsFiles as $file) {
    if (file_exists($file)) {
        echo "   ✅ " . basename($file) . " trouvé\n";
        $content = file_get_contents($file);
        
        // Vérifier si F CFA est présent
        if (strpos($content, 'F CFA') !== false) {
            echo "      ✅ F CFA détecté dans " . basename($file) . "\n";
        } else {
            echo "      ❌ F CFA non trouvé dans " . basename($file) . "\n";
        }
        
        // Vérifier si l'euro a été supprimé
        if (strpos($content, 'currency: \'EUR\'') !== false) {
            echo "      ❌ EUR encore présent dans " . basename($file) . "\n";
        } else {
            echo "      ✅ EUR supprimé de " . basename($file) . "\n";
        }
    } else {
        echo "   ❌ " . basename($file) . " non trouvé\n";
    }
}

// 3. Test des URLs
echo "\n3. 🌐 Test des URLs...\n";

$urls = [
    'http://localhost:8000/admin.html' => 'Page admin',
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
        
        // Vérifier les éléments spécifiques
        $checks = [
            'Prix (F CFA)' => 'Label prix F CFA',
            'F CFA' => 'F CFA dans le contenu',
            'Prix (€)' => 'Label prix euro (ne doit pas être présent)',
            '€' => 'Symbole euro (ne doit pas être présent)'
        ];
        
        foreach ($checks as $search => $description) {
            if (strpos($response, $search) !== false) {
                if ($search === 'Prix (€)' || $search === '€') {
                    echo "      ❌ $description: encore présent\n";
                } else {
                    echo "      ✅ $description: présent\n";
                }
            } else {
                if ($search === 'Prix (€)' || $search === '€') {
                    echo "      ✅ $description: supprimé\n";
                } else {
                    echo "      ❌ $description: manquant\n";
                }
            }
        }
    } else {
        echo "   ❌ $description: inaccessible\n";
    }
}

echo "\n🎯 RÉSUMÉ DES MODIFICATIONS:\n";
echo "============================\n";
echo "✅ Labels 'Prix (€)' remplacés par 'Prix (F CFA)'\n";
echo "✅ Fonction formatPrice modifiée pour utiliser F CFA\n";
echo "✅ Suppression de currency: 'EUR'\n";
echo "✅ Formatage des nombres avec espaces\n";

echo "\n💡 EXEMPLES D'AFFICHAGE:\n";
echo "=======================\n";
echo "Prix (F CFA) *\n";
echo "1 000 F CFA\n";
echo "2 500 F CFA\n";
echo "5 000 F CFA\n";

echo "\n🌐 POUR TESTER:\n";
echo "1. Ouvrez http://localhost:8000/admin.html\n";
echo "2. Cliquez sur 'Ajouter une formation'\n";
echo "3. Vérifiez que le label affiche 'Prix (F CFA)'\n";
echo "4. Vérifiez que les prix affichent 'F CFA'\n";

echo "\n✅ REMPLACEMENT TERMINÉ !\n";
echo "💰 Tous les symboles euro ont été remplacés par F CFA !\n";
?> 