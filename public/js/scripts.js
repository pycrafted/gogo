/**
 * Scripts JavaScript pour l'application de catalogue de formations
 * Couche Presentation - Logique côté client
 */

// Variables globales
let trainings = [];
let currentTrainingId = null;
let searchTimeout = null;

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', function() {
    console.log('Application de catalogue de formations initialisée');
    loadTrainings();
    setupEventListeners();
});

/**
 * Configuration des écouteurs d'événements
 */
function setupEventListeners() {
    // Recherche avec debounce
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchTrainings, 300);
        });
    }
    
    // Écouteur pour le filtre par domaine
    const domainFilter = document.getElementById('domainFilter');
    if (domainFilter) {
        domainFilter.addEventListener('change', filterByDomain);
    }
    
    // Smooth scrolling pour les liens d'ancrage
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

/**
 * Affiche le modal des certifications
 */
function showCertificationModal() {
    console.log('🏆 Affichage du modal des certifications...');
    const modal = new bootstrap.Modal(document.getElementById('certificationModal'));
    modal.show();
}

/**
 * Affiche le modal des experts
 */
function showExpertsModal() {
    console.log('👨‍🏫 Affichage du modal des experts...');
    const modal = new bootstrap.Modal(document.getElementById('expertsModal'));
    modal.show();
}

/**
 * Affiche le modal des méthodes pédagogiques
 */
function showPedagogyModal() {
    console.log('🚀 Affichage du modal des méthodes pédagogiques...');
    const modal = new bootstrap.Modal(document.getElementById('pedagogyModal'));
    modal.show();
}

/**
 * Affiche le modal des partenariats
 */
function showPartnershipsModal() {
    console.log('🤝 Affichage du modal des partenariats...');
    const modal = new bootstrap.Modal(document.getElementById('partnershipsModal'));
    modal.show();
}

/**
 * Gère la soumission du formulaire de contact
 */
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleContactForm();
        });
    }
});

function handleContactForm() {
    console.log('📧 Traitement du formulaire de contact...');
    
    // Récupération des données du formulaire
    const formData = new FormData(document.getElementById('contactForm'));
    const contactData = {
        firstName: formData.get('firstName'),
        lastName: formData.get('lastName'),
        email: formData.get('email'),
        phone: formData.get('phone'),
        subject: formData.get('subject'),
        formation: formData.get('formation'),
        message: formData.get('message'),
        newsletter: formData.get('newsletter') === 'on'
    };
    
    console.log('📋 Données du formulaire:', contactData);
    
    // Simulation d'envoi (remplacer par un vrai appel API)
    setTimeout(() => {
        showNotification('✅ Message envoyé avec succès !', 'Nous vous répondrons dans les plus brefs délais.', 'success');
        document.getElementById('contactForm').reset();
    }, 1000);
    
    // Ici, vous pourriez ajouter un appel API réel :
    // fetch('/api/contact', {
    //     method: 'POST',
    //     headers: { 'Content-Type': 'application/json' },
    //     body: JSON.stringify(contactData)
    // })
    // .then(response => response.json())
    // .then(data => {
    //     showNotification('✅ Message envoyé !', 'Nous vous répondrons bientôt.', 'success');
    //     document.getElementById('contactForm').reset();
    // })
    // .catch(error => {
    //     showNotification('❌ Erreur', 'Impossible d\'envoyer le message. Réessayez.', 'error');
    // });
}

/**
 * Charge toutes les formations depuis l'API
 */
