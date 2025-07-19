# Script PowerShell pour démarrer le serveur de débogage
Write-Host "🔧 Démarrage du serveur de débogage..." -ForegroundColor Green
Write-Host ""

# Vérification de PHP
Write-Host "📋 Vérification de PHP..." -ForegroundColor Yellow
try {
    $phpVersion = php --version 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ PHP détecté" -ForegroundColor Green
        Write-Host $phpVersion[0] -ForegroundColor Cyan
    } else {
        Write-Host "❌ PHP n'est pas installé ou n'est pas dans le PATH" -ForegroundColor Red
        Write-Host "💡 Installez PHP et ajoutez-le au PATH" -ForegroundColor Yellow
        Read-Host "Appuyez sur Entrée pour continuer"
        exit 1
    }
} catch {
    Write-Host "❌ Erreur lors de la vérification de PHP" -ForegroundColor Red
    Read-Host "Appuyez sur Entrée pour continuer"
    exit 1
}

Write-Host ""

# Initialisation de la base de données
Write-Host "🗄️  Initialisation de la base de données..." -ForegroundColor Yellow
try {
    php init_database.php
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Base de données initialisée" -ForegroundColor Green
    } else {
        Write-Host "❌ Erreur lors de l'initialisation de la base de données" -ForegroundColor Red
        Read-Host "Appuyez sur Entrée pour continuer"
        exit 1
    }
} catch {
    Write-Host "❌ Erreur lors de l'initialisation" -ForegroundColor Red
    Read-Host "Appuyez sur Entrée pour continuer"
    exit 1
}

Write-Host ""

# Correction de la structure de la base de données
Write-Host "🔧 Correction de la structure de la base de données..." -ForegroundColor Yellow
try {
    php fix_database_structure.php
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Structure de la base de données corrigée" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Erreur lors de la correction de la structure" -ForegroundColor Yellow
    }
} catch {
    Write-Host "⚠️  Erreur lors de la correction" -ForegroundColor Yellow
}

Write-Host ""

# Test de débogage
Write-Host "🔍 Test de débogage..." -ForegroundColor Yellow
try {
    php test_inscription_debug.php
} catch {
    Write-Host "⚠️  Erreur lors du test de débogage" -ForegroundColor Yellow
}

Write-Host ""

# Démarrage du serveur
Write-Host "🚀 Démarrage du serveur PHP..." -ForegroundColor Green
Write-Host "📍 Serveur accessible sur: http://localhost:8000" -ForegroundColor Cyan
Write-Host "🔍 Logs détaillés activés pour le débogage" -ForegroundColor Cyan
Write-Host ""
Write-Host "💡 Pour arrêter le serveur, appuyez sur Ctrl+C" -ForegroundColor Yellow
Write-Host ""

# Ouvrir le navigateur
try {
    Start-Process "http://localhost:8000"
} catch {
    Write-Host "⚠️  Impossible d'ouvrir le navigateur automatiquement" -ForegroundColor Yellow
}

# Démarrer le serveur PHP
try {
    php -S localhost:8000 -t public
} catch {
    Write-Host "❌ Erreur lors du démarrage du serveur" -ForegroundColor Red
    Read-Host "Appuyez sur Entrée pour continuer"
} 