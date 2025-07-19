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
    
    // Créer le modal d'inscription s'il n'existe pas
    if (!document.getElementById('registrationModal')) {
        createRegistrationModal();
    }
    
    // Remplir les informations de la formation
    const training = trainings.find(t => t.id == trainingId);
    if (training) {
        const titleElement = document.getElementById('registrationTrainingTitle');
        const infoElement = document.getElementById('registrationTrainingInfo');
        
        if (titleElement) {
            titleElement.textContent = training.title;
        }
        if (infoElement) {
            infoElement.textContent = `${training.domain} - ${training.location || 'N/A'} - ${training.date_formatted || formatDate(training.date)}`;
        }
    }
    
    // Réinitialiser le formulaire
    const form = document.getElementById('registrationForm');
    if (form) {
        form.reset();
    }
    
    const trainingIdInput = document.getElementById('registrationTrainingId');
    if (trainingIdInput) {
        trainingIdInput.value = trainingId;
    }
    
    // Afficher le modal
    const modal = new bootstrap.Modal(document.getElementById('registrationModal'));
    modal.show();
}

/**
 * Crée le modal d'inscription
 */
function createRegistrationModal() {
    const modalHtml = `
        <div class="modal fade" id="registrationModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">📝 Inscription à la formation</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <h6 id="registrationTrainingTitle"></h6>
                            <p class="mb-0" id="registrationTrainingInfo"></p>
                        </div>
                        <form id="registrationForm">
                            <input type="hidden" id="registrationTrainingId">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="firstName" class="form-label">Prénom *</label>
                                    <input type="text" class="form-control" id="firstName" required maxlength="100">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastName" class="form-label">Nom *</label>
                                    <input type="text" class="form-control" id="lastName" required maxlength="100">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" required maxlength="255">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Téléphone</label>
                                    <input type="tel" class="form-control" id="phone" maxlength="20">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company" class="form-label">Entreprise</label>
                                    <input type="text" class="form-control" id="company" maxlength="255">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="position" class="form-label">Poste</label>
                                    <input type="text" class="form-control" id="position" maxlength="255">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes (optionnel)</label>
                                <textarea class="form-control" id="notes" rows="3" placeholder="Informations complémentaires..."></textarea>
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
    
    document.body.insertAdjacentHTML('beforeend', modalHtml);
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
    
    const trainingIdInput = document.getElementById('registrationTrainingId');
    const firstNameInput = document.getElementById('firstName');
    const lastNameInput = document.getElementById('lastName');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    const companyInput = document.getElementById('company');
    const positionInput = document.getElementById('position');
    const notesInput = document.getElementById('notes');
    
    console.log("Champs trouvés:", {
        trainingId: !!trainingIdInput,
        firstName: !!firstNameInput,
        lastName: !!lastNameInput,
        email: !!emailInput,
        phone: !!phoneInput,
        company: !!companyInput,
        position: !!positionInput,
        notes: !!notesInput
    });
    
    if (!trainingIdInput || !firstNameInput || !lastNameInput || !emailInput) {
        console.log("Champs obligatoires manquants");
        showNotification('Champs obligatoires manquants', 'error');
        return;
    }
    
    const registrationData = {
        training_id: parseInt(trainingIdInput.value),
        first_name: firstNameInput.value.trim(),
        last_name: lastNameInput.value.trim(),
        email: emailInput.value.trim(),
        phone: phoneInput ? phoneInput.value.trim() : '',
        company: companyInput ? companyInput.value.trim() : '',
        position: positionInput ? positionInput.value.trim() : '',
        notes: notesInput ? notesInput.value.trim() : ''
    };
    
    console.log("Données d'inscription préparées:", registrationData);
    
    // Validation côté client
    if (!registrationData.training_id || registrationData.training_id <= 0) {
        console.log("ID de formation invalide:", registrationData.training_id);
        showNotification('Formation invalide', 'error');
        return;
    }
    
    if (!registrationData.first_name || !registrationData.last_name || !registrationData.email) {
        console.log("Champs obligatoires vides");
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