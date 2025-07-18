<?php
/**
 * Test PowerShell de l'API
 */

echo "🧪 Test PowerShell de l'API...\n";

// Créer le script PowerShell
$powershellScript = '
$headers = @{
    "Content-Type" = "application/json"
}

$body = @{
    training_id = 1
    first_name = "Test"
    last_name = "PowerShell"
    email = "testpowershell@example.com"
    phone = "0123456789"
    company = "TestCorp"
    position = "Développeur"
    notes = "Test PowerShell"
} | ConvertTo-Json

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/participants.php" -Method POST -Headers $headers -Body $body
    Write-Host "Status: $($response.StatusCode)"
    Write-Host "Response: $($response.Content)"
} catch {
    Write-Host "Error: $($_.Exception.Message)"
    Write-Host "Response: $($_.Exception.Response)"
}
';

// Écrire le script PowerShell
file_put_contents('test_api.ps1', $powershellScript);

echo "📝 Script PowerShell créé: test_api.ps1\n";
echo "🚀 Exécution du script...\n";

// Exécuter le script PowerShell
$output = shell_exec('powershell -ExecutionPolicy Bypass -File test_api.ps1 2>&1');
echo $output;

echo "\n🎉 Test terminé !\n";
?> 