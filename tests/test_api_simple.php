<?php
// Test simple de l'API
echo "Test API\n";

// Données de test
$data = [
    'training_id' => 1,
    'first_name' => 'sounatou',
    'last_name' => 'niane',
    'email' => 'sounatou@gmail.com',
    'phone' => '770123456',
    'company' => 'cmu',
    'position' => 'Développeur',
    'status' => 'confirmed', // Changement du statut
    'notes' => 'Test API'
];

// Créer le contexte
$context = stream_context_create([
    'http' => [
        'method' => 'PUT',
        'header' => [
            'Content-Type: application/json',
            'Accept: application/json'
        ],
        'content' => json_encode($data)
    ]
]);

echo "Envoi de la requête...\n";

// Appeler l'API
$response = file_get_contents('http://localhost:8000/api/participants.php/12', false, $context);

if ($response === false) {
    echo "Erreur: Impossible d'appeler l'API\n";
} else {
    echo "Réponse reçue:\n";
    echo $response . "\n";
    
    $data = json_decode($response, true);
    if ($data && isset($data['success']) && $data['success']) {
        echo "API appelée avec succès\n";
        if (isset($data['data']['status'])) {
            echo "Statut retourné: " . $data['data']['status'] . "\n";
        }
    } else {
        echo "Erreur API: " . ($data['message'] ?? 'Erreur inconnue') . "\n";
    }
}
?> 