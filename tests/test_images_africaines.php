<?php
/**
 * Test des images africaines
 */

echo "🌍 TEST DES IMAGES AFRICAINES\n";
echo "==============================\n\n";

// 1. Vérifier le fichier HTML
echo "1. 📄 Vérification des images africaines...\n";

$indexFile = __DIR__ . '/public/index.html';
$cssFile = __DIR__ . '/public/css/styles.css';

if (file_exists($indexFile)) {
    echo "   ✅ index.html trouvé\n";
    $content = file_get_contents($indexFile);
    
    // Vérifier l'URL de l'image africaine
    $imageUrl = 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    if (strpos($content, $imageUrl) !== false) {
        echo "   ✅ Image africaine présente: $imageUrl\n";
    } else {
        echo "   ❌ Image africaine manquante\n";
    }
    
    // Vérifier l'alt text africain
    $altText = 'Université africaine moderne';
    if (strpos($content, $altText) !== false) {
        echo "   ✅ Alt text africain présent: $altText\n";
    } else {
        echo "   ❌ Alt text africain manquant\n";
    }
    
} else {
    echo "   ❌ index.html non trouvé\n";
}

// 2. Vérifier le CSS
if (file_exists($cssFile)) {
    echo "   ✅ styles.css trouvé\n";
    $cssContent = file_get_contents($cssFile);
    
    // Vérifier l'image de fond africaine
    $bgImageUrl = 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
    if (strpos($cssContent, $bgImageUrl) !== false) {
        echo "   ✅ Image de fond africaine présente dans CSS\n";
    } else {
        echo "   ❌ Image de fond africaine manquante dans CSS\n";
    }
    
} else {
    echo "   ❌ styles.css non trouvé\n";
}

// 3. Test de l'accessibilité des images
echo "\n2. 🌐 Test de l'accessibilité des images...\n";

$imageUrls = [
    'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' => 'Image section À propos',
    'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' => 'Image de fond Hero'
];

foreach ($imageUrls as $url => $description) {
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]
    ]);
    
    $headers = get_headers($url, 1, $context);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "   ✅ $description: accessible (HTTP 200)\n";
    } else {
        echo "   ❌ $description: non accessible\n";
    }
}

// 4. Test des URLs
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
        
        // Vérifier la présence de l'image africaine dans le HTML
        if (strpos($response, 'Université africaine moderne') !== false) {
            echo "      ✅ Image africaine présente dans le HTML\n";
        } else {
            echo "      ❌ Image africaine manquante dans le HTML\n";
        }
        
    } else {
        echo "   ❌ $description: inaccessible\n";
    }
}

echo "\n🎯 RÉSUMÉ DES MODIFICATIONS:\n";
echo "============================\n";
echo "✅ Image remplacée par une image plus africaine\n";
echo "✅ Alt text mis à jour: 'Université africaine moderne'\n";
echo "✅ Image de fond Hero mise à jour\n";
echo "✅ Contexte visuel plus adapté à l'Afrique\n";

echo "\n🌍 NOUVELLES IMAGES AFRICAINES:\n";
echo "===============================\n";
echo "📸 Image À propos: https://images.unsplash.com/photo-1571260899304-425eee4c7efc\n";
echo "🏛️ Description: Université africaine moderne\n";
echo "🎨 Style: Contexte africain authentique\n";
echo "🌍 Thème: Éducation et développement africain\n";

echo "\n🌐 POUR TESTER:\n";
echo "1. Ouvrez http://localhost:8000/index.html\n";
echo "2. Vérifiez l'image de fond du Hero (plus africaine)\n";
echo "3. Scrollez vers la section 'À propos'\n";
echo "4. Vérifiez que l'image évoque mieux l'Afrique\n";

echo "\n✅ IMAGES AFRICAINES TERMINÉES !\n";
echo "🌍 L'université a maintenant un visuel plus authentiquement africain !\n";
?> 
/**
 * Test des images africaines
 */

