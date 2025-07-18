/**
 * Scripts JavaScript pour l'interface d'administration
 */

// Variables globales
let participants = [];
let trainings = [];
let currentParticipantId = null;

// Initialisation de l'application
document.addEventListener('DOMContentLoaded', function() {
    console.log('Interface d\'administration initialisée');
    loadStats();
    showParticipants(); // Afficher les participants par défaut
});

/**
 * Charge les statistiques
 */
async function loadStats() {
    try {
        // Charger les participants
        const participantsResponse = await fetch('/api/participants.php');
        const participantsResult = await participantsResponse.json();
        
        if (participantsResult.success) {
            participants = participantsResult.data;
            document.getElementById('totalParticipants').textContent = participants.length;
            
            // Calculer les inscriptions récentes
            const today = new Date().toISOString().split('T')[0];
            const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            
            const todayRegistrations = participants.filter(p => p.registration_date.startsWith(today)).length;
            const weekRegistrations = participants.filter(p => p.registration_date >= weekAgo).length;
            
            document.getElementById('todayRegistrations').textContent = todayRegistrations;
            document.getElementById('weekRegistrations').textContent = weekRegistrations;
        }
        
        // Charger les formations
        const trainingsResponse = await fetch('/api/trainings.php');
        const trainingsResult = await trainingsResponse.json();
        
        if (trainingsResult.success) {
            trainings = trainingsResult.data;
            document.getElementById('totalTrainings').textContent = trainings.length;
            
            // Calculer le top des formations
            const trainingStats = {};
            participants.forEach(p => {
                const trainingId = p.training_id;
                if (!trainingStats[trainingId]) {
                    trainingStats[trainingId] = 0;
                }
                trainingStats[trainingId]++;
            });
            
            const topTrainings = Object.entries(trainingStats)
                .sort(([,a], [,b]) => b - a)
                .slice(0, 3)
                .map(([trainingId, count]) => {
                    const training = trainings.find(t => t.id == trainingId);
                    return training ? `${training.title}: ${count}` : `Formation ${trainingId}: ${count}`;
                });
            
            document.getElementById('topTrainings').innerHTML = topTrainings.map(t => `<p class="mb-1">${t}</p>`).join('');
        }
        
    } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        showNotification('Erreur lors du chargement des statistiques', 'error');
    }
}

/**
 * Affiche la section participants
 */
async function showParticipants() {
    document.getElementById('participantsSection').style.display = 'block';
    document.getElementById('trainingsSection').style.display = 'none';
    
    // Charger les participants
    await loadParticipants();
    
    // Charger les formations pour le filtre
    await loadTrainingsForFilter();
}

/**
 * Affiche la section formations
 */
async function showTrainings() {
    document.getElementById('participantsSection').style.display = 'none';
    document.getElementById('trainingsSection').style.display = 'block';
    
    // Charger les formations
    await loadAdminTrainings();
}

/**
 * Charge les participants
 */
