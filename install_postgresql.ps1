# Script PowerShell pour l'installation et configuration de PostgreSQL
# Exécutez ce script en tant qu'administrateur

Write-Host "========================================" -ForegroundColor Green
Write-Host "Installation et configuration PostgreSQL" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""

# Vérification de PostgreSQL
Write-Host "1. Vérification de PostgreSQL..." -ForegroundColor Yellow
try {
    $psqlVersion = psql --version 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "PostgreSQL est installé." -ForegroundColor Green
        Write-Host $psqlVersion -ForegroundColor Cyan
    } else {
        throw "PostgreSQL non trouvé"
    }
} catch {
    Write-Host "PostgreSQL n'est pas installé." -ForegroundColor Red
    Write-Host "Veuillez installer PostgreSQL depuis: https://www.postgresql.org/download/" -ForegroundColor Yellow
    Write-Host "Après installation, relancez ce script." -ForegroundColor Yellow
    Write-Host ""
    Read-Host "Appuyez sur Entrée pour quitter"
    exit 1
}

Write-Host ""

# Création de la base de données
Write-Host "2. Création de la base de données..." -ForegroundColor Yellow
try {
    $createDbResult = psql -U postgres -c "CREATE DATABASE training_catalog;" 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Base de données 'training_catalog' créée avec succès." -ForegroundColor Green
    } else {
        Write-Host "La base de données existe déjà ou erreur de connexion." -ForegroundColor Yellow
        Write-Host "Vérifiez que PostgreSQL est démarré et que l'utilisateur postgres existe." -ForegroundColor Yellow
    }
} catch {
    Write-Host "Erreur lors de la création de la base de données." -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host ""

# Import du script SQL
Write-Host "3. Import du script SQL..." -ForegroundColor Yellow
try {
    $importResult = psql -U postgres -d training_catalog -f "sql/trainings.sql" 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "Script SQL importé avec succès." -ForegroundColor Green
    } else {
        Write-Host "Erreur lors de l'import du script SQL." -ForegroundColor Red
        Write-Host "Vérifiez que la base de données training_catalog existe." -ForegroundColor Yellow
    }
} catch {
    Write-Host "Erreur lors de l'import du script SQL." -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Configuration terminée !" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Pour démarrer l'application:" -ForegroundColor Cyan
Write-Host "php -S localhost:8000 -t public" -ForegroundColor White
Write-Host ""
Write-Host "Puis ouvrez: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""

Read-Host "Appuyez sur Entrée pour quitter" 