echo "🌍 TEST DES IMAGES AFRICAINES\n";
echo "==============================\n\n";

// 1. Vérifier le fichier HTML
echo "1. 📄 Vérification des images africaines...\n";

$indexFile = __DIR__ . '/public/index.html';
$cssFile = __DIR__ . '/public/css/styles.css';

if (file_exists($indexFile)) {
    echo "   ✅ index.html trouvé\n";
    $content = file_get_contents($indexFile);
    
    // Vérifier l'URL de l'image africaine
    $imageUrl = 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    if (strpos($content, $imageUrl) !== false) {
        echo "   ✅ Image africaine présente: $imageUrl\n";
    } else {
        echo "   ❌ Image africaine manquante\n";
    }
    
    // Vérifier l'alt text africain
    $altText = 'Université africaine moderne';
    if (strpos($content, $altText) !== false) {
        echo "   ✅ Alt text africain présent: $altText\n";
    } else {
        echo "   ❌ Alt text africain manquant\n";
    }
    
} else {
    echo "   ❌ index.html non trouvé\n";
}

// 2. Vérifier le CSS
if (file_exists($cssFile)) {
    echo "   ✅ styles.css trouvé\n";
    $cssContent = file_get_contents($cssFile);
    
    // Vérifier l'image de fond africaine
    $bgImageUrl = 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
    if (strpos($cssContent, $bgImageUrl) !== false) {
        echo "   ✅ Image de fond africaine présente dans CSS\n";
    } else {
        echo "   ❌ Image de fond africaine manquante dans CSS\n";
    }
    
} else {
    echo "   ❌ styles.css non trouvé\n";
}

// 3. Test de l'accessibilité des images
echo "\n2. 🌐 Test de l'accessibilité des images...\n";

$imageUrls = [
    'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' => 'Image section À propos',
    'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' => 'Image de fond Hero'
];

foreach ($imageUrls as $url => $description) {
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]
    ]);
    
    $headers = get_headers($url, 1, $context);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "   ✅ $description: accessible (HTTP 200)\n";
    } else {
        echo "   ❌ $description: non accessible\n";
    }
}

// 4. Test des URLs
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
        
        // Vérifier la présence de l'image africaine dans le HTML
        if (strpos($response, 'Université africaine moderne') !== false) {
            echo "      ✅ Image africaine présente dans le HTML\n";
        } else {
            echo "      ❌ Image africaine manquante dans le HTML\n";
        }
        
    } else {
        echo "   ❌ $description: inaccessible\n";
    }
}

echo "\n🎯 RÉSUMÉ DES MODIFICATIONS:\n";
echo "============================\n";
echo "✅ Image remplacée par une image plus africaine\n";
echo "✅ Alt text mis à jour: 'Université africaine moderne'\n";
echo "✅ Image de fond Hero mise à jour\n";
echo "✅ Contexte visuel plus adapté à l'Afrique\n";

echo "\n🌍 NOUVELLES IMAGES AFRICAINES:\n";
echo "===============================\n";
echo "📸 Image À propos: https://images.unsplash.com/photo-1571260899304-425eee4c7efc\n";
echo "🏛️ Description: Université africaine moderne\n";
echo "🎨 Style: Contexte africain authentique\n";
echo "🌍 Thème: Éducation et développement africain\n";

echo "\n🌐 POUR TESTER:\n";
echo "1. Ouvrez http://localhost:8000/index.html\n";
echo "2. Vérifiez l'image de fond du Hero (plus africaine)\n";
echo "3. Scrollez vers la section 'À propos'\n";
echo "4. Vérifiez que l'image évoque mieux l'Afrique\n";

echo "\n✅ IMAGES AFRICAINES TERMINÉES !\n";
echo "🌍 L'université a maintenant un visuel plus authentiquement africain !\n";
?> 
/**
 * Test des images africaines
 */