async function loadTrainings() {
    try {
        console.log('Chargement des formations...');
        showLoading(true);
        const response = await fetch('/api/trainings.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        console.log('Réponse API:', result);
        
        if (result.success) {
            trainings = result.data;
            console.log('Formations chargées:', trainings.length);
            displayTrainings(trainings);
            updateTrainingCount(trainings.length);
            showNotification('Formations chargées avec succès', 'success');
        } else {
            throw new Error(result.message || 'Erreur lors du chargement des formations');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des formations:', error);
        showNotification('Erreur lors du chargement des formations: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

/**
 * Affiche les formations en grille
 */
function displayTrainings(trainingsToShow) {
    console.log('Affichage des formations:', trainingsToShow.length);
    const grid = document.getElementById('formationsGrid');
    if (!grid) {
        console.error('Element formationsGrid non trouvé');
        return;
    }
    
    grid.innerHTML = '';
    
    if (trainingsToShow.length === 0) {
        grid.innerHTML = `
            <div class="col-12 text-center">
                <div class="py-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="text-muted mt-3">Aucune formation trouvée</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche</p>
                </div>
            </div>
        `;
        return;
    }
    
    trainingsToShow.forEach((training, index) => {
        console.log(`Affichage formation ${index + 1}:`, training);
        
        const domainColors = {
            'Informatique': 'bg-primary',
            'Management': 'bg-success',
            'Marketing': 'bg-warning',
            'Finance': 'bg-danger',
            'Ressources Humaines': 'bg-info',
            'Communication': 'bg-secondary',
            'Vente': 'bg-dark',
            'Logistique': 'bg-primary'
        };
        
        const domainColor = domainColors[training.domain] || 'bg-primary';
        
        const card = document.createElement('div');
        card.className = 'col-lg-4 col-md-6';
        card.innerHTML = `
            <div class="formation-card">
                <div class="formation-image">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="formation-content">
                    <span class="formation-domain ${domainColor} text-white">${training.domain}</span>
                    <h5 class="formation-title">${training.title}</h5>
                    <div class="formation-details">
                        <p class="mb-2">
                            <i class="bi bi-geo-alt me-2"></i>${training.location || 'N/A'}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-calendar me-2"></i>${training.date_formatted || formatDate(training.date)}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-clock me-2"></i>${training.duration_formatted || (training.duration ? training.duration + ' jour(s)' : 'N/A')}
                        </p>
                        <p class="mb-2">
                            <i class="bi bi-person me-2"></i>${training.animators || 'Expert du domaine'}
                        </p>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="formation-price">${training.price_formatted || (training.price ? formatPrice(training.price) : 'N/A')}</span>
                        <button class="btn btn-primary btn-sm" onclick="viewProgram(${training.id})">
                            <i class="bi bi-eye me-1"></i>Voir le programme
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });
    
    console.log('Grille mise à jour avec', trainingsToShow.length, 'formations');
}

/**
 * Recherche de formations
 */
function searchTrainings() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const domainFilter = document.getElementById('domainFilter').value;
    
    let filteredTrainings = trainings;
    
    // Filtre par terme de recherche
    if (searchTerm) {
        filteredTrainings = filteredTrainings.filter(training => 
            training.title.toLowerCase().includes(searchTerm) ||
            training.domain.toLowerCase().includes(searchTerm) ||
            (training.location && training.location.toLowerCase().includes(searchTerm)) ||
            (training.animators && training.animators.toLowerCase().includes(searchTerm))
        );
    }
    
    // Filtre par domaine
    if (domainFilter) {
        filteredTrainings = filteredTrainings.filter(training => 
            training.domain === domainFilter
        );
    }
    
    displayTrainings(filteredTrainings);
    updateTrainingCount(filteredTrainings.length);
}

/**
 * Filtre par domaine
 */
function filterByDomain() {
    searchTrainings();
}



/**
 * Affiche le programme d'une formation
 */
async function viewProgram(id) {
    try {
        const response = await fetch(`/api/trainings.php/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const training = result.data;
            currentTrainingId = id;
            
            // Formater le programme pour un affichage plus lisible
            const formattedProgram = formatProgram(training.program);
            
            const programContent = document.getElementById('programContent');
            programContent.innerHTML = `
                <div class="mb-4">
                    <h5 class="text-primary">📖 ${training.title}</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <span class="badge bg-primary">${training.domain}</span>
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-geo-alt"></i> ${training.location || 'N/A'}
                        </div>
                        <div class="col-md-4">
                            <i class="bi bi-calendar"></i> ${training.date_formatted || formatDate(training.date)}
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h6><i class="bi bi-person"></i> Animateurs:</h6>
                    <p class="text-muted">${training.animators || 'Non spécifié'}</p>
                </div>
                
                <div>
                    <h6><i class="bi bi-list-check"></i> Programme détaillé:</h6>
                    <div class="bg-light p-3 rounded">
                        ${formattedProgram}
                    </div>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('programModal'));
            modal.show();
        } else {
            showNotification('Erreur lors du chargement du programme', 'error');
        }
    } catch (error) {
        console.error('Erreur lors du chargement du programme:', error);
        showNotification('Erreur lors du chargement du programme', 'error');
    }
}

/**
 * Formate le programme pour un affichage plus lisible
 */
function formatProgram(program) {
    if (!program) {
        return '<p class="text-muted">Programme non disponible</p>';
    }
    
    // Diviser le programme en lignes
    const lines = program.split('\n');
    let formattedProgram = '';
    
    lines.forEach((line, index) => {
        const trimmedLine = line.trim();
        if (trimmedLine) {
            // Détecter si c'est un jour (commence par "Jour" ou contient "Jour")
            if (trimmedLine.toLowerCase().includes('jour') || /^jour\s*\d+/i.test(trimmedLine)) {
                formattedProgram += `<div class="mb-2"><strong class="text-primary">📅 ${trimmedLine}</strong></div>`;
            } else {
                // C'est un contenu de programme
                formattedProgram += `<div class="mb-1 ms-3">• ${trimmedLine}</div>`;
            }
        }
    });
    
    // Si aucun formatage spécial n'a été appliqué, afficher le texte brut
    if (!formattedProgram.includes('📅')) {
        return `<pre class="mb-0">${program}</pre>`;
    }
    
    return formattedProgram;
}

/**
 * Inscription à une formation
 */
async function registerToTraining() {
    if (!currentTrainingId) {
        showNotification('Aucune formation sélectionnée', 'error');
        return;
    }
    
    // Afficher le formulaire d'inscription
    showRegistrationForm(currentTrainingId);
}

/**
 * Affiche le formulaire d'inscription
 */
function showRegistrationForm(trainingId) {
    currentTrainingId = trainingId;
    
    console.log("=== AFFICHAGE FORMULAIRE D'INSCRIPTION ===");
    console.log("Training ID:", trainingId);
    
    // Vérifier si le modal existe déjà
    const existingModal = document.getElementById('registrationModal');
    console.log("Modal existant trouvé:", !!existingModal);
    
    // Créer le modal d'inscription s'il n'existe pas
    if (!existingModal) {
        console.log("Création du modal d'inscription...");
        createRegistrationModal();
    } else {
        console.log("Modal d'inscription déjà existant - pas de recréation");
    }
    
    // Configuration immédiate du modal
    console.log("=== CONFIGURATION DU MODAL ===");
    
    // Remplir les informations de la formation
    const training = trainings.find(t => t.id == trainingId);
    if (training) {
        const titleElement = document.getElementById('registrationTrainingTitle');
        const infoElement = document.getElementById('registrationTrainingInfo');
        
        if (titleElement) {
            titleElement.textContent = training.title;
            console.log("Titre de formation défini:", training.title);
        }
        if (infoElement) {
            infoElement.textContent = `${training.domain} - ${training.location || 'N/A'} - ${training.date_formatted || formatDate(training.date)}`;
            console.log("Info de formation définie");
        }
    }
    
    // Mettre à jour le training ID sans réinitialiser le formulaire
    const trainingIdInput = document.getElementById('registrationTrainingId');
    if (trainingIdInput) {
        trainingIdInput.value = trainingId;
        console.log("Training ID défini dans le formulaire:", trainingId);
    } else {
        console.log("⚠️  Champ training ID non trouvé");
    }
    
    // Vérifier que tous les champs sont présents et leurs valeurs
    const requiredFields = ['firstName', 'lastName', 'email'];
    const optionalFields = ['phone', 'company', 'position', 'notes'];
    
    console.log("=== VÉRIFICATION DES CHAMPS ===");
    [...requiredFields, ...optionalFields].forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            console.log(`- ${fieldId}: trouvé, valeur = "${field.value}"`);
        } else {
            console.log(`- ${fieldId}: NON TROUVÉ`);
        }
    });
    
    // Afficher le modal
    const modalElement = document.getElementById('registrationModal');
    if (modalElement) {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log("Modal affiché");
    } else {
        console.log("⚠️  Élément modal non trouvé");
    }
    
    console.log("=== FIN AFFICHAGE FORMULAIRE D'INSCRIPTION ===");
}

/**
 * Crée le modal d'inscription
 */
function createRegistrationModal() {
    console.log("Création du modal d'inscription...");
    
    // Vérifier si le modal existe déjà
    const existingModal = document.getElementById('registrationModal');
    if (existingModal) {
        console.log("Modal d'inscription existe déjà - pas de recréation");
        return;
    }
    
    console.log("Modal n'existe pas, création en cours...");
    
    const modalHtml = `
        <div class="modal fade" id="registrationModal" tabindex="-1" aria-labelledby="registrationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registrationModalLabel">📝 Inscription à la formation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6 id="registrationTrainingTitle"></h6>
                            <p class="mb-0" id="registrationTrainingInfo"></p>
                        </div>
                        <form id="registrationForm" novalidate>
                            <input type="hidden" id="registrationTrainingId" name="training_id">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" id="firstName" name="first_name" maxlength="100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="lastName" name="last_name" maxlength="100">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" maxlength="255">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" maxlength="20">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company" class="form-label">Entreprise</label>
                                    <input type="text" class="form-control" id="company" name="company" maxlength="255">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="position" class="form-label">Poste</label>
                                    <input type="text" class="form-control" id="position" name="position" maxlength="255">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (optionnel)</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Informations complémentaires..."></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-success" onclick="submitRegistration()">S'inscrire</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Insérer le modal dans le DOM
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    console.log("Modal d'inscription créé avec succès");
    
    // Attendre que le DOM soit mis à jour
    // Ajouter les écouteurs d'événements aux champs
    addFormEventListeners();
}

/**
 * Valide un email
 * @param {string} email Email à valider
 * @return {boolean} True si l'email est valide
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Soumet l'inscription
 */
async function submitRegistration() {
    console.log("=== DEBUT DE SOUMISSION D'INSCRIPTION ===");
    
    // Diagnostic préalable
    diagnoseSubmissionProblem();
    
    const form = document.getElementById('registrationForm');
    console.log("Formulaire trouvé:", !!form);
    
    if (!form || !form.checkValidity()) {
        console.log("Formulaire invalide ou non trouvé");
        if (form) {
            form.reportValidity();
        } else {
            showNotification('Formulaire d\'inscription non trouvé', 'error');
        }
        return;
    }
    
    console.log("Formulaire valide, récupération des données...");
    
    // UTILISER FORMDATA QUI FONCTIONNE CORRECTEMENT
    const formData = new FormData(form);
    const registrationData = {
        training_id: parseInt(formData.get('training_id')),
        first_name: formData.get('first_name').trim(),
        last_name: formData.get('last_name').trim(),
        email: formData.get('email').trim(),
        phone: formData.get('phone') ? formData.get('phone').trim() : '',
        company: formData.get('company') ? formData.get('company').trim() : '',
        position: formData.get('position') ? formData.get('position').trim() : '',
        notes: formData.get('notes') ? formData.get('notes').trim() : ''
    };
    
    console.log("=== RÉCUPÉRATION VIA FORMDATA ===");
    console.log("Données récupérées:", registrationData);
    
    // Validation côté client
    if (!registrationData.training_id || registrationData.training_id <= 0) {
        console.log("ID de formation invalide:", registrationData.training_id);
        showNotification('Formation invalide', 'error');
        return;
    }
    
    if (!registrationData.first_name || !registrationData.last_name || !registrationData.email) {
        console.log("Champs obligatoires vides");
        console.log("Prénom:", registrationData.first_name);
        console.log("Nom:", registrationData.last_name);
        console.log("Email:", registrationData.email);
        showNotification('Veuillez remplir tous les champs obligatoires', 'error');
        return;
    }
    
    if (!isValidEmail(registrationData.email)) {
        console.log("Email invalide:", registrationData.email);
        showNotification('Email invalide', 'error');
        return;
    }
    
    console.log("Validation côté client réussie, envoi de la requête...");
    
    // Sauvegarder le texte original du bouton
    const submitButton = document.querySelector('#registrationModal .btn-success');
    const originalText = submitButton ? submitButton.textContent : 'S\'inscrire';
    
    try {
        // Afficher un indicateur de chargement
        if (submitButton) {
            submitButton.textContent = 'Inscription en cours...';
            submitButton.disabled = true;
        }
        
        console.log("Envoi de la requête POST vers /api/participants.php");
        console.log("Données envoyées:", JSON.stringify(registrationData));
        
        const response = await fetch('/api/participants.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(registrationData)
        });
        
        console.log("Réponse reçue:", {
            status: response.status,
            statusText: response.statusText,
            headers: Object.fromEntries(response.headers.entries())
        });
        
        const responseText = await response.text();
        console.log("Contenu de la réponse:", responseText);
        
        // Nettoyer la réponse des warnings PHP
        let cleanResponseText = responseText;
        
        // Supprimer les warnings PHP qui commencent par <br /> et <b>Warning</b>
        if (cleanResponseText.includes('<br />') && cleanResponseText.includes('<b>Warning</b>')) {
            console.log("⚠️  Warnings PHP détectés dans la réponse, nettoyage...");
            
            // Trouver le début du JSON valide
            const jsonStart = cleanResponseText.indexOf('{"');
            if (jsonStart !== -1) {
                cleanResponseText = cleanResponseText.substring(jsonStart);
                console.log("Réponse nettoyée:", cleanResponseText);
            }
        }
        
        let data;
        try {
            data = JSON.parse(cleanResponseText);
            console.log("Données JSON parsées:", data);
        } catch (parseError) {
            console.error("Erreur de parsing JSON:", parseError);
            console.log("Réponse brute:", responseText);
            console.log("Réponse nettoyée:", cleanResponseText);
            
            // Essayer de trouver du JSON valide dans la réponse
            const jsonMatch = responseText.match(/\{.*\}/s);
            if (jsonMatch) {
                try {
                    data = JSON.parse(jsonMatch[0]);
                    console.log("JSON extrait et parsé:", data);
                } catch (secondParseError) {
                    console.error("Échec du second parsing:", secondParseError);
                    showNotification('Erreur de communication avec le serveur', 'error');
                    return;
                }
            } else {
                showNotification('Erreur de communication avec le serveur', 'error');
                return;
            }
        }
        
        if (data.success) {
            console.log("Inscription réussie:", data);
            showNotification(data.message || 'Inscription réussie !', 'success');
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('registrationModal'));
            if (modal) {
                modal.hide();
            }
            
            // Recharger les formations si nécessaire
            if (typeof loadTrainings === 'function') {
                loadTrainings();
            }
        } else {
            console.log("Erreur d'inscription:", data);
            showNotification(data.message || 'Erreur lors de l\'inscription', 'error');
        }
        
    } catch (error) {
        console.error("Erreur lors de la soumission:", error);
        showNotification('Erreur de connexion au serveur', 'error');
    } finally {
        // Restaurer le bouton
        if (submitButton) {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        }
    }
    
    console.log("=== FIN DE SOUMISSION D'INSCRIPTION ===");
}

/**
 * Affiche les participants
 */
function showParticipants() {
    // TODO: Implémenter l'affichage des participants
    showNotification('Fonctionnalité de gestion des participants à venir', 'info');
}

/**
 * Met à jour le compteur de formations
 */
function updateTrainingCount(count) {
    const badge = document.getElementById('trainingCount');
    if (badge) {
        badge.textContent = count;
    }
}

/**
 * Affiche/masque le spinner de chargement
 */
function showLoading(show) {
    // TODO: Implémenter un spinner de chargement
}

/**
 * Affiche une notification toast
 */
function showNotification(message, type = 'info') {
    const toast = document.getElementById('notificationToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    if (toast && toastTitle && toastMessage) {
        toastTitle.textContent = type === 'error' ? 'Erreur' : type === 'success' ? 'Succès' : 'Information';
        toastMessage.textContent = message;
        
        // Changer la couleur selon le type
        toast.className = `toast ${type === 'error' ? 'bg-danger text-white' : type === 'success' ? 'bg-success text-white' : ''}`;
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
}

/**
 * Formate une date
 */
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

/**
 * Formate un prix
 */
function formatPrice(price) {
    if (!price) return 'N/A';
    return new Intl.NumberFormat('fr-FR').format(price) + ' F CFA';
}

/**
 * Tronque un texte
 */
function truncateText(text, maxLength) {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
} 

/**
 * Test complet et très bavard du formulaire d'inscription
 */
function comprehensiveFormTest() {
    console.log("🔍 === TEST COMPLET DU FORMULAIRE D'INSCRIPTION ===");
    
    // 1. Vérifier l'existence du modal
    const modal = document.getElementById('registrationModal');
    console.log("1. Modal trouvé:", !!modal);
    
    if (!modal) {
        console.log("❌ ERREUR: Modal non trouvé");
        return;
    }
    
    // 2. Vérifier l'existence du formulaire
    const form = document.getElementById('registrationForm');
    console.log("2. Formulaire trouvé:", !!form);
    
    if (!form) {
        console.log("❌ ERREUR: Formulaire non trouvé");
        return;
    }
    
    // 3. Lister tous les champs du formulaire
    console.log("3. === ANALYSE DES CHAMPS ===");
    const allFields = form.querySelectorAll('input, textarea, select');
    console.log("Nombre total de champs:", allFields.length);
    
    allFields.forEach((field, index) => {
        console.log(`Champ ${index + 1}:`, {
            id: field.id,
            name: field.name,
            type: field.type,
            value: field.value,
            required: field.required,
            hasOnInput: field.hasAttribute('oninput')
        });
    });
    
    // 4. Vérifier les champs spécifiques
    console.log("4. === VÉRIFICATION DES CHAMPS SPÉCIFIQUES ===");
    const specificFields = {
        trainingId: document.getElementById('registrationTrainingId'),
        firstName: document.getElementById('firstName'),
        lastName: document.getElementById('lastName'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        company: document.getElementById('company'),
        position: document.getElementById('position'),
        notes: document.getElementById('notes')
    };
    
    Object.entries(specificFields).forEach(([name, field]) => {
        console.log(`${name}:`, {
            exists: !!field,
            id: field ? field.id : 'N/A',
            name: field ? field.name : 'N/A',
            type: field ? field.type : 'N/A',
            value: field ? field.value : 'N/A',
            required: field ? field.required : 'N/A',
            hasOnInput: field ? field.hasAttribute('oninput') : 'N/A'
        });
    });
    
    // 5. Tester la récupération des valeurs
    console.log("5. === TEST DE RÉCUPÉRATION DES VALEURS ===");
    const values = {};
    Object.entries(specificFields).forEach(([name, field]) => {
        if (field) {
            values[name] = field.value;
            console.log(`${name}: "${field.value}"`);
        } else {
            values[name] = null;
            console.log(`${name}: null (champ non trouvé)`);
        }
    });
    
    // 6. Tester les événements
    console.log("6. === TEST DES ÉVÉNEMENTS ===");
    Object.entries(specificFields).forEach(([name, field]) => {
        if (field) {
            // Tester si l'événement oninput fonctionne
            const originalValue = field.value;
            field.value = 'TEST_VALUE_' + name;
            console.log(`${name} - Valeur changée à: "${field.value}"`);
            
            // Déclencher l'événement input
            const event = new Event('input', { bubbles: true });
            field.dispatchEvent(event);
            
            // Remettre la valeur originale
            field.value = originalValue;
        }
    });
    
    // 7. Tester la validation du formulaire
    console.log("7. === TEST DE VALIDATION ===");
    const isValid = form.checkValidity();
    console.log("Formulaire valide:", isValid);
    
    if (!isValid) {
        console.log("❌ ERREURS DE VALIDATION:");
        const invalidFields = form.querySelectorAll(':invalid');
        invalidFields.forEach(field => {
            console.log(`- ${field.id}: ${field.validationMessage}`);
        });
    }
    
    // 8. Tester la soumission manuelle
    console.log("8. === TEST DE SOUMISSION MANUELLE ===");
    const formData = new FormData(form);
    console.log("FormData créé:", !!formData);
    
    const formDataEntries = [];
    for (let [key, value] of formData.entries()) {
        formDataEntries.push({ key, value });
    }
    console.log("Données FormData:", formDataEntries);
    
    // 9. Test de récupération manuelle des valeurs
    console.log("9. === RÉCUPÉRATION MANUELLE ===");
    const manualValues = {
        training_id: specificFields.trainingId ? specificFields.trainingId.value : '',
        first_name: specificFields.firstName ? specificFields.firstName.value : '',
        last_name: specificFields.lastName ? specificFields.lastName.value : '',
        email: specificFields.email ? specificFields.email.value : '',
        phone: specificFields.phone ? specificFields.phone.value : '',
        company: specificFields.company ? specificFields.company.value : '',
        position: specificFields.position ? specificFields.position.value : '',
        notes: specificFields.notes ? specificFields.notes.value : ''
    };
    
    console.log("Valeurs manuelles:", manualValues);
    
    // 10. Test de validation côté client
    console.log("10. === VALIDATION CÔTÉ CLIENT ===");
    const validationErrors = [];
    
    if (!manualValues.first_name.trim()) {
        validationErrors.push('Prénom manquant');
    }
    if (!manualValues.last_name.trim()) {
        validationErrors.push('Nom manquant');
    }
    if (!manualValues.email.trim()) {
        validationErrors.push('Email manquant');
    } else if (!isValidEmail(manualValues.email)) {
        validationErrors.push('Email invalide');
    }
    
    console.log("Erreurs de validation:", validationErrors);
    
    // 11. Test de préparation des données
    console.log("11. === PRÉPARATION DES DONNÉES ===");
    const preparedData = {
        training_id: parseInt(manualValues.training_id) || 0,
        first_name: manualValues.first_name.trim(),
        last_name: manualValues.last_name.trim(),
        email: manualValues.email.trim(),
        phone: manualValues.phone.trim(),
        company: manualValues.company.trim(),
        position: manualValues.position.trim(),
        notes: manualValues.notes.trim()
    };
    
    console.log("Données préparées:", preparedData);
    
    // 12. Résumé final
    console.log("12. === RÉSUMÉ FINAL ===");
    console.log("✅ Modal:", !!modal);
    console.log("✅ Formulaire:", !!form);
    console.log("✅ Champs trouvés:", Object.values(specificFields).filter(f => f).length, "/", Object.keys(specificFields).length);
    console.log("✅ Formulaire valide:", isValid);
    console.log("✅ Erreurs de validation:", validationErrors.length);
    console.log("✅ Données complètes:", Object.values(preparedData).every(v => v !== null));
    
    if (validationErrors.length === 0 && preparedData.first_name && preparedData.last_name && preparedData.email) {
        console.log("🎉 TOUT EST PRÊT POUR L'INSCRIPTION !");
    } else {
        console.log("❌ PROBLÈMES DÉTECTÉS:");
        validationErrors.forEach(error => console.log(`  - ${error}`));
        if (!preparedData.first_name) console.log("  - Prénom manquant");
        if (!preparedData.last_name) console.log("  - Nom manquant");
        if (!preparedData.email) console.log("  - Email manquant");
    }
    
    console.log("🔍 === FIN DU TEST COMPLET ===");
    
    return {
        modal: !!modal,
        form: !!form,
        fields: Object.values(specificFields).filter(f => f).length,
        valid: isValid,
        errors: validationErrors,
        data: preparedData
    };
}

/**
 * Test du formulaire d'inscription
 */
function testRegistrationForm() {
    console.log("=== TEST DU FORMULAIRE D'INSCRIPTION ===");
    
    // Vérifier si le modal existe
    const modal = document.getElementById('registrationModal');
    console.log("Modal trouvé:", !!modal);
    
    if (modal) {
        // Vérifier tous les champs
        const fields = {
            trainingId: document.getElementById('registrationTrainingId'),
            firstName: document.getElementById('firstName'),
            lastName: document.getElementById('lastName'),
            email: document.getElementById('email'),
            phone: document.getElementById('phone'),
            company: document.getElementById('company'),
            position: document.getElementById('position'),
            notes: document.getElementById('notes')
        };
        
        console.log("Champs trouvés:");
        Object.entries(fields).forEach(([name, field]) => {
            console.log(`- ${name}:`, field ? 'trouvé' : 'NON TROUVÉ');
            if (field) {
                console.log(`  Valeur: "${field.value}"`);
                console.log(`  Type: ${field.type}`);
                console.log(`  Required: ${field.required}`);
            }
        });
        
        // Tester la récupération des valeurs
        const testData = {
            training_id: fields.trainingId ? parseInt(fields.trainingId.value) : 0,
            first_name: fields.firstName ? fields.firstName.value.trim() : '',
            last_name: fields.lastName ? fields.lastName.value.trim() : '',
            email: fields.email ? fields.email.value.trim() : '',
            phone: fields.phone ? fields.phone.value.trim() : '',
            company: fields.company ? fields.company.value.trim() : '',
            position: fields.position ? fields.position.value.trim() : '',
            notes: fields.notes ? fields.notes.value.trim() : ''
        };
        
        console.log("Données de test:", testData);
        
        // Vérifier les champs obligatoires
        const missingFields = [];
        if (!testData.first_name) missingFields.push('prénom');
        if (!testData.last_name) missingFields.push('nom');
        if (!testData.email) missingFields.push('email');
        
        if (missingFields.length > 0) {
            console.log("⚠️  Champs manquants:", missingFields.join(', '));
        } else {
            console.log("✅ Tous les champs obligatoires sont remplis");
        }
        
    } else {
        console.log("❌ Modal non trouvé");
    }
    
    console.log("=== FIN TEST DU FORMULAIRE D'INSCRIPTION ===");
} 

/**
 * Tests automatisés pour diagnostiquer le problème d'inscription
 */
function runAutomatedTests() {
    console.log("🧪 === DÉBUT DES TESTS AUTOMATISÉS ===");
    
    // Test 1: Vérification de l'environnement
    testEnvironment();
    
    // Test 2: Vérification du modal
    testModalCreation();
    
    // Test 3: Vérification des champs
    testFormFields();
    
    // Test 4: Test de saisie automatique
    testDataEntry();
    
    // Test 5: Test de récupération des données
    testDataRetrieval();
    
    // Test 6: Test de soumission
    testSubmission();
    
    // Test 7: Diagnostic spécifique
    setTimeout(() => {
        diagnoseValueRetrieval();
    }, 1000);
    
    console.log("🧪 === FIN DES TESTS AUTOMATISÉS ===");
}

/**
 * Test 1: Vérification de l'environnement
 */
function testEnvironment() {
    console.log("🔍 TEST 1: VÉRIFICATION DE L'ENVIRONNEMENT");
    
    // Vérifier Bootstrap
    const bootstrapAvailable = typeof bootstrap !== 'undefined';
    console.log("✅ Bootstrap disponible:", bootstrapAvailable);
    
    // Vérifier les fonctions essentielles
    const functionsAvailable = {
        showRegistrationForm: typeof showRegistrationForm === 'function',
        createRegistrationModal: typeof createRegistrationModal === 'function',
        submitRegistration: typeof submitRegistration === 'function',
        isValidEmail: typeof isValidEmail === 'function'
    };
    
    console.log("✅ Fonctions disponibles:", functionsAvailable);
    
    // Vérifier les formations
    console.log("✅ Formations chargées:", trainings.length);
    
    // Vérifier le DOM
    const body = document.body;
    console.log("✅ Body disponible:", !!body);
    
    console.log("✅ Test 1 terminé");
}

/**
 * Test simple pour vérifier la création du modal
 */
function testModalCreation() {
    console.log("🧪 === TEST DE CRÉATION DU MODAL ===");
    
    // 1. Supprimer le modal existant s'il y en a un
    const existingModal = document.getElementById('registrationModal');
    if (existingModal) {
        existingModal.remove();
        console.log("✅ Modal existant supprimé");
    }
    
    // 2. Créer un nouveau modal
    console.log("Création d'un nouveau modal...");
    createRegistrationModal();
    
    // 3. Vérifier que le modal a été créé
    const newModal = document.getElementById('registrationModal');
    if (newModal) {
        console.log("✅ Modal créé avec succès");
        
        // 4. Vérifier tous les champs
        const fields = ['firstName', 'lastName', 'email', 'phone', 'company', 'position', 'notes'];
        fields.forEach(fieldId => {
            const field = newModal.querySelector(`#${fieldId}`);
            if (field) {
                console.log(`✅ Champ ${fieldId} trouvé dans le modal`);
            } else {
                console.log(`❌ Champ ${fieldId} NON TROUVÉ dans le modal`);
            }
        });
        
        // 5. Afficher le modal
        const modal = new bootstrap.Modal(newModal);
        modal.show();
        console.log("✅ Modal affiché");
        
        // 6. Attendre un peu puis vérifier les valeurs
        setTimeout(() => {
            console.log("🔍 Vérification des valeurs après affichage...");
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    console.log(`  ${fieldId}: "${field.value}"`);
                }
            });
        }, 1000);
        
    } else {
        console.log("❌ Échec de la création du modal");
    }
    
    console.log("🧪 === FIN DU TEST ===");
}

/**
 * Test 3: Vérification des champs
 */
function testFormFields() {
    console.log("🔍 TEST 3: VÉRIFICATION DES CHAMPS");
    
    const requiredFields = ['firstName', 'lastName', 'email'];
    const optionalFields = ['phone', 'company', 'position', 'notes'];
    const allFields = [...requiredFields, ...optionalFields];
    
    const fieldStatus = {};
    
    allFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        fieldStatus[fieldId] = {
            exists: !!field,
            id: field ? field.id : null,
            name: field ? field.name : null,
            type: field ? field.type : null,
            required: field ? field.required : null,
            value: field ? field.value : null
        };
    });
    
    console.log("📋 Statut des champs:", fieldStatus);
    
    // Vérifier les champs obligatoires
    const missingRequired = requiredFields.filter(fieldId => !fieldStatus[fieldId].exists);
    if (missingRequired.length > 0) {
        console.log("❌ Champs obligatoires manquants:", missingRequired);
    } else {
        console.log("✅ Tous les champs obligatoires présents");
    }
    
    console.log("✅ Test 3 terminé");
}

/**
 * Test 4: Test de saisie automatique
 */
function testDataEntry() {
    console.log("🔍 TEST 4: TEST DE SAISIE AUTOMATIQUE");
    
    // Demander confirmation avant de remplir les champs
    const shouldFillFields = confirm("Ce test va remplir les champs avec des données de test. Continuer ?");
    if (!shouldFillFields) {
        console.log("❌ Test annulé par l'utilisateur");
        return;
    }
    
    const testData = {
        firstName: 'Test',
        lastName: 'User',
        email: 'test@example.com',
        phone: '0123456789',
        company: 'TestCorp',
        position: 'Développeur',
        notes: 'Test automatique'
    };
    
    // Remplir les champs avec des données de test
    Object.entries(testData).forEach(([fieldId, value]) => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = value;
            console.log(`✅ ${fieldId} rempli avec: "${value}"`);
            
            // Déclencher l'événement input
            const event = new Event('input', { bubbles: true });
            field.dispatchEvent(event);
        } else {
            console.log(`❌ Champ ${fieldId} non trouvé`);
        }
    });
    
    // Vérifier que les valeurs ont été définies
    setTimeout(() => {
        console.log("🔍 Vérification des valeurs après saisie:");
        Object.entries(testData).forEach(([fieldId, expectedValue]) => {
            const field = document.getElementById(fieldId);
            if (field) {
                const actualValue = field.value;
                const match = actualValue === expectedValue;
                console.log(`  ${fieldId}: "${actualValue}" ${match ? '✅' : '❌'} (attendu: "${expectedValue}")`);
            }
        });
    }, 100);
    
    console.log("✅ Test 4 terminé");
}

