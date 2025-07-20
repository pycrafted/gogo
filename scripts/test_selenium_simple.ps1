# Script pour lancer le test Selenium simplifié
# Usage: .\test_selenium_simple.ps1

Write-Host "=== TEST SELENIUM SIMPLIFIÉ ===" -ForegroundColor Cyan

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

# Vérifier ChromeDriver
Write-Host "Vérification de ChromeDriver..." -ForegroundColor Yellow

$chromeDriverPaths = @(
    "chromedriver.exe",
    ".\chromedriver.exe",
    "C:\Windows\System32\chromedriver.exe"
)

$chromeDriverFound = $false
foreach ($path in $chromeDriverPaths) {
    if (Test-Path $path) {
        Write-Host "ChromeDriver trouvé: $path" -ForegroundColor Green
        $chromeDriverFound = $true
        break
    }
}

if (-not $chromeDriverFound) {
    Write-Host "ChromeDriver non trouvé" -ForegroundColor Red
    Write-Host "Téléchargez ChromeDriver depuis: https://chromedriver.chromium.org/" -ForegroundColor Yellow
    Write-Host "Placez chromedriver.exe dans le répertoire du projet" -ForegroundColor Yellow
    Write-Host "Ou utilisez le test simple: .\scripts\test_simple.ps1" -ForegroundColor Yellow
    exit 1
}

# Lancer le test Selenium
Write-Host "Lancement du test Selenium..." -ForegroundColor Yellow

if (Test-Path "tests/test_selenium_simple.php") {
    try {
        $result = php tests/test_selenium_simple.php
        Write-Host $result -ForegroundColor White
    } catch {
        Write-Host "Erreur lors de l'exécution du test Selenium: $_" -ForegroundColor Red
    }
} else {
    Write-Host "Fichier de test Selenium non trouvé: tests/test_selenium_simple.php" -ForegroundColor Red
}

Write-Host "=== FIN DU TEST SELENIUM ===" -ForegroundColor Cyan 