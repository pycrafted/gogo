# Script PowerShell pour tester l'inscription automatisée
# Usage: .\test_inscription.ps1

Write-Host "=== LANCEMENT DU TEST D'INSCRIPTION AUTOMATISÉ ===" -ForegroundColor Cyan

# Vérifier que le serveur PHP est en cours d'exécution
Write-Host "Vérification du serveur PHP..." -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/trainings.php" -Method GET -TimeoutSec 5
    if ($response.StatusCode -eq 200) {
        Write-Host "Serveur PHP accessible" -ForegroundColor Green
    } else {
        Write-Host "Serveur PHP non accessible (Code: $($response.StatusCode))" -ForegroundColor Red
        Write-Host "Assurez-vous que le serveur PHP est démarré avec: php -S localhost:8000 -t public" -ForegroundColor Yellow
        exit 1
    }
} catch {
    Write-Host "Serveur PHP non accessible" -ForegroundColor Red
    Write-Host "Assurez-vous que le serveur PHP est démarré avec: php -S localhost:8000 -t public" -ForegroundColor Yellow
    exit 1
}

# Lancer le test PHP
Write-Host "Lancement du test d'inscription..." -ForegroundColor Yellow

$testScript = "tests/test_inscription_automated.php"

if (Test-Path $testScript) {
    try {
        $result = php $testScript
        Write-Host $result -ForegroundColor White
    } catch {
        Write-Host "Erreur lors de l'exécution du test: $_" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "Fichier de test non trouvé: $testScript" -ForegroundColor Red
    exit 1
}

Write-Host "=== FIN DU TEST ===" -ForegroundColor Cyan 