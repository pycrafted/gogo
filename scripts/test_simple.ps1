# Script simple pour tester l'inscription sans dépendances complexes
# Usage: .\test_simple.ps1

Write-Host "=== TEST SIMPLE D'INSCRIPTION ===" -ForegroundColor Cyan

# Vérifier que le serveur PHP est en cours d'exécution
Write-Host "Vérification du serveur PHP..." -ForegroundColor Yellow

try {
    $response = Invoke-WebRequest -Uri "http://localhost:8000/api/trainings.php" -Method GET -TimeoutSec 5
    if ($response.StatusCode -eq 200) {
        Write-Host "Serveur PHP accessible" -ForegroundColor Green
    } else {
        Write-Host "Serveur PHP non accessible (Code: $($response.StatusCode))" -ForegroundColor Red
        Write-Host "Démarrage du serveur PHP..." -ForegroundColor Yellow
        Start-Process -FilePath "php" -ArgumentList "-S", "localhost:8000", "-t", "public" -WindowStyle Hidden
        Start-Sleep -Seconds 3
    }
} catch {
    Write-Host "Serveur PHP non accessible. Démarrage..." -ForegroundColor Red
    Start-Process -FilePath "php" -ArgumentList "-S", "localhost:8000", "-t", "public" -WindowStyle Hidden
    Start-Sleep -Seconds 3
}

# Lancer le test simple
Write-Host "Lancement du test simple..." -ForegroundColor Yellow

if (Test-Path "tests/test_simple_inscription.php") {
    try {
        $result = php tests/test_simple_inscription.php
        Write-Host $result -ForegroundColor White
    } catch {
        Write-Host "Erreur lors de l'exécution du test: $_" -ForegroundColor Red
    }
} else {
    Write-Host "Fichier de test non trouvé: tests/test_simple_inscription.php" -ForegroundColor Red
}

Write-Host "=== FIN DU TEST SIMPLE ===" -ForegroundColor Cyan 