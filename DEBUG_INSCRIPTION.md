# 🔍 Guide de Débogage - Inscription aux Formations

## Problème
Vous n'arrivez pas à vous inscrire à un programme de formation.

## Solution
J'ai ajouté des **logs détaillés** dans tout le système pour identifier rapidement le problème.

## 🚀 Démarrage Rapide

### Option 1: Script automatique (Recommandé)
```bash
# Windows (PowerShell)
.\start_debug_server.ps1

# Windows (CMD)
start_debug_server.bat
```

### Option 2: Manuel
```bash
# 1. Initialiser la base de données
php init_database.php

# 2. Tester le système
php test_inscription_debug.php

# 3. Démarrer le serveur
php -S localhost:8000 -t public

# 4. Ouvrir dans le navigateur
# http://localhost:8000
```

## 📋 Logs Ajoutés

### 1. Contrôleur Participant (`src/controllers/ParticipantController.php`)
- ✅ Logs de début/fin de requête
- ✅ Méthode HTTP et URI
- ✅ Données reçues (raw, JSON, POST)
- ✅ Validation des données
- ✅ Vérification de la formation
- ✅ Vérification de l'unicité de l'email
- ✅ Création du participant
- ✅ Erreurs détaillées avec stack trace

### 2. Modèle Participant (`src/models/Participant.php`)
- ✅ Structure de la table
- ✅ SQL généré
- ✅ Paramètres d'insertion
- ✅ Résultat de l'exécution
- ✅ Vérification d'email

### 3. Fonctions utilitaires (`src/utils/helpers.php`)
- ✅ Données avant/après nettoyage
- ✅ Validation étape par étape
- ✅ Erreurs de validation détaillées

### 4. JavaScript côté client (`public/js/scripts.js`)
- ✅ Formulaire et champs trouvés
- ✅ Données préparées
- ✅ Validation côté client
- ✅ Requête HTTP détaillée
- ✅ Réponse du serveur
- ✅ Parsing JSON

## 🔍 Comment Utiliser les Logs

### 1. Vérifier les logs PHP
```bash
# Trouver le fichier de log PHP
php -i | grep error_log

# Ou vérifier dans php.ini
php --ini
```

### 2. Logs dans la console du navigateur
1. Ouvrez les outils de développement (F12)
2. Allez dans l'onglet "Console"
3. Essayez de vous inscrire
4. Regardez les logs détaillés

### 3. Script de test automatique
```bash
php test_inscription_debug.php
```

## 🐛 Problèmes Courants et Solutions

### 1. "Aucune donnée reçue"
**Cause**: Problème de parsing JSON côté serveur
**Solution**: Vérifiez les headers Content-Type

### 2. "Formation non trouvée"
**Cause**: Base de données vide ou ID incorrect
**Solution**: Relancez `init_database.php`

### 3. "Email déjà utilisé"
**Cause**: Email déjà inscrit
**Solution**: Utilisez un email différent

### 4. "Colonne position n'existe pas"
**Cause**: Structure de base de données incomplète
**Solution**: Relancez `init_database.php`

### 5. Erreur CORS
**Cause**: Headers CORS manquants
**Solution**: Vérifiez les headers dans le contrôleur

## 📊 Points de Contrôle

### ✅ Base de données
- [ ] Formations présentes
- [ ] Table participants créée
- [ ] Colonne position existe

### ✅ API
- [ ] Serveur PHP démarré
- [ ] API formations accessible
- [ ] API participants accessible

### ✅ Frontend
- [ ] Formulaire chargé
- [ ] JavaScript fonctionne
- [ ] Validation côté client

### ✅ Logs
- [ ] Logs PHP visibles
- [ ] Logs JavaScript dans console
- [ ] Pas d'erreurs critiques

## 🎯 Diagnostic Rapide

1. **Lancez le script de test**:
   ```bash
   php test_inscription_debug.php
   ```

2. **Vérifiez les logs PHP**:
   ```bash
   tail -f /var/log/php_errors.log
   ```

3. **Ouvrez la console du navigateur** (F12)

4. **Essayez une inscription** et regardez les logs

## 📞 Support

Si le problème persiste après avoir vérifié tous les logs :

1. Copiez les logs d'erreur
2. Notez les étapes qui échouent
3. Vérifiez la version de PHP et SQLite
4. Testez avec un navigateur différent

## 🔧 Configuration Avancée

### Activer tous les logs PHP
```ini
; php.ini
error_reporting = E_ALL
log_errors = On
error_log = /path/to/php_errors.log
display_errors = Off
```

### Logs SQLite
```php
// Dans config.php
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
```

---

**💡 Conseil**: Les logs sont maintenant très détaillés. Si vous ne voyez pas de logs, vérifiez la configuration `error_log` de PHP. 