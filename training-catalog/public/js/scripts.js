/**
 * Scripts JavaScript pour l'application de catalogue de formations
 * Couche Presentation - Gestion de l'interface utilisateur et des interactions AJAX
 */

// Variables globales
let trainings = [];
let currentTrainingId = null;
let deleteTrainingId = null;

// Configuration de l'API
const API_BASE_URL = 'api/trainings.php';

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', function() {
    console.log('Application de catalogue de formations initialisée');
    
    // Chargement initial des formations
    loadTrainings();
    
    // Événements de recherche et filtrage
    setupEventListeners();
    
    // Configuration des modales Bootstrap
    setupModals();
});

/**
 * Configuration des écouteurs d'événements
 */
function setupEventListeners() {
    // Recherche en temps réel
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterTrainings, 300));
    }
    
    // Filtre par domaine
    const domainFilter = document.getElementById('domainFilter');
    if (domainFilter) {
        domainFilter.addEventListener('change', filterTrainings);
    }
    
    // Validation du formulaire
    const trainingForm = document.getElementById('trainingForm');
    if (trainingForm) {
        trainingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveTraining();
        });
    }
}

/**
 * Configuration des modales Bootstrap
 */
function setupModals() {
    // Modal de formation
    const trainingModal = document.getElementById('trainingModal');
    if (trainingModal) {
        trainingModal.addEventListener('hidden.bs.modal', function() {
            resetForm();
        });
    }
    
    // Modal de suppression
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('hidden.bs.modal', function() {
            deleteTrainingId = null;
        });
    }
}

/**
 * Chargement des formations depuis l'API
 */
async function loadTrainings() {
    try {
        showLoading(true);
        
        const response = await fetch(API_BASE_URL, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            trainings = result.data || [];
            displayTrainings(trainings);
            showToast('Succès', 'Formations chargées avec succès', 'success');
        } else {
            throw new Error(result.error || 'Erreur lors du chargement des formations');
        }
        
    } catch (error) {
        console.error('Erreur lors du chargement des formations:', error);
        showToast('Erreur', 'Impossible de charger les formations', 'error');
        displayTrainings([]);
    } finally {
        showLoading(false);
    }
}

/**
 * Affichage des formations dans le tableau
 * @param {Array} trainingsList - Liste des formations à afficher
 */