async function loadParticipants() {
    try {
        const response = await fetch('/api/participants.php');
        const result = await response.json();
        
        if (result.success) {
            participants = result.data;
            displayParticipants(participants);
            updateParticipantCount(participants.length);
        } else {
            throw new Error(result.message || 'Erreur lors du chargement des participants');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des participants:', error);
        showNotification('Erreur lors du chargement des participants', 'error');
    }
}

/**
 * Affiche les participants dans le tableau
 */
function displayParticipants(participantsToShow) {
    const tbody = document.getElementById('participantsTableBody');
    tbody.innerHTML = '';
    
    if (participantsToShow.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted">
                    Aucun participant trouvé
                </td>
            </tr>
        `;
        return;
    }
    
    participantsToShow.forEach(participant => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${participant.id}</td>
            <td><strong>${participant.training_title || 'N/A'}</strong></td>
            <td>${participant.first_name} ${participant.last_name}</td>
            <td>${participant.email}</td>
            <td>${participant.phone || 'N/A'}</td>
            <td>${participant.company || 'N/A'}</td>
            <td>${participant.position || 'N/A'}</td>
            <td><span class="badge bg-${getStatusColor(participant.status)}">${getStatusText(participant.status)}</span></td>
            <td>${participant.registration_date_formatted || formatDate(participant.registration_date)}</td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-info" onclick="viewParticipant(${participant.id})" title="Voir les détails">
                        👁️
                    </button>
                    <button class="btn btn-outline-warning" onclick="editParticipant(${participant.id})" title="Modifier">
                        ✏️
                    </button>
                    <button class="btn btn-outline-danger" onclick="deleteParticipant(${participant.id})" title="Supprimer">
                        🗑️
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

/**
 * Filtre les participants par formation
 */
function filterParticipants() {
    const trainingFilter = document.getElementById('trainingFilter').value;
    
    let filteredParticipants = participants;
    
    if (trainingFilter) {
        filteredParticipants = participants.filter(p => p.training_id == trainingFilter);
    }
    
    displayParticipants(filteredParticipants);
    updateParticipantCount(filteredParticipants.length);
}

/**
 * Charge les formations pour le filtre
 */
async function loadTrainingsForFilter() {
    try {
        const response = await fetch('/api/trainings.php');
        const result = await response.json();
        
        if (result.success) {
            const select = document.getElementById('trainingFilter');
            select.innerHTML = '<option value="">Toutes les formations</option>';
            
            result.data.forEach(training => {
                const option = document.createElement('option');
                option.value = training.id;
                option.textContent = training.title;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des formations pour le filtre:', error);
    }
}

/**
 * Charge les formations pour l'administration
 */
async function loadAdminTrainings() {
    try {
        const response = await fetch('/api/trainings.php');
        const result = await response.json();
        
        if (result.success) {
            trainings = result.data;
            displayAdminTrainings(trainings);
            updateTrainingCount(trainings.length);
        } else {
            throw new Error(result.message || 'Erreur lors du chargement des formations');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des formations:', error);
        showNotification('Erreur lors du chargement des formations', 'error');
    }
}

/**
 * Affiche les formations dans le tableau d'administration
 */
function displayAdminTrainings(trainingsToShow) {
    const tbody = document.getElementById('adminTrainingsTableBody');
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
    
    trainingsToShow.forEach(training => {
        // Compter les participants pour cette formation
        const participantCount = participants.filter(p => p.training_id == training.id).length;
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${training.id}</td>
            <td><span class="badge bg-primary">${training.domain}</span></td>
            <td><strong>${training.title}</strong></td>
            <td>${training.location || 'N/A'}</td>
            <td>${training.date_formatted || formatDate(training.date)}</td>
            <td>${training.duration_formatted || (training.duration ? training.duration + ' jour(s)' : 'N/A')}</td>
            <td>${training.price_formatted || (training.price ? formatPrice(training.price) : 'N/A')}</td>
            <td><span class="badge bg-info">${participantCount}</span></td>
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
}

/**
 * Affiche les détails d'un participant
 */
async function viewParticipant(id) {
    try {
        const response = await fetch(`/api/participants.php/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const participant = result.data;
            currentParticipantId = id;
            
            const details = document.getElementById('participantDetails');
            details.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations personnelles</h6>
                        <p><strong>Nom:</strong> ${participant.first_name} ${participant.last_name}</p>
                        <p><strong>Email:</strong> ${participant.email}</p>
                        <p><strong>Téléphone:</strong> ${participant.phone || 'Non renseigné'}</p>
                        <p><strong>Entreprise:</strong> ${participant.company || 'Non renseigné'}</p>
                        <p><strong>Poste:</strong> ${participant.position || 'Non renseigné'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Informations de formation</h6>
                        <p><strong>Formation:</strong> ${participant.training_title}</p>
                        <p><strong>Domaine:</strong> ${participant.training_domain}</p>
                        <p><strong>Date de formation:</strong> ${participant.training_date_formatted || formatDate(participant.training_date)}</p>
                        <p><strong>Statut:</strong> <span class="badge bg-${getStatusColor(participant.status)}">${getStatusText(participant.status)}</span></p>
                        <p><strong>Date d'inscription:</strong> ${participant.registration_date_formatted || formatDate(participant.registration_date)}</p>
                    </div>
                </div>
                ${participant.notes ? `
                <div class="mt-3">
                    <h6>Notes</h6>
                    <p class="bg-light p-3 rounded">${participant.notes}</p>
                </div>
                ` : ''}
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('participantModal'));
            modal.show();
        } else {
            showNotification('Erreur lors du chargement du participant', 'error');
        }
    } catch (error) {
        console.error('Erreur lors du chargement du participant:', error);
        showNotification('Erreur lors du chargement du participant', 'error');
    }
}

/**
 * Édite un participant
 */