echo "🌍 TEST DES IMAGES AFRICAINES\n";
echo "==============================\n\n";

// 1. Vérifier le fichier HTML
echo "1. 📄 Vérification des images africaines...\n";

$indexFile = __DIR__ . '/public/index.html';
$cssFile = __DIR__ . '/public/css/styles.css';

if (file_exists($indexFile)) {
    echo "   ✅ index.html trouvé\n";
    $content = file_get_contents($indexFile);
    
    // Vérifier l'URL de l'image africaine
    $imageUrl = 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    if (strpos($content, $imageUrl) !== false) {
        echo "   ✅ Image africaine présente: $imageUrl\n";
    } else {
        echo "   ❌ Image africaine manquante\n";
    }
    
    // Vérifier l'alt text africain
    $altText = 'Université africaine moderne';
    if (strpos($content, $altText) !== false) {
        echo "   ✅ Alt text africain présent: $altText\n";
    } else {
        echo "   ❌ Alt text africain manquant\n";
    }
    
} else {
    echo "   ❌ index.html non trouvé\n";
}

// 2. Vérifier le CSS
if (file_exists($cssFile)) {
    echo "   ✅ styles.css trouvé\n";
    $cssContent = file_get_contents($cssFile);
    
    // Vérifier l'image de fond africaine
    $bgImageUrl = 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
    if (strpos($cssContent, $bgImageUrl) !== false) {
        echo "   ✅ Image de fond africaine présente dans CSS\n";
    } else {
        echo "   ❌ Image de fond africaine manquante dans CSS\n";
    }
    
} else {
    echo "   ❌ styles.css non trouvé\n";
}

// 3. Test de l'accessibilité des images
echo "\n2. 🌐 Test de l'accessibilité des images...\n";

$imageUrls = [
    'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' => 'Image section À propos',
    'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' => 'Image de fond Hero'
];

foreach ($imageUrls as $url => $description) {
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]
    ]);
    
    $headers = get_headers($url, 1, $context);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "   ✅ $description: accessible (HTTP 200)\n";
    } else {
        echo "   ❌ $description: non accessible\n";
    }
}

// 4. Test des URLs
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
        
        // Vérifier la présence de l'image africaine dans le HTML
        if (strpos($response, 'Université africaine moderne') !== false) {
            echo "      ✅ Image africaine présente dans le HTML\n";
        } else {
            echo "      ❌ Image africaine manquante dans le HTML\n";
        }
        
    } else {
        echo "   ❌ $description: inaccessible\n";
    }
}

echo "\n🎯 RÉSUMÉ DES MODIFICATIONS:\n";
echo "============================\n";
echo "✅ Image remplacée par une image plus africaine\n";
echo "✅ Alt text mis à jour: 'Université africaine moderne'\n";
echo "✅ Image de fond Hero mise à jour\n";
echo "✅ Contexte visuel plus adapté à l'Afrique\n";

echo "\n🌍 NOUVELLES IMAGES AFRICAINES:\n";
echo "===============================\n";
echo "📸 Image À propos: https://images.unsplash.com/photo-1571260899304-425eee4c7efc\n";
echo "🏛️ Description: Université africaine moderne\n";
echo "🎨 Style: Contexte africain authentique\n";
echo "🌍 Thème: Éducation et développement africain\n";

echo "\n🌐 POUR TESTER:\n";
echo "1. Ouvrez http://localhost:8000/index.html\n";
echo "2. Vérifiez l'image de fond du Hero (plus africaine)\n";
echo "3. Scrollez vers la section 'À propos'\n";
echo "4. Vérifiez que l'image évoque mieux l'Afrique\n";

echo "\n✅ IMAGES AFRICAINES TERMINÉES !\n";
echo "🌍 L'université a maintenant un visuel plus authentiquement africain !\n";
?> 
/**
 * Test des images africaines
 */

echo "🌍 TEST DES IMAGES AFRICAINES\n";
echo "==============================\n\n";