/**
 * Test 5: Test de récupération des données
 */
function testDataRetrieval() {
    console.log("🔍 TEST 5: TEST DE RÉCUPÉRATION DES DONNÉES");
    
    // Méthode 1: Récupération directe
    const directValues = {
        trainingId: document.getElementById('registrationTrainingId')?.value,
        firstName: document.getElementById('firstName')?.value,
        lastName: document.getElementById('lastName')?.value,
        email: document.getElementById('email')?.value,
        phone: document.getElementById('phone')?.value,
        company: document.getElementById('company')?.value,
        position: document.getElementById('position')?.value,
        notes: document.getElementById('notes')?.value
    };
    
    console.log("📊 Valeurs récupérées directement:", directValues);
    
    // Méthode 2: Récupération via FormData
    const form = document.getElementById('registrationForm');
    if (form) {
        const formData = new FormData(form);
        const formDataValues = {};
        for (let [key, value] of formData.entries()) {
            formDataValues[key] = value;
        }
        console.log("📊 Valeurs récupérées via FormData:", formDataValues);
    }
    
    // Méthode 3: Récupération via querySelector
    const queryValues = {
        trainingId: form?.querySelector('[name="training_id"]')?.value,
        firstName: form?.querySelector('[name="first_name"]')?.value,
        lastName: form?.querySelector('[name="last_name"]')?.value,
        email: form?.querySelector('[name="email"]')?.value,
        phone: form?.querySelector('[name="phone"]')?.value,
        company: form?.querySelector('[name="company"]')?.value,
        position: form?.querySelector('[name="position"]')?.value,
        notes: form?.querySelector('[name="notes"]')?.value
    };
    
    console.log("📊 Valeurs récupérées via querySelector:", queryValues);
    
    console.log("✅ Test 5 terminé");
}

