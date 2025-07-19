# 🛠️ Scripts Utilitaires

Ce dossier contient tous les scripts utilitaires du projet organisés par catégorie.

## 📁 Structure

```
scripts/
├── database/     # Scripts de gestion de base de données
├── dev/          # Scripts de développement
└── install/      # Scripts d'installation
```

## 🗄️ Scripts de Base de Données (`database/`)

### Scripts d'initialisation
- `init_database.php` - Initialise la structure de la base de données
- `create_admin_user.php` - Crée un utilisateur administrateur
- `create_test_data.php` - Génère des données de test

### Scripts de maintenance
- `fix_database.php` - Correction générale de la base de données
- `fix_database_structure.php` - Correction de la structure
- `fix_participants_table.php` - Correction spécifique aux participants
- `force_fix_database.php` - Correction forcée
- `force_recreate_database.php` - Recréation complète de la DB
- `recreate_database.php` - Recréation de la base de données
- `simple_fix.php` - Correction simple
- `simple_fix_db.php` - Correction simple de la DB
- `update_database.php` - Mise à jour de la base de données

### Scripts de correction
- `quick_fix_inscription.php` - Correction rapide des inscriptions

## 🚀 Scripts de Développement (`dev/`)

### Serveurs de développement
- `start_debug_server.ps1` - Serveur PowerShell
- `start_debug_server.bat` - Serveur Windows

### Tests
- `test_api.ps1` - Tests de l'API

## 📦 Scripts d'Installation (`install/`)

### PostgreSQL
- Scripts d'installation PostgreSQL (à ajouter)

## 🔧 Utilisation

### Base de données
```bash
# Initialiser la DB
php scripts/database/init_database.php

# Créer un admin
php scripts/database/create_admin_user.php

# Corriger la DB
php scripts/database/fix_database.php
```

### Développement
```bash
# Lancer le serveur (PowerShell)
./scripts/dev/start_debug_server.ps1

# Lancer le serveur (Windows)
./scripts/dev/start_debug_server.bat

# Tester l'API
./scripts/dev/test_api.ps1
```

## ⚠️ Notes importantes

1. **Sauvegarde** : Toujours sauvegarder la base de données avant d'exécuter des scripts de correction
2. **Permissions** : S'assurer d'avoir les bonnes permissions pour les fichiers de base de données
3. **Environnement** : Vérifier que PHP est correctement configuré
4. **Logs** : Consulter les logs en cas d'erreur

## 🆘 Dépannage

### Problèmes courants
- **Erreur de connexion DB** : Vérifier `src/config.php`
- **Permissions** : Vérifier les droits d'écriture sur `database/`
- **PHP** : Vérifier que les extensions SQLite/PostgreSQL sont activées

### Ordre d'exécution recommandé
1. `init_database.php`
2. `create_admin_user.php`
3. `create_test_data.php` (optionnel)
4. Scripts de correction si nécessaire 