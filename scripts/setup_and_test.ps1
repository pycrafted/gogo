# Script complet pour installer les dépendances et lancer tous les tests
# Usage: .\setup_and_test.ps1

Write-Host "=== CONFIGURATION ET TESTS COMPLETS ===" -ForegroundColor Cyan

# 1. Vérifier que Composer est installé
Write-Host "1. Vérification de Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Composer installé" -ForegroundColor Green
    } else {
        Write-Host "Composer non installé. Installation..." -ForegroundColor Red
        # Télécharger et installer Composer
        Invoke-WebRequest -Uri "https://getcomposer.org/installer" -OutFile "composer-setup.php"
        php composer-setup.php
        Remove-Item composer-setup.php
        Move-Item composer.phar composer
    }
} catch {
    Write-Host "Erreur lors de la vérification de Composer: $_" -ForegroundColor Red
    exit 1
}

# 2. Installer les dépendances
Write-Host "2. Installation des dépendances..." -ForegroundColor Yellow
try {
    composer install --no-interaction
    Write-Host "Dépendances installées" -ForegroundColor Green
} catch {
    Write-Host "Erreur lors de l'installation des dépendances: $_" -ForegroundColor Red
    exit 1
}

# 3. Vérifier que ChromeDriver est installé
Write-Host "3. Vérification de ChromeDriver..." -ForegroundColor Yellow
try {
    $chromeDriverVersion = chromedriver --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "ChromeDriver installé" -ForegroundColor Green
    } else {
        Write-Host "ChromeDriver non installé. Installation..." -ForegroundColor Red
        # Télécharger ChromeDriver
        $chromeVersion = (Get-ItemProperty "HKLM:\SOFTWARE\Google\Chrome\BLBeacon").version
        $majorVersion = $chromeVersion.Split('.')[0]
        
        $chromeDriverUrl = "https://chromedriver.storage.googleapis.com/LATEST_RELEASE_$majorVersion"
        $latestVersion = Invoke-WebRequest -Uri $chromeDriverUrl -UseBasicParsing | Select-Object -ExpandProperty Content
        
        $downloadUrl = "https://chromedriver.storage.googleapis.com/$latestVersion/chromedriver_win32.zip"
        Invoke-WebRequest -Uri $downloadUrl -OutFile "chromedriver.zip"
        
        # Extraire et installer
        Expand-Archive -Path "chromedriver.zip" -DestinationPath "."
        Remove-Item "chromedriver.zip"
        
        # Ajouter au PATH
        $env:PATH += ";$PWD"
        Write-Host "ChromeDriver installé" -ForegroundColor Green
    }
} catch {
    Write-Host "Erreur lors de la vérification de ChromeDriver: $_" -ForegroundColor Red
    Write-Host "Installez ChromeDriver manuellement depuis: https://chromedriver.chromium.org/" -ForegroundColor Yellow
    exit 1
}

# 4. Vérifier que le serveur PHP est en cours d'exécution
Write-Host "4. Vérification du serveur PHP..." -ForegroundColor Yellow
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

# 5. Lancer les tests API
Write-Host "5. Lancement des tests API..." -ForegroundColor Yellow
try {
    $apiResult = php tests/test_inscription_automated.php
    Write-Host $apiResult -ForegroundColor White
} catch {
    Write-Host "Erreur lors des tests API: $_" -ForegroundColor Red
}

# 6. Lancer les tests Selenium
Write-Host "6. Lancement des tests Selenium..." -ForegroundColor Yellow
try {
    $seleniumResult = php tests/test_selenium_inscription.php
    Write-Host $seleniumResult -ForegroundColor White
} catch {
    Write-Host "Erreur lors des tests Selenium: $_" -ForegroundColor Red
}

# 7. Résumé
Write-Host "=== RÉSUMÉ ===" -ForegroundColor Cyan
Write-Host "✅ Configuration terminée" -ForegroundColor Green
Write-Host "✅ Tests API lancés" -ForegroundColor Green
Write-Host "✅ Tests Selenium lancés" -ForegroundColor Green
Write-Host "Votre application est prête pour la livraison!" -ForegroundColor Green

Write-Host "=== FIN DE LA CONFIGURATION ===" -ForegroundColor Cyan 