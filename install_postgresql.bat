@echo off
echo ========================================
echo Installation et configuration PostgreSQL
echo ========================================
echo.

echo 1. Vérification de PostgreSQL...
psql --version >nul 2>&1
if %errorlevel% neq 0 (
    echo PostgreSQL n'est pas installé.
    echo Veuillez installer PostgreSQL depuis: https://www.postgresql.org/download/
    echo.
    echo Après installation, relancez ce script.
    pause
    exit /b 1
)

echo PostgreSQL est installé.
echo.

echo 2. Création de la base de données...
psql -U postgres -c "CREATE DATABASE training_catalog;" 2>nul
if %errorlevel% neq 0 (
    echo La base de données existe déjà ou erreur de connexion.
    echo Vérifiez que PostgreSQL est démarré et que l'utilisateur postgres existe.
)

echo 3. Import du script SQL...
psql -U postgres -d training_catalog -f sql/trainings.sql
if %errorlevel% neq 0 (
    echo Erreur lors de l'import du script SQL.
    echo Vérifiez que la base de données training_catalog existe.
)

echo.
echo ========================================
echo Configuration terminée !
echo ========================================
echo.
echo Pour démarrer l'application:
echo php -S localhost:8000 -t public
echo.
echo Puis ouvrez: http://localhost:8000
echo.
pause 