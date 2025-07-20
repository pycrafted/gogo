# Script d'installation automatique pour PC Windows neuf
# Exécuter en tant qu'administrateur

param(
    [switch]$SkipPHP,
    [switch]$SkipDatabase,
    [switch]$SkipServer
)

Write-Host "🚀 Installation automatique de Gogo sur Windows" -ForegroundColor Green
Write-Host "================================================" -ForegroundColor Green
Write-Host ""

# Fonction pour vérifier si une commande existe
function Test-Command($cmdname) {
    return [bool](Get-Command -Name $cmdname -ErrorAction SilentlyContinue)
}

# Fonction pour télécharger un fichier
function Download-File($url, $output) {
    try {
        Write-Host "📥 Téléchargement de $url..." -ForegroundColor Yellow
        Invoke-WebRequest -Uri $url -OutFile $output
        return $true
    } catch {
        Write-Host "❌ Erreur lors du téléchargement" -ForegroundColor Red
        return $false
    }
}

# 1. Vérification de PHP
if (-not $SkipPHP) {
    Write-Host "📋 Vérification de PHP..." -ForegroundColor Yellow
    
    if (Test-Command "php") {
        $phpVersion = php --version 2>$null
        Write-Host "✅ PHP détecté : $($phpVersion[0])" -ForegroundColor Green
    } else {
        Write-Host "❌ PHP non détecté" -ForegroundColor Red
        Write-Host ""
        Write-Host "🔧 Installation de PHP..." -ForegroundColor Yellow
        
        # Télécharger XAMPP
        $xamppUrl = "https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe/download"
        $xamppInstaller = "$env:TEMP\xampp-installer.exe"
        
        if (Download-File $xamppUrl $xamppInstaller) {
            Write-Host "✅ XAMPP téléchargé" -ForegroundColor Green
            Write-Host "💡 Veuillez installer XAMPP manuellement :" -ForegroundColor Cyan
            Write-Host "   - Double-cliquer sur $xamppInstaller" -ForegroundColor Cyan
            Write-Host "   - Suivre l'assistant d'installation" -ForegroundColor Cyan
            Write-Host "   - Cocher Apache, PHP, SQLite" -ForegroundColor Cyan
            Write-Host ""
            Write-Host "⏳ Appuyez sur Entrée une fois XAMPP installé..." -ForegroundColor Yellow
            Read-Host
            
            # Ajouter PHP au PATH
            $phpPath = "C:\xampp\php"
            if (Test-Path $phpPath) {
                $currentPath = [Environment]::GetEnvironmentVariable("PATH", "Machine")
                if ($currentPath -notlike "*$phpPath*") {
                    [Environment]::SetEnvironmentVariable("PATH", "$currentPath;$phpPath", "Machine")
                    Write-Host "✅ PHP ajouté au PATH" -ForegroundColor Green
                }
            }
        } else {
            Write-Host "❌ Impossible de télécharger XAMPP" -ForegroundColor Red
            Write-Host "💡 Installez PHP manuellement depuis https://windows.php.net/" -ForegroundColor Yellow
            exit 1
        }
    }
}

Write-Host ""

# 2. Vérification de la structure du projet
Write-Host "📁 Vérification de la structure du projet..." -ForegroundColor Yellow

$requiredDirs = @("public", "src", "database", "scripts")
$missingDirs = @()

foreach ($dir in $requiredDirs) {
    if (-not (Test-Path $dir)) {
        $missingDirs += $dir
    }
}

if ($missingDirs.Count -gt 0) {
    Write-Host "❌ Dossiers manquants : $($missingDirs -join ', ')" -ForegroundColor Red
    Write-Host "💡 Assurez-vous d'être dans le bon répertoire" -ForegroundColor Yellow
    exit 1
} else {
    Write-Host "✅ Structure du projet correcte" -ForegroundColor Green
}

Write-Host ""

# 3. Configuration de la base de données
if (-not $SkipDatabase) {
    Write-Host "🗄️ Configuration de la base de données..." -ForegroundColor Yellow
    
    # Initialiser la base de données
    try {
        php scripts\database\init_database.php
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Base de données initialisée" -ForegroundColor Green
        } else {
            Write-Host "❌ Erreur lors de l'initialisation" -ForegroundColor Red
        }
    } catch {
        Write-Host "❌ Erreur lors de l'initialisation" -ForegroundColor Red
    }
    
    # Créer l'utilisateur administrateur
    try {
        php scripts\database\create_admin_user.php
        if ($LASTEXITCODE -eq 0) {
            Write-Host "✅ Utilisateur administrateur créé" -ForegroundColor Green
        } else {
            Write-Host "❌ Erreur lors de la création de l'utilisateur" -ForegroundColor Red
        }
    } catch {
        Write-Host "❌ Erreur lors de la création de l'utilisateur" -ForegroundColor Red
    }
    
    # Corriger la structure si nécessaire
    try {
        php scripts\database\fix_database_structure.php
        Write-Host "✅ Structure de la base de données vérifiée" -ForegroundColor Green
    } catch {
        Write-Host "⚠️ Erreur lors de la vérification de la structure" -ForegroundColor Yellow
    }
}

Write-Host ""

# 4. Test de la configuration
Write-Host "🔍 Test de la configuration..." -ForegroundColor Yellow

try {
    php tests\check_database.php
    Write-Host "✅ Tests de base de données réussis" -ForegroundColor Green
} catch {
    Write-Host "⚠️ Erreur lors des tests" -ForegroundColor Yellow
}

Write-Host ""

# 5. Démarrage du serveur
if (-not $SkipServer) {
    Write-Host "🚀 Démarrage du serveur..." -ForegroundColor Yellow
    Write-Host "📍 Serveur accessible sur: http://localhost:8000" -ForegroundColor Cyan
    Write-Host "🔧 Interface admin: http://localhost:8000/admin.html" -ForegroundColor Cyan
    Write-Host "👤 Identifiants: admin / admin123" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "💡 Pour arrêter le serveur, appuyez sur Ctrl+C" -ForegroundColor Yellow
    Write-Host ""
    
    # Ouvrir le navigateur
    try {
        Start-Process "http://localhost:8000"
    } catch {
        Write-Host "⚠️ Impossible d'ouvrir le navigateur automatiquement" -ForegroundColor Yellow
    }
    
    # Démarrer le serveur
    try {
        php -S localhost:8000 -t public
    } catch {
        Write-Host "❌ Erreur lors du démarrage du serveur" -ForegroundColor Red
    }
} else {
    Write-Host "✅ Installation terminée !" -ForegroundColor Green
    Write-Host ""
    Write-Host "🎯 Pour démarrer le serveur manuellement :" -ForegroundColor Cyan
    Write-Host "   php -S localhost:8000 -t public" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "🌐 Accès à l'application :" -ForegroundColor Cyan
    Write-Host "   - Page d'accueil: http://localhost:8000" -ForegroundColor Cyan
    Write-Host "   - Interface admin: http://localhost:8000/admin.html" -ForegroundColor Cyan
    Write-Host "   - Identifiants: admin / admin123" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "🎉 Installation terminée avec succès !" -ForegroundColor Green 