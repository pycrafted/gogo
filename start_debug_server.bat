@echo off
echo 🔧 Démarrage du serveur de débogage...
echo.

echo 📋 Vérification de PHP...
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP n'est pas installé ou n'est pas dans le PATH
    echo 💡 Installez PHP et ajoutez-le au PATH
    pause
    exit /b 1
)

echo ✅ PHP détecté
echo.

echo 🗄️  Initialisation de la base de données...
php init_database.php
if %errorlevel% neq 0 (
    echo ❌ Erreur lors de l'initialisation de la base de données
    pause
    exit /b 1
)

echo ✅ Base de données initialisée
echo.

echo 🔍 Test de débogage...
php test_inscription_debug.php
echo.

echo 🚀 Démarrage du serveur PHP...
echo 📍 Serveur accessible sur: http://localhost:8000
echo 🔍 Logs détaillés activés pour le débogage
echo.
echo 💡 Pour arrêter le serveur, appuyez sur Ctrl+C
echo.

start http://localhost:8000

php -S localhost:8000 -t public

pause 