/**
 * Test 6: Test de soumission
 */
function testSubmission() {
    console.log("🔍 TEST 6: TEST DE SOUMISSION");
    
    // Simuler la récupération des données comme dans submitRegistration
    const fields = {
        trainingId: document.getElementById('registrationTrainingId'),
        firstName: document.getElementById('firstName'),
        lastName: document.getElementById('lastName'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        company: document.getElementById('company'),
        position: document.getElementById('position'),
        notes: document.getElementById('notes')
    };
    
    console.log("🔍 Champs trouvés:");
    Object.entries(fields).forEach(([name, field]) => {
        console.log(`  ${name}: ${field ? 'trouvé' : 'NON TROUVÉ'}`);
        if (field) {
            console.log(`    Valeur: "${field.value}"`);
            console.log(`    Type: ${field.type}`);
            console.log(`    Required: ${field.required}`);
        }
    });
    
    // Préparer les données comme dans submitRegistration
    const registrationData = {
        training_id: fields.trainingId ? parseInt(fields.trainingId.value) : 0,
        first_name: fields.firstName ? fields.firstName.value.trim() : '',
        last_name: fields.lastName ? fields.lastName.value.trim() : '',
        email: fields.email ? fields.email.value.trim() : '',
        phone: fields.phone ? fields.phone.value.trim() : '',
        company: fields.company ? fields.company.value.trim() : '',
        position: fields.position ? fields.position.value.trim() : '',
        notes: fields.notes ? fields.notes.value.trim() : ''
    };
    
    console.log("📊 Données préparées pour soumission:", registrationData);
    
    // Validation côté client
    const validationErrors = [];
    if (!registrationData.first_name) validationErrors.push('Prénom manquant');
    if (!registrationData.last_name) validationErrors.push('Nom manquant');
    if (!registrationData.email) validationErrors.push('Email manquant');
    else if (!isValidEmail(registrationData.email)) validationErrors.push('Email invalide');
    
    console.log("✅ Erreurs de validation:", validationErrors);
    
    if (validationErrors.length === 0) {
        console.log("🎉 Données prêtes pour l'envoi au serveur");
    } else {
        console.log("❌ Données invalides:", validationErrors);
    }
    
    console.log("✅ Test 6 terminé");
}

/**
 * Nettoie tous les champs du formulaire
 */
function clearFormFields() {
    console.log("🧹 Nettoyage des champs du formulaire...");
    
    const fields = ['firstName', 'lastName', 'email', 'phone', 'company', 'position', 'notes'];
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = '';
            console.log(`✅ ${fieldId} nettoyé`);
        }
    });
    
    console.log("✅ Tous les champs ont été nettoyés");
}

