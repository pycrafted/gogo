# 🚀 Guide d'Installation - PC Windows Neuf

## 📋 Prérequis Système

### 1. Installer PHP

#### Option A : Installation via XAMPP (Recommandé pour débutants)
1. **Télécharger XAMPP** : https://www.apachefriends.org/fr/download.html
2. **Installer XAMPP** :
   - Double-cliquer sur le fichier téléchargé
   - Suivre l'assistant d'installation
   - Cocher au minimum : Apache, PHP, SQLite
3. **Ajouter PHP au PATH** :
   - Ouvrir les Variables d'environnement système
   - Ajouter `C:\xampp\php` au PATH

#### Option B : Installation PHP standalone
1. **Télécharger PHP** : https://windows.php.net/download/
2. **Extraire dans** `C:\php`
3. **Copier** `php.ini-development` vers `php.ini`
4. **Ajouter** `C:\php` au PATH système

### 2. Vérifier l'Installation

Ouvrir PowerShell et taper :
```powershell
php --version
```

### 3. Installer Git (Optionnel mais recommandé)

1. **Télécharger Git** : https://git-scm.com/download/win
2. **Installer** avec les options par défaut

## 🔧 Configuration du Projet

### 1. Préparer le Dossier du Projet

```powershell
# Créer un dossier pour vos projets
mkdir C:\projects
cd C:\projects

# Si vous avez Git installé, cloner le projet
git clone <votre-repo-url> gogo

# Sinon, copier votre projet dans C:\projects\gogo
```

### 2. Vérifier la Structure

Votre dossier doit contenir :
```
gogo/
├── public/
├── src/
├── database/
├── scripts/
└── README.md
```

### 3. Configuration PHP

#### Créer un fichier php.ini personnalisé
```ini
; Extensions requises
extension=pdo
extension=pdo_sqlite
extension=sqlite3
extension=json
extension=mbstring

; Configuration de développement
display_errors = On
error_reporting = E_ALL
memory_limit = 256M
max_execution_time = 300
```

## 🚀 Lancement du Projet

### Méthode 1 : Script Automatique (Recommandé)

1. **Ouvrir PowerShell** en tant qu'administrateur
2. **Naviguer vers le projet** :
   ```powershell
   cd C:\projects\gogo
   ```
3. **Exécuter le script de démarrage** :
   ```powershell
   .\scripts\dev\start_debug_server.ps1
   ```

### Méthode 2 : Commandes Manuelles

```powershell
# 1. Initialiser la base de données
php scripts\database\init_database.php

# 2. Créer un utilisateur administrateur
php scripts\database\create_admin_user.php

# 3. Démarrer le serveur
php -S localhost:8000 -t public
```

### Méthode 3 : Via XAMPP

1. **Copier le projet** dans `C:\xampp\htdocs\gogo`
2. **Démarrer Apache** dans le panneau de contrôle XAMPP
3. **Accéder** à `http://localhost/gogo`

## 🌐 Accès à l'Application

### URLs d'Accès
- **Page d'accueil** : http://localhost:8000
- **Interface admin** : http://localhost:8000/admin.html
- **Page de connexion** : http://localhost:8000/login.html

### Identifiants par Défaut
- **Utilisateur** : `admin`
- **Mot de passe** : `admin123`

## 🔧 Dépannage

### Problème : PHP non reconnu
```powershell
# Vérifier le PATH
echo $env:PATH

# Ajouter PHP au PATH temporairement
$env:PATH += ";C:\xampp\php"
```

### Problème : Port 8000 occupé
```powershell
# Changer le port
php -S localhost:8080 -t public
```

### Problème : Permissions
```powershell
# Exécuter PowerShell en tant qu'administrateur
# Ou modifier les permissions du dossier
```

### Problème : Base de données
```powershell
# Réinitialiser complètement
php scripts\database\force_recreate_database.php
```

## 📚 Commandes Utiles

### Vérifications
```powershell
# Vérifier PHP
php --version

# Vérifier les extensions
php -m

# Tester la base de données
php tests\check_database.php
```

### Scripts de Maintenance
```powershell
# Créer des données de test
php scripts\database\create_test_data.php

# Corriger la base de données
php scripts\database\fix_database.php

# Tester l'API
php tests\test_api.php
```

## 🎯 Prochaines Étapes

1. **Personnaliser** l'application selon vos besoins
2. **Configurer** la base de données pour la production
3. **Sécuriser** les identifiants par défaut
4. **Déployer** sur un serveur web

## 📞 Support

En cas de problème :
1. Vérifier les logs d'erreur PHP
2. Consulter la documentation technique
3. Tester avec les scripts de débogage inclus

---
*Guide créé pour PC Windows neuf - Version 1.0* 