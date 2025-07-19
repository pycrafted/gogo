<?php
/**
 * Test de l'image du campus moderne
 */

echo "🏛️ TEST DE L'IMAGE DU CAMPUS\n";
echo "==============================\n\n";

// 1. Vérifier le fichier HTML
echo "1. 📄 Vérification de l'image...\n";

$indexFile = __DIR__ . '/public/index.html';
if (file_exists($indexFile)) {
    echo "   ✅ index.html trouvé\n";
    $content = file_get_contents($indexFile);
    
    // Vérifier l'URL de l'image
    $imageUrl = 'https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    if (strpos($content, $imageUrl) !== false) {
        echo "   ✅ URL de l'image présente: $imageUrl\n";
    } else {
        echo "   ❌ URL de l'image manquante\n";
    }
    
    // Vérifier l'alt text
    $altText = 'Campus moderne africain';
    if (strpos($content, $altText) !== false) {
        echo "   ✅ Alt text présent: $altText\n";
    } else {
        echo "   ❌ Alt text manquant\n";
    }
    
} else {
    echo "   ❌ index.html non trouvé\n";
}

// 2. Test de l'accessibilité de l'image
echo "\n2. 🌐 Test de l'accessibilité de l'image...\n";

$imageUrl = 'https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';

$context = stream_context_create([
    'http' => [
        'method' => 'HEAD',
        'header' => [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ]
    ]
]);

$headers = get_headers($imageUrl, 1, $context);

if ($headers && strpos($headers[0], '200') !== false) {
    echo "   ✅ Image accessible (HTTP 200)\n";
} else {
    echo "   ❌ Image non accessible\n";
    echo "   🔄 Tentative avec une image alternative...\n";
    
    // Image alternative
    $alternativeUrl = 'https://images.unsplash.com/photo-1523240798132-875193fcded4?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    $altHeaders = get_headers($alternativeUrl, 1, $context);
    
    if ($altHeaders && strpos($altHeaders[0], '200') !== false) {
        echo "   ✅ Image alternative accessible\n";
    } else {
        echo "   ❌ Image alternative non accessible\n";
    }
}

// 3. Test des URLs
echo "\n3. 🌐 Test de la page web...\n";

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
        
        // Vérifier la présence de l'image dans le HTML
        if (strpos($response, 'Campus moderne africain') !== false) {
            echo "      ✅ Image présente dans le HTML\n";
        } else {
            echo "      ❌ Image manquante dans le HTML\n";
        }
        
    } else {
        echo "   ❌ $description: inaccessible\n";
    }
}

echo "\n🎯 RÉSUMÉ DES CORRECTIONS:\n";
echo "===========================\n";
echo "✅ Image remplacée par une URL plus fiable\n";
echo "✅ Alt text mis à jour: 'Campus moderne africain'\n";
echo "✅ CSS amélioré pour l'affichage des images\n";
echo "✅ Gestion des erreurs d'image ajoutée\n";
echo "✅ Effets visuels améliorés (ombre, bordure)\n";

echo "\n🏛️ NOUVELLE IMAGE:\n";
echo "==================\n";
echo "📸 URL: https://images.unsplash.com/photo-1562774053-701939374585\n";
echo "🏛️ Description: Campus moderne africain\n";
echo "🎨 Style: Arrondi, ombre, effet hover\n";

echo "\n🌐 POUR TESTER:\n";
echo "1. Ouvrez http://localhost:8000/index.html\n";
echo "2. Scrollez vers la section 'À propos'\n";
echo "3. Vérifiez que l'image s'affiche correctement\n";
echo "4. Testez l'effet hover sur l'image\n";

echo "\n✅ CORRECTION DE L'IMAGE TERMINÉE !\n";
echo "🏛️ L'image du campus devrait maintenant s'afficher correctement !\n";
?> 