/**
 * Ajoute les écouteurs d'événements aux champs du formulaire
 */
function addFormEventListeners() {
    console.log("🔧 === AJOUT DES ÉCOUTEURS D'ÉVÉNEMENTS ===");
    
    const fields = ['firstName', 'lastName', 'email', 'phone', 'company', 'position', 'notes'];
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            console.log(`🔧 Configuration de l'écouteur pour ${fieldId}:`);
            console.log(`  - Type: ${field.type}`);
            console.log(`  - Required: ${field.required}`);
            console.log(`  - ID: ${field.id}`);
            console.log(`  - Name: ${field.name}`);
            console.log(`  - Valeur actuelle: "${field.value}"`);
            
            // Supprimer les anciens écouteurs s'ils existent
            field.removeEventListener('input', handleFieldChange);
            field.removeEventListener('change', handleFieldChange);
            
            // Ajouter le nouvel écouteur
            field.addEventListener('input', handleFieldChange);
            field.addEventListener('change', handleFieldChange);
            
            console.log(`✅ Écouteur d'événement ajouté pour ${fieldId}`);
            
            // Test immédiat de l'écouteur
            setTimeout(() => {
                console.log(`🧪 Test de l'écouteur pour ${fieldId}:`);
                console.log(`  - Valeur avant test: "${field.value}"`);
                
                // Simuler un événement input
                const testEvent = new Event('input', { bubbles: true });
                field.dispatchEvent(testEvent);
                
                console.log(`  - Valeur après test: "${field.value}"`);
            }, 100);
            
        } else {
            console.log(`❌ Champ ${fieldId} non trouvé`);
        }
    });
    
    console.log("✅ Tous les écouteurs d'événements ont été configurés");
}

