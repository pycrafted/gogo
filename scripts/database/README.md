# 🗄️ Scripts de Base de Données

Ce dossier contient tous les scripts liés à la gestion de la base de données.

## 📋 Scripts disponibles

### 🔧 Initialisation
- **`init_database.php`** - Initialise la structure complète de la base de données
- **`create_admin_user.php`** - Crée un utilisateur administrateur
- **`create_test_data.php`** - Génère des données de test pour le développement

### 🔨 Maintenance et Correction
- **`fix_database.php`** - Correction générale de la base de données
- **`fix_database_structure.php`** - Correction spécifique de la structure
- **`fix_participants_table.php`** - Correction de la table participants
- **`force_fix_database.php`** - Correction forcée (utiliser avec précaution)
- **`force_recreate_database.php`** - Recréation complète de la DB
- **`recreate_database.php`** - Recréation de la base de données
- **`simple_fix.php`** - Correction simple
- **`simple_fix_db.php`** - Correction simple de la DB
- **`update_database.php`** - Mise à jour de la base de données

### ⚡ Corrections Rapides
- **`quick_fix_inscription.php`** - Correction rapide des inscriptions

## 🚀 Utilisation

### Installation initiale
```bash
# 1. Initialiser la base de données
php init_database.php

# 2. Créer un utilisateur admin
php create_admin_user.php

# 3. (Optionnel) Ajouter des données de test
php create_test_data.php
```

### Maintenance
```bash
# Correction générale
php fix_database.php

# Correction spécifique
php fix_participants_table.php

# Recréation complète (⚠️ ATTENTION)
php force_recreate_database.php
```

## ⚠️ Précautions

### Avant d'exécuter un script de correction
1. **Sauvegarder** la base de données
2. **Tester** sur un environnement de développement
3. **Vérifier** les permissions d'écriture
4. **Consulter** les logs d'erreur

### Ordre d'exécution recommandé
1. `init_database.php` (première fois)
2. `create_admin_user.php` (première fois)
3. `create_test_data.php` (optionnel)
4. Scripts de correction si nécessaire

## 🔍 Dépannage

### Erreurs courantes
- **"Database locked"** : Fermer toutes les connexions
- **"Permission denied"** : Vérifier les droits d'écriture
- **"Table not found"** : Exécuter `init_database.php`

### Logs
Les scripts génèrent des logs dans la console. En cas d'erreur :
1. Vérifier les messages d'erreur
2. Consulter les permissions de fichiers
3. Vérifier la configuration PHP

## 📊 Structure de la base de données

### Tables principales
- `users` - Utilisateurs administrateurs
- `trainings` - Catalogue des formations
- `participants` - Inscriptions aux formations

### Relations
- Un participant peut s'inscrire à une formation
- Un utilisateur peut gérer plusieurs formations
- Chaque formation peut avoir plusieurs participants 