// 1. Vérifier le fichier HTML
echo "1. 📄 Vérification des images africaines...\n";

$indexFile = __DIR__ . '/public/index.html';
$cssFile = __DIR__ . '/public/css/styles.css';

if (file_exists($indexFile)) {
    echo "   ✅ index.html trouvé\n";
    $content = file_get_contents($indexFile);
    
    // Vérifier l'URL de l'image africaine
    $imageUrl = 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    if (strpos($content, $imageUrl) !== false) {
        echo "   ✅ Image africaine présente: $imageUrl\n";
    } else {
        echo "   ❌ Image africaine manquante\n";
    }
    
    // Vérifier l'alt text africain
    $altText = 'Université africaine moderne';
    if (strpos($content, $altText) !== false) {
        echo "   ✅ Alt text africain présent: $altText\n";
    } else {
        echo "   ❌ Alt text africain manquant\n";
    }
    
} else {
    echo "   ❌ index.html non trouvé\n";
}

// 2. Vérifier le CSS
if (file_exists($cssFile)) {
    echo "   ✅ styles.css trouvé\n";
    $cssContent = file_get_contents($cssFile);
    
    // Vérifier l'image de fond africaine
    $bgImageUrl = 'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80';
    if (strpos($cssContent, $bgImageUrl) !== false) {
        echo "   ✅ Image de fond africaine présente dans CSS\n";
    } else {
        echo "   ❌ Image de fond africaine manquante dans CSS\n";
    }
    
} else {
    echo "   ❌ styles.css non trouvé\n";
}

// 3. Test de l'accessibilité des images
echo "\n2. 🌐 Test de l'accessibilité des images...\n";

$imageUrls = [
    'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' => 'Image section À propos',
    'https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' => 'Image de fond Hero'
];

foreach ($imageUrls as $url => $description) {
    $context = stream_context_create([
        'http' => [
            'method' => 'HEAD',
            'header' => [
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ]
        ]
    ]);
    
    $headers = get_headers($url, 1, $context);
    
    if ($headers && strpos($headers[0], '200') !== false) {
        echo "   ✅ $description: accessible (HTTP 200)\n";
    } else {
        echo "   ❌ $description: non accessible\n";
    }
}

// 4. Test des URLs
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
        
        // Vérifier la présence de l'image africaine dans le HTML
        if (strpos($response, 'Université africaine moderne') !== false) {
            echo "      ✅ Image africaine présente dans le HTML\n";
        } else {
            echo "      ❌ Image africaine manquante dans le HTML\n";
        }
        
    } else {
        echo "   ❌ $description: inaccessible\n";
    }
}

echo "\n🎯 RÉSUMÉ DES MODIFICATIONS:\n";
echo "============================\n";
echo "✅ Image remplacée par une image plus africaine\n";
echo "✅ Alt text mis à jour: 'Université africaine moderne'\n";
echo "✅ Image de fond Hero mise à jour\n";
echo "✅ Contexte visuel plus adapté à l'Afrique\n";

echo "\n🌍 NOUVELLES IMAGES AFRICAINES:\n";
echo "===============================\n";
echo "📸 Image À propos: https://images.unsplash.com/photo-1571260899304-425eee4c7efc\n";
echo "🏛️ Description: Université africaine moderne\n";
echo "🎨 Style: Contexte africain authentique\n";
echo "🌍 Thème: Éducation et développement africain\n";

echo "\n🌐 POUR TESTER:\n";
echo "1. Ouvrez http://localhost:8000/index.html\n";
echo "2. Vérifiez l'image de fond du Hero (plus africaine)\n";
echo "3. Scrollez vers la section 'À propos'\n";
echo "4. Vérifiez que l'image évoque mieux l'Afrique\n";

echo "\n✅ IMAGES AFRICAINES TERMINÉES !\n";
echo "🌍 L'université a maintenant un visuel plus authentiquement africain !\n";
?> 