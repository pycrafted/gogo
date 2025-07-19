# 🚀 Scripts de Développement

Ce dossier contient les scripts utiles pour le développement et les tests.

## 📋 Scripts disponibles

### 🌐 Serveurs de développement
- **`start_debug_server.ps1`** - Serveur de développement PowerShell
- **`start_debug_server.bat`** - Serveur de développement Windows

### 🧪 Tests
- **`test_api.ps1`** - Tests de l'API REST

## 🚀 Utilisation

### Lancer le serveur de développement

#### PowerShell
```powershell
# Dans le répertoire racine du projet
.\scripts\dev\start_debug_server.ps1
```

#### Windows (CMD)
```cmd
# Dans le répertoire racine du projet
scripts\dev\start_debug_server.bat
```

### Tester l'API
```powershell
# Tests de l'API
.\scripts\dev\test_api.ps1
```

## ⚙️ Configuration

### Serveur de développement
- **Port** : 8000 (par défaut)
- **Document root** : `public/`
- **URL** : `http://localhost:8000`

### Variables d'environnement
Les scripts utilisent les variables suivantes :
- `PHP_PATH` - Chemin vers PHP (auto-détecté)
- `PROJECT_ROOT` - Racine du projet
- `DEBUG_PORT` - Port du serveur (8000)

## 🔧 Personnalisation

### Changer le port
Modifier la variable `$port` dans les scripts :
```powershell
$port = 8080  # Au lieu de 8000
```

### Ajouter des options
```powershell
# Options supplémentaires pour PHP
$phpOptions = "-d display_errors=1 -d error_reporting=E_ALL"
```

## 🐛 Dépannage

### Problèmes courants

#### "PHP not found"
```powershell
# Vérifier que PHP est installé
php --version

# Ajouter PHP au PATH si nécessaire
$env:PATH += ";C:\php"
```

#### "Port already in use"
```powershell
# Vérifier les processus sur le port
netstat -ano | findstr :8000

# Tuer le processus si nécessaire
taskkill /PID <PID> /F
```

#### "Permission denied"
```powershell
# Exécuter PowerShell en tant qu'administrateur
# Ou modifier la politique d'exécution
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
```

### Logs et debugging
- Les scripts affichent les logs dans la console
- Vérifier les erreurs PHP dans la sortie
- Consulter les logs du serveur web

## 📝 Notes de développement

### Bonnes pratiques
1. **Toujours** tester sur le serveur de développement avant la production
2. **Vérifier** que la base de données est initialisée
3. **Consulter** les logs en cas d'erreur
4. **Sauvegarder** avant les modifications importantes

### Workflow recommandé
1. Lancer le serveur de développement
2. Tester l'API avec `test_api.ps1`
3. Développer et tester les fonctionnalités
4. Valider sur différents navigateurs
5. Déployer en production

## 🔗 Liens utiles

- **Documentation API** : Voir `docs/technical_report.md`
- **Scripts DB** : Voir `scripts/database/`
- **Configuration** : Voir `src/config.php` 