/**
 * Gère les changements dans les champs du formulaire
 */
function handleFieldChange(event) {
    const field = event.target;
    const fieldId = field.id;
    const value = field.value;
    
    console.log(`📝 ${fieldId} changed: "${value}"`);
    console.log(`  - Type d'événement: ${event.type}`);
    console.log(`  - Bubbles: ${event.bubbles}`);
    console.log(`  - Target ID: ${field.id}`);
    console.log(`  - Target value: "${field.value}"`);
    console.log(`  - Target type: ${field.type}`);
    console.log(`  - Target required: ${field.required}`);
    
    // Vérifier si la valeur est bien mise à jour
    setTimeout(() => {
        const currentValue = document.getElementById(fieldId)?.value;
        console.log(`  - Vérification après 100ms: "${currentValue}"`);
        if (currentValue !== value) {
            console.log(`⚠️ ATTENTION: Valeur perdue pour ${fieldId}!`);
        }
    }, 100);
}

/**
 * Test spécifique pour diagnostiquer le problème des champs obligatoires
 */
function diagnoseRequiredFields() {
    console.log("🔬 === DIAGNOSTIC DES CHAMPS OBLIGATOIRES ===");
    
    const requiredFields = ['firstName', 'lastName', 'email'];
    
    requiredFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            console.log(`🔍 Analyse du champ ${fieldId}:`);
            console.log(`  - Type: ${field.type}`);
            console.log(`  - Required: ${field.required}`);
            console.log(`  - ID: ${field.id}`);
            console.log(`  - Name: ${field.name}`);
            console.log(`  - Valeur actuelle: "${field.value}"`);
            console.log(`  - Placeholder: "${field.placeholder}"`);
            console.log(`  - Disabled: ${field.disabled}`);
            console.log(`  - Readonly: ${field.readOnly}`);
            
            // Vérifier les écouteurs d'événements
            const listeners = getEventListeners(field);
            console.log(`  - Écouteurs d'événements:`, listeners);
            
            // Test de saisie manuelle
            console.log(`  - Test de saisie manuelle:`);
            console.log(`    Tapez quelque chose dans le champ ${fieldId} et regardez les logs...`);
            
        } else {
            console.log(`❌ Champ ${fieldId} non trouvé`);
        }
    });
    
    console.log("🔬 === FIN DU DIAGNOSTIC DES CHAMPS OBLIGATOIRES ===");
}

