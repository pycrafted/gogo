# 🧪 Guide Complet des Tests

## 📋 Vue d'Ensemble

Ce projet inclut plusieurs types de tests pour valider le bon fonctionnement de l'application de catalogue de formations :

1. **Test Simple** - Validation API et structure (sans dépendances)
2. **Test Selenium Simplifié** - Simulation utilisateur réelle (ChromeDriver requis)
3. **Test API Automatisé** - Tests backend complets
4. **Test Selenium Complet** - Tests end-to-end avec dépendances

## 🚀 Tests Disponibles

### 1. Test Simple (Recommandé pour la livraison)

**Commande :**
```powershell
.\scripts\test_simple.ps1
```

**Ce que fait ce test :**
- ✅ Vérifie l'accessibilité de l'API
- ✅ Vérifie l'accessibilité de la page web
- ✅ Teste le chargement des formations
- ✅ Teste l'inscription avec données valides
- ✅ Teste la validation des champs manquants
- ✅ Teste la validation des emails
- ✅ Vérifie l'enregistrement en base de données
- ✅ Vérifie la structure HTML du formulaire

**Avantages :**
- ⚡ Rapide (quelques secondes)
- 🔧 Simple (aucune dépendance)
- ✅ Fiable (teste tous les aspects critiques)
- 📊 Complet (rapport détaillé)

### 2. Test Selenium Simplifié (Simulation Utilisateur Réelle)

**Commande :**
```powershell
.\scripts\test_selenium_simple.ps1
```

**Ce que fait ce test :**
- ✅ **Ouvre un navigateur Chrome** (mode headless)
- ✅ **Navigue vers la page** de l'application
- ✅ **Trouve les boutons d'inscription** dans l'interface
- ✅ **Clique sur le bouton** pour ouvrir le modal
- ✅ **Remplit les champs** avec les données utilisateur
- ✅ **Soumet le formulaire** en cliquant sur "S'inscrire"
- ✅ **Vérifie le succès** de l'inscription
- ✅ **Valide en base** l'enregistrement

**Prérequis :**
- ChromeDriver installé (voir installation ci-dessous)

**Avantages :**
- 🎯 **Réaliste** - Simulation utilisateur réelle
- 🔄 **End-to-End** - Du clic à l'enregistrement
- 📱 **Interface** - Teste l'interface utilisateur
- 🎯 **Complet** - Processus utilisateur complet

### 3. Test API Automatisé

**Commande :**
```bash
php tests/test_inscription_automated.php
```

**Ce que fait ce test :**
- ✅ Tests d'inscription avec données valides
- ✅ Tests de validation avec données invalides
- ✅ Tests de gestion d'erreurs
- ✅ Vérification en base de données

### 4. Test Selenium Complet (Avec Dépendances)

**Commande :**
```bash
php tests/test_selenium_inscription.php
```

**Prérequis :**
- Composer installé
- Extension ZIP PHP activée
- ChromeDriver installé

## 📦 Installation des Dépendances

### Pour les Tests Selenium

#### 1. Installer ChromeDriver

**Téléchargement :**
- Aller sur : https://chromedriver.chromium.org/
- Télécharger la version compatible avec votre Chrome

**Installation :**
```bash
# Option 1: Placer dans le répertoire du projet
# Copier chromedriver.exe dans le dossier racine

# Option 2: Ajouter au PATH système
# Copier chromedriver.exe dans C:\Windows\System32\
```

**Vérification :**
```bash
chromedriver --version
```

#### 2. Activer l'Extension ZIP PHP (Optionnel)

**Localiser php.ini :**
```bash
php --ini
```

**Éditer php.ini :**
- Ouvrir le fichier php.ini
- Chercher `;extension=zip`
- Décommenter : `extension=zip`

#### 3. Installer les Dépendances Composer (Optionnel)

```bash
composer install
```

## 🎯 Recommandation pour la Livraison

**Pour une validation complète, utilisez les deux tests principaux :**

```powershell
# 1. Test simple (API + Structure)
.\scripts\test_simple.ps1

# 2. Test Selenium (Simulation utilisateur)
.\scripts\test_selenium_simple.ps1
```

