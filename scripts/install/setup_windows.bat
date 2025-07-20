@echo off
chcp 65001 >nul
setlocal enabledelayedexpansion

echo 🚀 Installation automatique de Gogo sur Windows
echo ================================================
echo.

:: Vérifier si on est administrateur
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ❌ Ce script doit être exécuté en tant qu'administrateur
    echo 💡 Clic droit sur le fichier → "Exécuter en tant qu'administrateur"
    pause
    exit /b 1
)

:: 1. Vérification de PHP
echo 📋 Vérification de PHP...
php --version >nul 2>&1
if %errorLevel% equ 0 (
    echo ✅ PHP détecté
    for /f "tokens=*" %%i in ('php --version 2^>nul') do (
        echo    %%i
        goto :php_ok
    )
) else (
    echo ❌ PHP non détecté
    echo.
    echo 🔧 Installation de PHP...
    echo 💡 Téléchargez et installez XAMPP depuis :
    echo    https://www.apachefriends.org/fr/download.html
    echo.
    echo ⏳ Appuyez sur Entrée une fois PHP installé...
    pause
    
    :: Ajouter PHP au PATH si XAMPP est installé
    if exist "C:\xampp\php\php.exe" (
        setx PATH "%PATH%;C:\xampp\php" /M
        echo ✅ PHP ajouté au PATH
    )
)

:php_ok
echo.

:: 2. Vérification de la structure du projet
echo 📁 Vérification de la structure du projet...
if not exist "public" (
    echo ❌ Dossier 'public' manquant
    goto :error
)
if not exist "src" (
    echo ❌ Dossier 'src' manquant
    goto :error
)
if not exist "database" (
    echo ❌ Dossier 'database' manquant
    goto :error
)
if not exist "scripts" (
    echo ❌ Dossier 'scripts' manquant
    goto :error
)
echo ✅ Structure du projet correcte
echo.

:: 3. Configuration de la base de données
echo 🗄️ Configuration de la base de données...

echo   - Initialisation de la base de données...
php scripts\database\init_database.php
if %errorLevel% equ 0 (
    echo ✅ Base de données initialisée
) else (
    echo ❌ Erreur lors de l'initialisation
)

echo   - Création de l'utilisateur administrateur...
php scripts\database\create_admin_user.php
if %errorLevel% equ 0 (
    echo ✅ Utilisateur administrateur créé
) else (
    echo ❌ Erreur lors de la création de l'utilisateur
)

echo   - Vérification de la structure...
php scripts\database\fix_database_structure.php
if %errorLevel% equ 0 (
    echo ✅ Structure de la base de données vérifiée
) else (
    echo ⚠️ Erreur lors de la vérification de la structure
)
echo.

:: 4. Test de la configuration
echo 🔍 Test de la configuration...
php tests\check_database.php
if %errorLevel% equ 0 (
    echo ✅ Tests de base de données réussis
) else (
    echo ⚠️ Erreur lors des tests
)
echo.

:: 5. Démarrage du serveur
echo 🚀 Démarrage du serveur...
echo 📍 Serveur accessible sur: http://localhost:8000
echo 🔧 Interface admin: http://localhost:8000/admin.html
echo 👤 Identifiants: admin / admin123
echo.
echo 💡 Pour arrêter le serveur, appuyez sur Ctrl+C
echo.

:: Ouvrir le navigateur
start http://localhost:8000

:: Démarrer le serveur
php -S localhost:8000 -t public
goto :end

:error
echo.
echo ❌ Erreur lors de l'installation
echo 💡 Vérifiez que vous êtes dans le bon répertoire
pause
exit /b 1

:end
echo.
echo 🎉 Installation terminée avec succès !
pause 