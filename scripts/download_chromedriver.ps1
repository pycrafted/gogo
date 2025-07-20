# Script pour télécharger et installer ChromeDriver automatiquement
# Usage: .\download_chromedriver.ps1

Write-Host "=== TÉLÉCHARGEMENT AUTOMATIQUE DE CHROMEDRIVER ===" -ForegroundColor Cyan

# Configuration
$chromeDriverVersion = "138.0.7204.157"
$chromeDriverUrl = "https://storage.googleapis.com/chrome-for-testing-public/$chromeDriverVersion/win64/chromedriver-win64.zip"
$downloadPath = "chromedriver-win64.zip"
$extractPath = "chromedriver-win64"
$finalPath = "chromedriver.exe"

Write-Host "Version ChromeDriver: $chromeDriverVersion" -ForegroundColor Yellow
Write-Host "URL de téléchargement: $chromeDriverUrl" -ForegroundColor Yellow

# Vérifier si ChromeDriver existe déjà
if (Test-Path $finalPath) {
    Write-Host "ChromeDriver existe déjà: $finalPath" -ForegroundColor Green
    Write-Host "Version actuelle:" -ForegroundColor Yellow
    try {
        $version = & .\$finalPath --version
        Write-Host $version -ForegroundColor Green
    } catch {
        Write-Host "Impossible de vérifier la version" -ForegroundColor Red
    }
    exit 0
}

# Télécharger ChromeDriver
Write-Host "Téléchargement de ChromeDriver..." -ForegroundColor Yellow

try {
    $webClient = New-Object System.Net.WebClient
    $webClient.DownloadFile($chromeDriverUrl, $downloadPath)
    Write-Host "✅ Téléchargement réussi: $downloadPath" -ForegroundColor Green
} catch {
    Write-Host "❌ Erreur lors du téléchargement: $_" -ForegroundColor Red
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

# Chercher chromedriver.exe dans le dossier extrait
$chromeDriverInExtract = Get-ChildItem -Path $extractPath -Name "chromedriver.exe" -Recurse

if ($chromeDriverInExtract) {
    $sourcePath = Join-Path $extractPath $chromeDriverInExtract[0]
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
    Write-Host "❌ ChromeDriver non trouvé dans l'archive" -ForegroundColor Red
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
        Write-Host "Emplacement: $(Resolve-Path $finalPath)" -ForegroundColor Green
    } catch {
        Write-Host "⚠️ ChromeDriver installé mais erreur lors de la vérification de version" -ForegroundColor Yellow
    }
} else {
    Write-Host "❌ ChromeDriver non trouvé après installation" -ForegroundColor Red
    exit 1
}

Write-Host "=== INSTALLATION TERMINÉE ===" -ForegroundColor Cyan
Write-Host "Vous pouvez maintenant lancer le test Selenium:" -ForegroundColor Yellow
Write-Host ".\scripts\test_selenium_simple.ps1" -ForegroundColor Green 