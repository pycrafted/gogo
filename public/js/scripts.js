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
 * Affiche les formations dans le tableau
 */
function displayTrainings(trainingsToShow) {
    console.log('Affichage des formations:', trainingsToShow.length);
    const tbody = document.getElementById('trainingsTableBody');
    if (!tbody) {
        console.error('Element trainingsTableBody non trouvé');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (trainingsToShow.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted">
                    Aucune formation trouvée
                </td>
            </tr>
        `;
        return;
    }
    
    trainingsToShow.forEach((training, index) => {
        console.log(`Affichage formation ${index + 1}:`, training);
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${training.id}</td>
            <td><span class="badge bg-primary">${training.domain}</span></td>
            <td><strong>${training.title}</strong></td>
            <td>${training.location || 'N/A'}</td>
            <td>${training.date_formatted || formatDate(training.date)}</td>
            <td>${training.duration_formatted || (training.duration ? training.duration + ' jour(s)' : 'N/A')}</td>
            <td>${training.price_formatted || (training.price ? formatPrice(training.price) : 'N/A')}</td>
            <td>${truncateText(training.animators || '', 30)}</td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-info" onclick="viewProgram(${training.id})" title="Voir le programme">
                        📖
                    </button>
                    <button class="btn btn-outline-primary" onclick="editTraining(${training.id})" title="Modifier">
                        ✏️
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteTraining(${training.id})" title="Supprimer">
                        🗑️
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
    
    console.log('Tableau mis à jour avec', trainingsToShow.length, 'formations');
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
 * Affiche le formulaire d'ajout
 */
function showAddForm() {
    currentTrainingId = null;
    document.getElementById('modalTitle').textContent = 'Ajouter une formation';
    document.getElementById('trainingForm').reset();
    document.getElementById('trainingId').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('trainingModal'));
    modal.show();
}

/**
 * Édite une formation
 */
async function editTraining(id) {
    try {
        const response = await fetch(`/api/trainings.php/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const training = result.data;
            currentTrainingId = id;
            
            // Remplir le formulaire
            document.getElementById('modalTitle').textContent = 'Modifier la formation';
            document.getElementById('trainingId').value = training.id;
            document.getElementById('domain').value = training.domain;
            document.getElementById('title').value = training.title;
            document.getElementById('location').value = training.location || '';
            document.getElementById('date').value = training.date;
            document.getElementById('duration').value = training.duration || '';
            document.getElementById('price').value = training.price || '';
            document.getElementById('animators').value = training.animators || '';
            document.getElementById('program').value = training.program || '';
            
            const modal = new bootstrap.Modal(document.getElementById('trainingModal'));
            modal.show();
        } else {
            showNotification('Erreur lors du chargement de la formation', 'error');
        }
    } catch (error) {
        console.error('Erreur lors de l\'édition:', error);
        showNotification('Erreur lors de l\'édition de la formation', 'error');
    }
}

/**
 * Sauvegarde une formation (ajout ou modification)
 */
async function saveTraining() {
    const form = document.getElementById('trainingForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const trainingData = {
        domain: document.getElementById('domain').value,
        title: document.getElementById('title').value,
        location: document.getElementById('location').value,
        date: document.getElementById('date').value,
        duration: parseInt(document.getElementById('duration').value),
        price: parseFloat(document.getElementById('price').value),
        animators: document.getElementById('animators').value,
        program: document.getElementById('program').value
    };
    
    try {
        const url = currentTrainingId ? `/api/trainings.php/${currentTrainingId}` : '/api/trainings.php';
        const method = currentTrainingId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(trainingData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification(
                currentTrainingId ? 'Formation modifiée avec succès' : 'Formation ajoutée avec succès', 
                'success'
            );
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('trainingModal'));
            modal.hide();
            
            // Recharger les formations
            await loadTrainings();
        } else {
            showNotification(result.message || 'Erreur lors de la sauvegarde', 'error');
        }
    } catch (error) {
        console.error('Erreur lors de la sauvegarde:', error);
        showNotification('Erreur lors de la sauvegarde de la formation', 'error');
    }
}

/**
 * Supprime une formation
 */
function deleteTraining(id) {
    currentTrainingId = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

/**
 * Confirme la suppression d'une formation
 */
async function confirmDelete() {
    try {
        const response = await fetch(`/api/trainings.php/${currentTrainingId}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Formation supprimée avec succès', 'success');
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
            modal.hide();
            
            // Recharger les formations
            await loadTrainings();
        } else {
            showNotification(result.message || 'Erreur lors de la suppression', 'error');
        }
    } catch (error) {
        console.error('Erreur lors de la suppression:', error);
        showNotification('Erreur lors de la suppression de la formation', 'error');
    }
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
            
            const programContent = document.getElementById('programContent');
            programContent.innerHTML = `
                <div class="mb-3">
                    <h6>Formation: ${training.title}</h6>
                    <p class="text-muted">${training.domain} - ${training.location || 'N/A'} - ${training.date_formatted || formatDate(training.date)}</p>
                </div>
                <div class="mb-3">
                    <h6>Animateurs:</h6>
                    <p>${training.animators || 'Non spécifié'}</p>
                </div>
                <div>
                    <h6>Programme détaillé:</h6>
                    <pre class="bg-light p-3 rounded">${training.program || 'Programme non disponible'}</pre>
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
    // Créer le modal d'inscription s'il n'existe pas
    if (!document.getElementById('registrationModal')) {
        createRegistrationModal();
    }
    
    // Remplir les informations de la formation
    const training = trainings.find(t => t.id == trainingId);
    if (training) {
        document.getElementById('registrationTrainingTitle').textContent = training.title;
        document.getElementById('registrationTrainingInfo').textContent = 
            `${training.domain} - ${training.location || 'N/A'} - ${training.date_formatted || formatDate(training.date)}`;
    }
    
    // Réinitialiser le formulaire
    document.getElementById('registrationForm').reset();
    document.getElementById('registrationTrainingId').value = trainingId;
    
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
 * Soumet l'inscription
 */
async function submitRegistration() {
    const form = document.getElementById('registrationForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const registrationData = {
        training_id: parseInt(document.getElementById('registrationTrainingId').value),
        first_name: document.getElementById('firstName').value,
        last_name: document.getElementById('lastName').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        company: document.getElementById('company').value,
        position: document.getElementById('position').value,
        notes: document.getElementById('notes').value
    };
    
    try {
        const response = await fetch('/api/participants.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(registrationData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Inscription réussie ! Vous recevrez une confirmation par email.', 'success');
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('registrationModal'));
            modal.hide();
            
            // Fermer aussi le modal du programme
            const programModal = bootstrap.Modal.getInstance(document.getElementById('programModal'));
            if (programModal) {
                programModal.hide();
            }
        } else {
            showNotification(result.message || 'Erreur lors de l\'inscription', 'error');
        }
    } catch (error) {
        console.error('Erreur lors de l\'inscription:', error);
        showNotification('Erreur lors de l\'inscription', 'error');
    }
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
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

/**
 * Tronque un texte
 */
function truncateText(text, maxLength) {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
} 