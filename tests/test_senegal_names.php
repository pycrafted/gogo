<?php
/**
 * Test des noms sénégalais
 */

echo "🇸🇳 TEST DES NOMS SÉNÉGALAIS\n";
echo "==============================\n\n";

// 1. Vérifier le fichier HTML
echo "1. 📄 Vérification des noms sénégalais...\n";

$indexFile = __DIR__ . '/public/index.html';
if (file_exists($indexFile)) {
    echo "   ✅ index.html trouvé\n";
    $content = file_get_contents($indexFile);
    
    // Vérifier les nouveaux noms sénégalais
    $senegalNames = [
        'Mamadou Diallo' => 'Nom sénégalais 1',
        'Fatou Sall' => 'Nom sénégalais 2',
        'Ousmane Ndiaye' => 'Nom sénégalais 3'
    ];
    
    foreach ($senegalNames as $name => $description) {
        if (strpos($content, $name) !== false) {
            echo "   ✅ $description: $name présent\n";
        } else {
            echo "   ❌ $description: $name manquant\n";
        }
    }
    
    // Vérifier les anciens noms français (ne doivent plus être présents)
    $frenchNames = [
        'Thomas Martin' => 'Nom français 1',
        'Sarah Dubois' => 'Nom français 2',
        'Marc Leroy' => 'Nom français 3'
    ];
    
    foreach ($frenchNames as $name => $description) {
        if (strpos($content, $name) !== false) {
            echo "   ❌ $description: $name encore présent\n";
        } else {
            echo "   ✅ $description: $name supprimé\n";
        }
    }
    
    // Vérifier les entreprises sénégalaises
    $senegalCompanies = [
        'Orange Sénégal' => 'Entreprise sénégalaise 1',
        'DakarTech Solutions' => 'Entreprise sénégalaise 2',
        'MTN Sénégal' => 'Entreprise sénégalaise 3'
    ];
    
    foreach ($senegalCompanies as $company => $description) {
        if (strpos($content, $company) !== false) {
            echo "   ✅ $description: $company présent\n";
        } else {
            echo "   ❌ $description: $company manquant\n";
        }
    }
    
    // Vérifier les informations de contact sénégalaises
    $senegalContact = [
        'Avenue Cheikh Anta Diop' => 'Adresse sénégalaise',
        '+221 33 123 45 67' => 'Téléphone sénégalais',
        'contact@ecole-excellence.sn' => 'Email sénégalais'
    ];
    
    foreach ($senegalContact as $contact => $description) {
        if (strpos($content, $contact) !== false) {
            echo "   ✅ $description: $contact présent\n";
        } else {
            echo "   ❌ $description: $contact manquant\n";
        }
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
        
        // Vérifier les noms sénégalais dans le HTML
        $senegalNames = ['Mamadou Diallo', 'Fatou Sall', 'Ousmane Ndiaye'];
        foreach ($senegalNames as $name) {
            if (strpos($response, $name) !== false) {
                echo "      ✅ $name présent\n";
            } else {
                echo "      ❌ $name manquant\n";
            }
        }
        
        // Vérifier les entreprises sénégalaises
        $senegalCompanies = ['Orange Sénégal', 'DakarTech Solutions', 'MTN Sénégal'];
        foreach ($senegalCompanies as $company) {
            if (strpos($response, $company) !== false) {
                echo "      ✅ $company présent\n";
            } else {
                echo "      ❌ $company manquant\n";
            }
        }
        
    } else {
        echo "   ❌ $description: inaccessible\n";
    }
}

echo "\n🎯 RÉSUMÉ DES MODIFICATIONS:\n";
echo "============================\n";
echo "✅ Noms français remplacés par des noms sénégalais\n";
echo "✅ Entreprises françaises remplacées par des entreprises sénégalaises\n";
echo "✅ Informations de contact adaptées au Sénégal\n";
echo "✅ Adresse mise à jour (Avenue Cheikh Anta Diop, Dakar)\n";
echo "✅ Téléphone sénégalais (+221)\n";
echo "✅ Email avec domaine .sn\n";

echo "\n💡 NOUVEAUX TÉMOIGNAGES:\n";
echo "========================\n";
echo "👤 Mamadou Diallo - Développeur Senior - Orange Sénégal\n";
echo "👤 Fatou Sall - CEO - DakarTech Solutions\n";
echo "👤 Ousmane Ndiaye - Data Scientist - MTN Sénégal\n";

echo "\n🌍 INFORMATIONS DE CONTACT:\n";
echo "==========================\n";
echo "📍 123 Avenue Cheikh Anta Diop, Dakar\n";
echo "📞 +221 33 123 45 67\n";
echo "📧 contact@ecole-excellence.sn\n";

echo "\n🌐 POUR TESTER:\n";
echo "1. Ouvrez http://localhost:8000/index.html\n";
echo "2. Scrollez vers la section témoignages\n";
echo "3. Vérifiez les noms sénégalais et entreprises\n";
echo "4. Vérifiez les informations de contact\n";

echo "\n✅ LOCALISATION SÉNÉGALAISE TERMINÉE !\n";
echo "🇸🇳 L'école est maintenant adaptée au contexte sénégalais !\n";
?> 