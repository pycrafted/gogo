# Script complet : Téléchargement ChromeDriver + Test Selenium
# Usage: .\test_complet_selenium.ps1

Write-Host "=== TEST COMPLET SELENIUM ===" -ForegroundColor Cyan

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
        try {
            $version = & $path --version
            Write-Host "Version: $version" -ForegroundColor Green
        } catch {
            Write-Host "Impossible de vérifier la version" -ForegroundColor Yellow
        }
        $chromeDriverFound = $true
        break
    }
}

# Si ChromeDriver n'est pas trouvé, le télécharger
if (-not $chromeDriverFound) {
    Write-Host "ChromeDriver non trouvé. Téléchargement automatique..." -ForegroundColor Yellow
    
    # Configuration
    $chromeDriverVersion = "138.0.7204.157"
    $chromeDriverUrl = "https://storage.googleapis.com/chrome-for-testing-public/$chromeDriverVersion/win64/chromedriver-win64.zip"
    $downloadPath = "chromedriver-win64.zip"
    $extractPath = "chromedriver-win64"
    $finalPath = "chromedriver.exe"
    
    Write-Host "Version ChromeDriver: $chromeDriverVersion" -ForegroundColor Yellow
    Write-Host "URL de téléchargement: $chromeDriverUrl" -ForegroundColor Yellow
    
    # Télécharger ChromeDriver
    Write-Host "Téléchargement de ChromeDriver..." -ForegroundColor Yellow
    
    try {
        $webClient = New-Object System.Net.WebClient
        $webClient.DownloadFile($chromeDriverUrl, $downloadPath)
        Write-Host "✅ Téléchargement réussi: $downloadPath" -ForegroundColor Green
    } catch {
        Write-Host "❌ Erreur lors du téléchargement: $_" -ForegroundColor Red
        Write-Host "Utilisez le test simple: .\scripts\test_simple.ps1" -ForegroundColor Yellow
        exit 1
    }
    
    # Vérifier que le fichier a été téléchargé
    if (-not (Test-Path $downloadPath)) {
        Write-Host "❌ Fichier de téléchargement non trouvé" -ForegroundColor Red
        exit 1
    }
    
    Write-Host "Taille du fichier: $((Get-Item $downloadPath).Length) bytes" -ForegroundColor Yellow
    
    # Extraire le fichier ZIP
    Write-Host "Extraction du fichier ZIP..." -ForegroundColor Yellow
    
    try {
        Expand-Archive -Path $downloadPath -DestinationPath "." -Force
        Write-Host "✅ Extraction réussie" -ForegroundColor Green
    } catch {
        Write-Host "❌ Erreur lors de l'extraction: $_" -ForegroundColor Red
        exit 1
    }
    
    # Vérifier que l'extraction a fonctionné
    if (-not (Test-Path $extractPath)) {
        Write-Host "❌ Dossier d'extraction non trouvé" -ForegroundColor Red
        exit 1
    }
    
    # Lister le contenu du dossier extrait pour debug
    Write-Host "Contenu du dossier extrait:" -ForegroundColor Yellow
    Get-ChildItem -Path $extractPath -Recurse | ForEach-Object {
        Write-Host "  - $($_.FullName)" -ForegroundColor Gray
    }
    
    # Chercher chromedriver.exe dans le dossier extrait (méthode simplifiée)
    $sourcePath = Join-Path $extractPath "chromedriver.exe"
    
    if (Test-Path $sourcePath) {
        Write-Host "ChromeDriver trouvé: $sourcePath" -ForegroundColor Green
        
        # Copier vers le répertoire racine
        try {
            Copy-Item -Path $sourcePath -Destination $finalPath -Force
            Write-Host "✅ ChromeDriver installé: $finalPath" -ForegroundColor Green
        } catch {
            Write-Host "❌ Erreur lors de la copie: $_" -ForegroundColor Red
            exit 1
        }
    } else {
        Write-Host "❌ ChromeDriver non trouvé dans l'archive: $sourcePath" -ForegroundColor Red
        exit 1
    }
    
    # Nettoyer les fichiers temporaires
    Write-Host "Nettoyage des fichiers temporaires..." -ForegroundColor Yellow
    
    try {
        Remove-Item -Path $downloadPath -Force -ErrorAction SilentlyContinue
        Remove-Item -Path $extractPath -Recurse -Force -ErrorAction SilentlyContinue
        Write-Host "✅ Nettoyage terminé" -ForegroundColor Green
    } catch {
        Write-Host "⚠️ Erreur lors du nettoyage: $_" -ForegroundColor Yellow
    }
    
    # Vérifier l'installation
    Write-Host "Vérification de l'installation..." -ForegroundColor Yellow
    
    if (Test-Path $finalPath) {
        try {
            $version = & .\$finalPath --version
            Write-Host "✅ ChromeDriver installé avec succès!" -ForegroundColor Green
            Write-Host "Version: $version" -ForegroundColor Green
        } catch {
            Write-Host "⚠️ ChromeDriver installé mais erreur lors de la vérification de version" -ForegroundColor Yellow
        }
    } else {
        Write-Host "❌ ChromeDriver non trouvé après installation" -ForegroundColor Red
        exit 1
    }
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

Write-Host "=== FIN DU TEST COMPLET SELENIUM ===" -ForegroundColor Cyan 