/**
 * Fonction utilitaire pour obtenir les écouteurs d'événements (approximation)
 */
function getEventListeners(element) {
    // Cette fonction est une approximation car on ne peut pas accéder directement aux écouteurs
    const events = ['input', 'change', 'keyup', 'keydown', 'focus', 'blur'];
    const listeners = {};
    
    events.forEach(eventType => {
        try {
            // On ne peut pas vraiment détecter les écouteurs, mais on peut tester
            const testEvent = new Event(eventType, { bubbles: true });
            element.dispatchEvent(testEvent);
            listeners[eventType] = 'Testé';
        } catch (e) {
            listeners[eventType] = 'Erreur';
        }
    });
    
    return listeners;
}

/**
 * Test spécifique pour diagnostiquer le problème de récupération des valeurs
 */
function diagnoseValueRetrieval() {
    console.log("🔬 === DIAGNOSTIC DE RÉCUPÉRATION DES VALEURS ===");
    
    // 1. Vérifier si le modal existe
    const modal = document.getElementById('registrationModal');
    if (!modal) {
        console.log("❌ Modal non trouvé - Création d'un modal de test");
        createRegistrationModal();
    }
    
    // 2. Demander confirmation avant de remplir les champs
    const shouldFillFields = confirm("Ce diagnostic va remplir les champs avec des données de test. Continuer ?");
    if (!shouldFillFields) {
        console.log("❌ Diagnostic annulé par l'utilisateur");
        return;
    }
    
    const testData = {
        firstName: 'John',
        lastName: 'Doe',
        email: 'john.doe@example.com',
        phone: '0123456789',
        company: 'TestCompany',
        position: 'Developer',
        notes: 'Test notes'
    };
    
    console.log("📝 Remplissage des champs avec des données de test...");
    Object.entries(testData).forEach(([fieldId, value]) => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = value;
            console.log(`✅ ${fieldId} = "${value}"`);
        } else {
            console.log(`❌ Champ ${fieldId} non trouvé`);
        }
    });
    
    // 3. Attendre un peu puis vérifier les valeurs
    setTimeout(() => {
        console.log("🔍 Vérification des valeurs après remplissage...");
        
        Object.entries(testData).forEach(([fieldId, expectedValue]) => {
            const field = document.getElementById(fieldId);
            if (field) {
                const actualValue = field.value;
                const match = actualValue === expectedValue;
                console.log(`  ${fieldId}: "${actualValue}" ${match ? '✅' : '❌'} (attendu: "${expectedValue}")`);
                
                if (!match) {
                    console.log(`    🔍 Debug ${fieldId}:`);
                    console.log(`      - field.value: "${field.value}"`);
                    console.log(`      - field.getAttribute('value'): "${field.getAttribute('value')}"`);
                    console.log(`      - field.defaultValue: "${field.defaultValue}"`);
                    console.log(`      - field.type: "${field.type}"`);
                    console.log(`      - field.id: "${field.id}"`);
                    console.log(`      - field.name: "${field.name}"`);
                }
            }
        });
        
        // 4. Tester différentes méthodes de récupération
        console.log("🔍 Test des différentes méthodes de récupération...");
        
        const methods = {
            'getElementById': (id) => document.getElementById(id)?.value,
            'querySelector': (id) => document.querySelector(`#${id}`)?.value,
            'querySelector name': (id) => document.querySelector(`[name="${id.replace(/([A-Z])/g, '_$1').toLowerCase()}"]`)?.value,
            'FormData': (id) => {
                const form = document.getElementById('registrationForm');
                if (form) {
                    const formData = new FormData(form);
                    return formData.get(id.replace(/([A-Z])/g, '_$1').toLowerCase());
                }
                return null;
            }
        };
        
        Object.entries(methods).forEach(([methodName, method]) => {
            console.log(`📊 Méthode "${methodName}":`);
            Object.keys(testData).forEach(fieldId => {
                const value = method(fieldId);
                console.log(`  ${fieldId}: "${value}"`);
            });
        });
        
        // 5. Test de la fonction submitRegistration
        console.log("🔍 Test de la fonction submitRegistration...");
        
        // Simuler la récupération comme dans submitRegistration
        const fields = {
            trainingId: document.getElementById('registrationTrainingId'),
            firstName: document.getElementById('firstName'),
            lastName: document.getElementById('lastName'),
            email: document.getElementById('email'),
            phone: document.getElementById('phone'),
            company: document.getElementById('company'),
            position: document.getElementById('position'),
            notes: document.getElementById('notes')
        };
        
        console.log("📊 Champs trouvés par submitRegistration:");
        Object.entries(fields).forEach(([name, field]) => {
            console.log(`  ${name}: ${field ? 'trouvé' : 'NON TROUVÉ'}`);
            if (field) {
                console.log(`    Valeur: "${field.value}"`);
                console.log(`    Type: ${field.type}`);
                console.log(`    Required: ${field.required}`);
            }
        });
        
        // 6. Résumé du diagnostic
        console.log("📋 === RÉSUMÉ DU DIAGNOSTIC ===");
        const allFieldsFound = Object.values(fields).every(field => field !== null);
        const allValuesCorrect = Object.entries(testData).every(([fieldId, expectedValue]) => {
            const field = document.getElementById(fieldId);
            return field && field.value === expectedValue;
        });
        
        console.log("✅ Tous les champs trouvés:", allFieldsFound);
        console.log("✅ Toutes les valeurs correctes:", allValuesCorrect);
        
        if (allFieldsFound && allValuesCorrect) {
            console.log("🎉 DIAGNOSTIC RÉUSSI: Tout fonctionne correctement");
        } else {
            console.log("❌ PROBLÈMES DÉTECTÉS:");
            if (!allFieldsFound) console.log("  - Certains champs ne sont pas trouvés");
            if (!allValuesCorrect) console.log("  - Certaines valeurs ne sont pas correctement récupérées");
        }
        
        // 7. Demander si l'utilisateur veut nettoyer les champs
        const shouldClear = confirm("Voulez-vous nettoyer les champs après le diagnostic ?");
        if (shouldClear) {
            clearFormFields();
        }
        
        console.log("🔬 === FIN DU DIAGNOSTIC ===");
        
    }, 500);
}