## 📊 Critères de Validation

### Test Simple
- [ ] ✅ API accessible (Code HTTP 200)
- [ ] ✅ Page web accessible
- [ ] ✅ Formations chargées
- [ ] ✅ Inscription réussie (API)
- [ ] ✅ Validation champs manquants
- [ ] ✅ Validation email
- [ ] ✅ Enregistrement en base
- [ ] ✅ Structure HTML correcte

### Test Selenium
- [ ] ✅ Navigation navigateur - ChromeDriver fonctionne
- [ ] ✅ Ouverture modal - Boutons trouvés et cliqués
- [ ] ✅ Remplissage formulaire - Champs saisis correctement
- [ ] ✅ Soumission réussie - Formulaire soumis
- [ ] ✅ Confirmation utilisateur - Processus complet validé

## 🔍 Comparaison des Tests

| Aspect | Test Simple | Test Selenium |
|--------|-------------|---------------|
| **API** | ✅ Testé | ✅ Testé |
| **Validation** | ✅ Testé | ✅ Testé |
| **Base de données** | ✅ Testé | ✅ Testé |
| **Interface utilisateur** | ⚠️ Partiel | ✅ Complet |
| **Saisie utilisateur** | ❌ Non testé | ✅ Simulé |
| **Navigation** | ❌ Non testé | ✅ Testé |
| **Dépendances** | ❌ Aucune | ⚠️ ChromeDriver |

## 🚨 Dépannage

### Problème : ChromeDriver non trouvé
```
❌ ChromeDriver non trouvé
```

**Solution :**
1. Télécharger ChromeDriver depuis https://chromedriver.chromium.org/
2. Placer chromedriver.exe dans le répertoire du projet
3. Ou utiliser le test simple : `.\scripts\test_simple.ps1`

### Problème : Extension ZIP manquante
```
Problem 1
- php-webdriver/webdriver require ext-zip * -> it is missing from your system
```

**Solution :**
1. Localiser php.ini : `php --ini`
2. Éditer php.ini et décommenter `extension=zip`
3. Redémarrer le serveur PHP

### Problème : Serveur PHP non accessible
```
Serveur PHP non accessible
```

**Solution :**
```bash
# Démarrer le serveur PHP
php -S localhost:8000 -t public
```

## 📝 Exemples de Sortie

### Test Simple Réussi
```
=== TEST SIMPLE D'INSCRIPTION ===
✅ Test 1: Accessibilité de l'API - RÉUSSI
✅ Test 2: Accessibilité de la page web - RÉUSSI
✅ Test 3: Chargement des formations - RÉUSSI
✅ Test 4: Inscription valide - RÉUSSI
✅ Test 5: Validation champs manquants - RÉUSSI
✅ Test 6: Validation email - RÉUSSI
✅ Test 7: Vérification en base - RÉUSSI
✅ Test 8: Structure HTML - RÉUSSI
```

### Test Selenium Réussi
```
=== TEST SELENIUM SIMPLIFIÉ ===
✅ ChromeDriver accessible
✅ Session Chrome créée
✅ Navigation vers la page réussie
✅ Boutons d'inscription trouvés
✅ Clic sur le bouton d'inscription réussi
✅ Champ firstName rempli
✅ Champ lastName rempli
✅ Champ email rempli
✅ Formulaire soumis avec succès
✅ Notification de succès trouvée
✅ Participant trouvé en base de données!
```

## 🎉 Validation Finale

**Si tous les tests passent ✅**

Votre application est :
- ✅ **Fonctionnelle** - Toutes les fonctionnalités marchent
- ✅ **Testée** - Validation complète effectuée
- ✅ **Prête** - Pour la livraison immédiate
- ✅ **Fiable** - Tests automatisés réussis

**Vous pouvez livrer votre application en toute confiance !** 🚀

---

**📚 Pour plus d'informations sur la résolution des dépendances, consultez :**
- `GUIDE_DEPENDANCES.md` - Guide complet de résolution des problèmes
- `README.md` - Documentation générale du projet 