async function editParticipant(id) {
    try {
        const response = await fetch(`/api/participants.php/${id}`);
        const result = await response.json();
        
        if (result.success) {
            const participant = result.data;
            currentParticipantId = id;
            
            // Remplir le formulaire
            document.getElementById('editParticipantId').value = participant.id;
            document.getElementById('editFirstName').value = participant.first_name;
            document.getElementById('editLastName').value = participant.last_name;
            document.getElementById('editEmail').value = participant.email;
            document.getElementById('editPhone').value = participant.phone || '';
            document.getElementById('editCompany').value = participant.company || '';
            document.getElementById('editPosition').value = participant.position || '';
            document.getElementById('editStatus').value = participant.status || 'pending';
            document.getElementById('editNotes').value = participant.notes || '';
            
            // Charger les formations pour le select
            const trainingSelect = document.getElementById('editTrainingId');
            trainingSelect.innerHTML = '';
            trainings.forEach(training => {
                const option = document.createElement('option');
                option.value = training.id;
                option.textContent = training.title;
                if (training.id == participant.training_id) {
                    option.selected = true;
                }
                trainingSelect.appendChild(option);
            });
            
            const modal = new bootstrap.Modal(document.getElementById('editParticipantModal'));
            modal.show();
        } else {
            showNotification('Erreur lors du chargement du participant', 'error');
        }
    } catch (error) {
        console.error('Erreur lors du chargement du participant:', error);
        showNotification('Erreur lors du chargement du participant', 'error');
    }
}

/**
 * Sauvegarde un participant
 */
async function saveParticipant() {
    const form = document.getElementById('editParticipantForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const participantData = {
        training_id: parseInt(document.getElementById('editTrainingId').value),
        first_name: document.getElementById('editFirstName').value,
        last_name: document.getElementById('editLastName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        company: document.getElementById('editCompany').value,
        position: document.getElementById('editPosition').value,
        status: document.getElementById('editStatus').value,
        notes: document.getElementById('editNotes').value
    };
    
    try {
        const response = await fetch(`/api/participants.php/${currentParticipantId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(participantData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Participant mis à jour avec succès', 'success');
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('editParticipantModal'));
            modal.hide();
            
            // Recharger les participants
            await loadParticipants();
        } else {
            showNotification(result.message || 'Erreur lors de la mise à jour du participant', 'error');
        }
    } catch (error) {
        console.error('Erreur lors de la mise à jour du participant:', error);
        showNotification('Erreur lors de la mise à jour du participant', 'error');
    }
}

/**
 * Supprime un participant
 */
async function deleteParticipant(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce participant ?')) {
        return;
    }
    
    try {
        const response = await fetch(`/api/participants.php/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showNotification('Participant supprimé avec succès', 'success');
            
            // Fermer le modal si ouvert
            const modal = bootstrap.Modal.getInstance(document.getElementById('participantModal'));
            if (modal) {
                modal.hide();
            }
            
            // Recharger les participants
            await loadParticipants();
        } else {
            showNotification(result.message || 'Erreur lors de la suppression du participant', 'error');
        }
    } catch (error) {
        console.error('Erreur lors de la suppression du participant:', error);
        showNotification('Erreur lors de la suppression du participant', 'error');
    }
}

/**
 * Affiche le formulaire d'ajout de formation
 */
function showAddForm() {
    // Rediriger vers la page principale pour ajouter une formation
    window.location.href = 'index.html';
}

/**
 * Fonctions utilitaires
 */
function updateParticipantCount(count) {
    document.getElementById('participantCount').textContent = count;
}

function updateTrainingCount(count) {
    document.getElementById('trainingCount').textContent = count;
}

function getStatusColor(status) {
    switch (status) {
        case 'confirmed': return 'success';
        case 'cancelled': return 'danger';
        default: return 'warning';
    }
}

function getStatusText(status) {
    switch (status) {
        case 'confirmed': return 'Confirmé';
        case 'cancelled': return 'Annulé';
        default: return 'En attente';
    }
}

function formatDate(dateString) {
    if (!dateString) return 'N/A';
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR');
}

function formatPrice(price) {
    if (!price) return 'N/A';
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

function showNotification(message, type = 'info') {
    const toast = document.getElementById('notificationToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    toastTitle.textContent = type === 'error' ? '❌ Erreur' : type === 'success' ? '✅ Succès' : 'ℹ️ Information';
    toastMessage.textContent = message;
    
    toast.classList.remove('bg-danger', 'bg-success', 'bg-info');
    toast.classList.add(type === 'error' ? 'bg-danger' : type === 'success' ? 'bg-success' : 'bg-info');
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
} 