/**
 * Diagnostic spécifique pour le problème des champs obligatoires
 */
function diagnoseSubmissionProblem() {
    console.log("🔬 === DIAGNOSTIC DU PROBLÈME DE SOUMISSION ===");
    
    // 1. Vérifier si le modal existe
    const modal = document.getElementById('registrationModal');
    console.log("1. Modal existe:", !!modal);
    
    if (!modal) {
        console.log("❌ Modal non trouvé - problème de création");
        return;
    }
    
    // 2. Vérifier si le modal est visible
    const isVisible = modal.classList.contains('show');
    console.log("2. Modal visible:", isVisible);
    
    // 3. Vérifier tous les champs individuellement
    const fields = {
        trainingId: document.getElementById('registrationTrainingId'),
        firstName: document.getElementById('firstName'),
        lastName: document.getElementById('lastName'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        company: document.getElementById('company'),
        position: document.getElementById('position'),
        notes: document.getElementById('notes')
    };
    
    console.log("3. Vérification des champs:");
    Object.entries(fields).forEach(([name, field]) => {
        if (field) {
            console.log(`  ✅ ${name}: trouvé`);
            console.log(`     - ID: ${field.id}`);
            console.log(`     - Name: ${field.name}`);
            console.log(`     - Type: ${field.type}`);
            console.log(`     - Required: ${field.required}`);
            console.log(`     - Value: "${field.value}"`);
            console.log(`     - Visible: ${field.offsetParent !== null}`);
            console.log(`     - Disabled: ${field.disabled}`);
            console.log(`     - Readonly: ${field.readOnly}`);
        } else {
            console.log(`  ❌ ${name}: NON TROUVÉ`);
        }
    });
    
    // 4. Test de récupération alternative
    console.log("4. Test de récupération alternative:");
    
    // Méthode 1: getElementById
    const firstName1 = document.getElementById('firstName');
    console.log(`  getElementById('firstName'): ${firstName1 ? firstName1.value : 'null'}`);
    
    // Méthode 2: querySelector
    const firstName2 = document.querySelector('#firstName');
    console.log(`  querySelector('#firstName'): ${firstName2 ? firstName2.value : 'null'}`);
    
    // Méthode 3: querySelector dans le modal
    const firstName3 = modal.querySelector('#firstName');
    console.log(`  modal.querySelector('#firstName'): ${firstName3 ? firstName3.value : 'null'}`);
    
    // Méthode 4: FormData
    const form = document.getElementById('registrationForm');
    if (form) {
        const formData = new FormData(form);
        console.log(`  FormData - firstName: "${formData.get('first_name')}"`);
        console.log(`  FormData - lastName: "${formData.get('last_name')}"`);
        console.log(`  FormData - email: "${formData.get('email')}"`);
    }
    
    // 5. Test de saisie manuelle
    console.log("5. Test de saisie manuelle:");
    console.log("  Remplissez manuellement les champs obligatoires et cliquez sur S'inscrire");
    console.log("  Puis regardez les logs de soumission...");
    
    console.log("🔬 === FIN DU DIAGNOSTIC ===");
}

/**
 * Lance tous les tests automatiquement
 */
function launchAllTests() {
    console.log("🚀 === LANCEMENT DE TOUS LES TESTS ===");
    
    // Attendre que la page soit chargée
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', runAutomatedTests);
    } else {
        runAutomatedTests();
    }
}

// Lancer les tests automatiquement au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    console.log("🧪 Tests automatiques prêts (non lancés automatiquement)");
    console.log("🧪 Pour lancer les tests, utilisez les boutons dans le modal ou appelez runAutomatedTests()");
    
    // Ne plus lancer les tests automatiquement
    // setTimeout(() => {
    //     console.log("🧪 Lancement automatique des tests...");
    //     launchAllTests();
    // }, 2000);
}); 