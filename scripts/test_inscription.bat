@echo off
REM Script batch pour tester l'inscription automatisée
REM Usage: test_inscription.bat

echo 🧪 === LANCEMENT DU TEST D'INSCRIPTION AUTOMATISÉ ===

REM Vérifier que PHP est installé
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP n'est pas installé ou n'est pas dans le PATH
    echo Installez PHP et ajoutez-le au PATH
    pause
    exit /b 1
)

REM Vérifier que le serveur PHP est en cours d'exécution
echo 🔍 Vérification du serveur PHP...
curl -s -o nul -w "%%{http_code}" http://localhost:8000/api/trainings.php > temp_status.txt
set /p status=<temp_status.txt
del temp_status.txt

if "%status%"=="200" (
    echo ✅ Serveur PHP accessible
) else (
    echo ❌ Serveur PHP non accessible (Code: %status%)
    echo Assurez-vous que le serveur PHP est démarré avec: php -S localhost:8000 -t public
    pause
    exit /b 1
)

REM Lancer le test PHP
echo 🚀 Lancement du test d'inscription...

if exist "tests\test_inscription_automated.php" (
    php tests\test_inscription_automated.php
    if %errorlevel% neq 0 (
        echo ❌ Erreur lors de l'exécution du test
        pause
        exit /b 1
    )
) else (
    echo ❌ Fichier de test non trouvé: tests\test_inscription_automated.php
    pause
    exit /b 1
)

echo 🎯 === FIN DU TEST ===
pause 