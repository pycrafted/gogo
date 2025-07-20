# 🚀 Démarrage Rapide - PC Windows Neuf

## ⚡ Installation en 3 Étapes

### Étape 1 : Installer PHP
1. **Télécharger XAMPP** : https://www.apachefriends.org/fr/download.html
2. **Installer XAMPP** :
   - Double-cliquer sur le fichier téléchargé
   - Suivre l'assistant d'installation
   - **IMPORTANT** : Cocher Apache, PHP, SQLite
3. **Redémarrer** votre PC

### Étape 2 : Préparer le Projet
1. **Copier** votre projet dans `C:\projects\gogo`
2. **Ouvrir PowerShell** en tant qu'administrateur
3. **Naviguer** vers le projet :
   ```powershell
   cd C:\projects\gogo
   ```

### Étape 3 : Lancer l'Application
**Option A : Script automatique (Recommandé)**
```powershell
.\scripts\install\setup_windows.ps1
```

**Option B : Commandes manuelles**
```powershell
# Initialiser la base de données
php scripts\database\init_database.php

# Créer l'utilisateur admin
php scripts\database\create_admin_user.php

# Démarrer le serveur
php -S localhost:8000 -t public
```

## 🌐 Accès à l'Application

- **Page d'accueil** : http://localhost:8000
- **Interface admin** : http://localhost:8000/admin.html
- **Identifiants** : `admin` / `admin123`

## 🔧 Dépannage Rapide

### PHP non reconnu
```powershell
# Ajouter PHP au PATH
$env:PATH += ";C:\xampp\php"
```

### Port 8000 occupé
```powershell
# Changer le port
php -S localhost:8080 -t public
```

### Erreur de base de données
```powershell
# Réinitialiser complètement
php scripts\database\force_recreate_database.php
```

## 📞 Aide

- **Guide complet** : `INSTALLATION_GUIDE.md`
- **Scripts de test** : Dossier `tests/`
- **Documentation** : Dossier `docs/`

---
*Démarrage rapide - Version 1.0* 