function displayTrainings(trainingsList) {
    const tbody = document.getElementById('trainingsTableBody');
    const noDataMessage = document.getElementById('noDataMessage');
    
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    if (trainingsList.length === 0) {
        tbody.style.display = 'none';
        if (noDataMessage) {
            noDataMessage.classList.remove('d-none');
        }
        return;
    }
    
    tbody.style.display = 'table-row-group';
    if (noDataMessage) {
        noDataMessage.classList.add('d-none');
    }
    
    trainingsList.forEach(training => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${training.id}</strong></td>
            <td><span class="badge bg-primary">${training.domain}</span></td>
            <td>${training.title}</td>
            <td>${training.date_formatted || formatDate(training.date)}</td>
            <td>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-warning btn-sm" 
                            onclick="editTraining(${training.id})" 
                            title="Modifier">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" 
                            onclick="deleteTraining(${training.id})" 
                            title="Supprimer">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

/**
 * Filtrage des formations
 */
function filterTrainings() {
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const domainFilter = document.getElementById('domainFilter')?.value || '';
    
    const filteredTrainings = trainings.filter(training => {
        const matchesSearch = !searchTerm || 
            training.title.toLowerCase().includes(searchTerm) ||
            training.domain.toLowerCase().includes(searchTerm);
        
        const matchesDomain = !domainFilter || training.domain === domainFilter;
        
        return matchesSearch && matchesDomain;
    });
    
    displayTrainings(filteredTrainings);
}

/**
 * Effacement des filtres
 */
function clearFilters() {
    const searchInput = document.getElementById('searchInput');
    const domainFilter = document.getElementById('domainFilter');
    
    if (searchInput) searchInput.value = '';
    if (domainFilter) domainFilter.value = '';
    
    displayTrainings(trainings);
    showToast('Info', 'Filtres effacés', 'info');
}

/**
 * Affichage du formulaire d'ajout
 */
function showAddForm() {
    currentTrainingId = null;
    const modal = document.getElementById('trainingModal');
    const modalTitle = document.getElementById('modalTitle');
    
    if (modalTitle) {
        modalTitle.innerHTML = '<i class="bi bi-plus-circle"></i> Ajouter une Formation';
    }
    
    resetForm();
    
    if (modal) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

/**
 * Édition d'une formation
 * @param {number} id - ID de la formation à éditer
 */
async function editTraining(id) {
    try {
        showLoading(true);
        
        const response = await fetch(`${API_BASE_URL}/${id}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            currentTrainingId = id;
            populateForm(result.data);
            
            const modal = document.getElementById('trainingModal');
            const modalTitle = document.getElementById('modalTitle');
            
            if (modalTitle) {
                modalTitle.innerHTML = '<i class="bi bi-pencil"></i> Modifier la Formation';
            }
            
            if (modal) {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        } else {
            throw new Error(result.error || 'Erreur lors de la récupération de la formation');
        }
        
    } catch (error) {
        console.error('Erreur lors de l\'édition:', error);
        showToast('Erreur', 'Impossible de charger les données de la formation', 'error');
    } finally {
        showLoading(false);
    }
}

/**
 * Remplissage du formulaire avec les données d'une formation
 * @param {Object} training - Données de la formation
 */
function populateForm(training) {
    const domainSelect = document.getElementById('domain');
    const titleInput = document.getElementById('title');
    const dateInput = document.getElementById('date');
    const idInput = document.getElementById('trainingId');
    
    if (domainSelect) domainSelect.value = training.domain;
    if (titleInput) titleInput.value = training.title;
    if (dateInput) dateInput.value = training.date;
    if (idInput) idInput.value = training.id;
}

/**
 * Réinitialisation du formulaire
 */
function resetForm() {
    const form = document.getElementById('trainingForm');
    if (form) {
        form.reset();
        form.classList.remove('was-validated');
    }
    
    currentTrainingId = null;
    
    // Réinitialisation des champs
    const idInput = document.getElementById('trainingId');
    if (idInput) idInput.value = '';
}

/**
 * Sauvegarde d'une formation (création ou mise à jour)
 */
async function saveTraining() {
    try {
        // Validation du formulaire
        const form = document.getElementById('trainingForm');
        if (!form || !validateForm(form)) {
            return;
        }
        
        // Récupération des données du formulaire
        const formData = new FormData(form);
        const trainingData = {
            domain: formData.get('domain'),
            title: formData.get('title'),
            date: formData.get('date')
        };
        
        showLoading(true);
        
        const url = currentTrainingId ? `${API_BASE_URL}/${currentTrainingId}` : API_BASE_URL;
        const method = currentTrainingId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(trainingData)
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            // Fermeture de la modale
            const modal = document.getElementById('trainingModal');
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            }
            
            // Rechargement des formations
            await loadTrainings();
            
            const message = currentTrainingId ? 'Formation mise à jour avec succès' : 'Formation créée avec succès';
            showToast('Succès', message, 'success');
        } else {
            throw new Error(result.error || 'Erreur lors de la sauvegarde');
        }
        
    } catch (error) {
        console.error('Erreur lors de la sauvegarde:', error);
        showToast('Erreur', 'Impossible de sauvegarder la formation', 'error');
    } finally {
        showLoading(false);
    }
}

/**
 * Validation du formulaire
 * @param {HTMLFormElement} form - Formulaire à valider
 * @returns {boolean} True si le formulaire est valide
 */
function validateForm(form) {
    form.classList.add('was-validated');
    
    const isValid = form.checkValidity();
    
    if (!isValid) {
        showToast('Erreur', 'Veuillez corriger les erreurs dans le formulaire', 'error');
    }
    
    return isValid;
}

/**
 * Suppression d'une formation
 * @param {number} id - ID de la formation à supprimer
 */
function deleteTraining(id) {
    const training = trainings.find(t => t.id == id);
    if (!training) {
        showToast('Erreur', 'Formation non trouvée', 'error');
        return;
    }
    
    deleteTrainingId = id;
    
    // Affichage des informations de la formation à supprimer
    const deleteTrainingInfo = document.getElementById('deleteTrainingInfo');
    if (deleteTrainingInfo) {
        deleteTrainingInfo.textContent = `${training.title} (${training.domain}) - ${training.date_formatted || formatDate(training.date)}`;
    }
    
    // Affichage de la modale de confirmation
    const modal = document.getElementById('deleteModal');
    if (modal) {
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
    }
}

/**
 * Confirmation de suppression
 */
async function confirmDelete() {
    if (!deleteTrainingId) return;
    
    try {
        showLoading(true);
        
        const response = await fetch(`${API_BASE_URL}/${deleteTrainingId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            // Fermeture de la modale
            const modal = document.getElementById('deleteModal');
            if (modal) {
                const bsModal = bootstrap.Modal.getInstance(modal);
                if (bsModal) bsModal.hide();
            }
            
            // Rechargement des formations
            await loadTrainings();
            
            showToast('Succès', 'Formation supprimée avec succès', 'success');
        } else {
            throw new Error(result.error || 'Erreur lors de la suppression');
        }
        
    } catch (error) {
        console.error('Erreur lors de la suppression:', error);
        showToast('Erreur', 'Impossible de supprimer la formation', 'error');
    } finally {
        showLoading(false);
    }
}

/**
 * Affichage/masquage du spinner de chargement
 * @param {boolean} show - True pour afficher, false pour masquer
 */
function showLoading(show) {
    const spinner = document.getElementById('loadingSpinner');
    if (spinner) {
        spinner.classList.toggle('d-none', !show);
    }
}

/**
 * Affichage d'un toast de notification
 * @param {string} title - Titre du toast
 * @param {string} message - Message du toast
 * @param {string} type - Type de toast (success, error, info, warning)
 */
function showToast(title, message, type = 'info') {
    const toast = document.getElementById('toast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    if (!toast || !toastTitle || !toastMessage) return;
    
    // Configuration du titre et du message
    toastTitle.textContent = title;
    toastMessage.textContent = message;
    
    // Configuration de la couleur selon le type
    toast.className = 'toast';
    switch (type) {
        case 'success':
            toast.classList.add('bg-success', 'text-white');
            break;
        case 'error':
            toast.classList.add('bg-danger', 'text-white');
            break;
        case 'warning':
            toast.classList.add('bg-warning', 'text-dark');
            break;
        default:
            toast.classList.add('bg-info', 'text-white');
    }
    
    // Affichage du toast
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

/**
 * Formatage d'une date
 * @param {string} dateString - Date au format YYYY-MM-DD
 * @returns {string} Date formatée DD/MM/YYYY
 */
function formatDate(dateString) {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return dateString;
    
    return date.toLocaleDateString('fr-FR');
}

/**
 * Fonction de debounce pour optimiser les recherches
 * @param {Function} func - Fonction à exécuter
 * @param {number} wait - Délai d'attente en millisecondes
 * @returns {Function} Fonction avec debounce
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Gestion des erreurs globales
 */
window.addEventListener('error', function(e) {
    console.error('Erreur JavaScript:', e.error);
    showToast('Erreur', 'Une erreur JavaScript s\'est produite', 'error');
});

/**
 * Gestion des erreurs de promesses non gérées
 */
window.addEventListener('unhandledrejection', function(e) {
    console.error('Promesse rejetée:', e.reason);
    showToast('Erreur', 'Une erreur réseau s\'est produite', 'error');
}); 