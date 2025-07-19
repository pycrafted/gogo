/**
 * Scripts JavaScript pour l'interface d'administration
 */

// Variables globales
let participants = [];
let trainings = [];
let currentParticipantId = null;

/**
 * Met à jour la navigation active
 */
function updateNavigation(activeSection) {
    console.log(`🔄 Mise à jour de la navigation active: ${activeSection}`);
    
    // Retirer la classe active de tous les liens
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    navLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Ajouter la classe active au lien correspondant
    if (activeSection === 'participants') {
        const participantsLink = document.querySelector('.nav-link[onclick*="showParticipants"]');
        if (participantsLink) {
            participantsLink.classList.add('active');
        }
    } else if (activeSection === 'trainings') {
        const trainingsLink = document.querySelector('.nav-link[onclick*="showTrainings"]');
        if (trainingsLink) {
            trainingsLink.classList.add('active');
        }
    }
}
let currentParticipant = null; // Stocke le participant actuellement édité

// Initialisation de l'application - seulement après authentification
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Interface d\'administration initialisée');
    
    // Ajouter des écouteurs d'événements pour les onglets
    const participantsTab = document.getElementById('participants-tab');
    const trainingsTab = document.getElementById('trainings-tab');
    
    if (participantsTab) {
        participantsTab.addEventListener('shown.bs.tab', function() {
            console.log('👥 Onglet participants activé');
            loadParticipants();
        });
    }
    
    if (trainingsTab) {
        trainingsTab.addEventListener('shown.bs.tab', function() {
            console.log('📋 Onglet formations activé');
            loadAdminTrainings();
        });
    }
    
    // Les données seront chargées après vérification de l'authentification
    // dans le script principal de admin.html
});

/**
 * Charge les statistiques
 */
async function loadStats() {
    try {
        console.log('📈 Début du chargement des statistiques...');
        
        // Charger les participants
        console.log('👥 Chargement des participants...');
        const participantsResponse = await fetch('/api/participants.php');
        const participantsResult = await participantsResponse.json();
        
        console.log('📄 Réponse participants:', participantsResult);
        
        if (participantsResult.success) {
            participants = participantsResult.data;
            console.log(`✅ ${participants.length} participants chargés`);
            
            const totalParticipantsElement = document.getElementById('totalParticipants');
            if (totalParticipantsElement) {
                totalParticipantsElement.textContent = participants.length;
                console.log('✅ Statistique totalParticipants mise à jour');
            } else {
                console.error('❌ Élément totalParticipants non trouvé');
            }
            
            // Calculer les inscriptions récentes
            const today = new Date().toISOString().split('T')[0];
            const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            
            const todayRegistrations = participants.filter(p => p.registration_date.startsWith(today)).length;
            const weekRegistrations = participants.filter(p => p.registration_date >= weekAgo).length;
            
            console.log(`📅 Inscriptions aujourd'hui: ${todayRegistrations}`);
            console.log(`📊 Inscriptions cette semaine: ${weekRegistrations}`);
            
            const todayElement = document.getElementById('todayRegistrations');
            const weekElement = document.getElementById('weekRegistrations');
            
            if (todayElement) {
                todayElement.textContent = todayRegistrations;
                console.log('✅ Statistique todayRegistrations mise à jour');
            } else {
                console.error('❌ Élément todayRegistrations non trouvé');
            }
            
            if (weekElement) {
                weekElement.textContent = weekRegistrations;
                console.log('✅ Statistique weekRegistrations mise à jour');
            } else {
                console.error('❌ Élément weekRegistrations non trouvé');
            }
        } else {
            console.error('❌ Erreur lors du chargement des participants:', participantsResult.message);
        }
        
        // Charger les formations
        console.log('📋 Chargement des formations...');
        const trainingsResponse = await fetch('/api/trainings.php');
        const trainingsResult = await trainingsResponse.json();
        
        console.log('📄 Réponse formations:', trainingsResult);
        
        if (trainingsResult.success) {
            trainings = trainingsResult.data;
            console.log(`✅ ${trainings.length} formations chargées`);
            
            const totalTrainingsElement = document.getElementById('totalTrainings');
            if (totalTrainingsElement) {
                totalTrainingsElement.textContent = trainings.length;
                console.log('✅ Statistique totalTrainings mise à jour');
            } else {
                console.error('❌ Élément totalTrainings non trouvé');
            }
        } else {
            console.error('❌ Erreur lors du chargement des formations:', trainingsResult.message);
        }
        
        console.log('✅ Chargement des statistiques terminé');
        
    } catch (error) {
        console.error('🚨 Erreur lors du chargement des statistiques:', error);
        showNotification('Erreur lors du chargement des statistiques', 'error');
    }
}

/**
 * Affiche la section participants
 */
async function showParticipants() {
    console.log('👥 Affichage de la section participants...');
    
    // Utiliser Bootstrap 5 pour activer l'onglet
    const participantsTab = new bootstrap.Tab(document.getElementById('participants-tab'));
    participantsTab.show();
    
    // Mettre à jour la navigation
    updateNavigation('participants');
    
    // Charger les participants
    await loadParticipants();
}

/**
 * Affiche la section formations
 */
async function showTrainings() {
    console.log('📋 Affichage de la section formations...');
    
    // Utiliser Bootstrap 5 pour activer l'onglet
    const trainingsTab = new bootstrap.Tab(document.getElementById('trainings-tab'));
    trainingsTab.show();
    
    // Mettre à jour la navigation
    updateNavigation('trainings');
    
    // Charger les formations
    await loadAdminTrainings();
}

/**
 * Charge les participants
 */
async function loadParticipants() {
    try {
        console.log('👥 Chargement des participants...');
        const response = await fetch('/api/participants.php');
        const result = await response.json();
        
        console.log('📄 Réponse participants:', result);
        
        if (result.success) {
            participants = result.data;
            console.log(`✅ ${participants.length} participants chargés`);
            
            // Log des statuts pour debug
            participants.forEach((participant, index) => {
                console.log(`📊 Participant ${index + 1} (ID: ${participant.id}): statut = "${participant.status}"`);
            });
            
            displayParticipants(participants);
            updateParticipantCount(participants.length);
            
            // Charger le filtre de formation
            loadTrainingFilter();
            
            // Ajouter les écouteurs d'événements pour les filtres
            setupFilterEventListeners();
        } else {
            throw new Error(result.message || 'Erreur lors du chargement des participants');
        }
    } catch (error) {
        console.error('🚨 Erreur lors du chargement des participants:', error);
        showNotification('Erreur lors du chargement des participants', 'error');
    }
}

/**
 * Affiche les participants dans le tableau
 */
function displayParticipants(participantsToShow) {
    console.log(`📊 Affichage de ${participantsToShow.length} participants...`);
    
    const tbody = document.getElementById('participantsTableBody');
    if (!tbody) {
        console.error('❌ Élément participantsTableBody non trouvé');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (participantsToShow.length === 0) {
        console.log('📭 Aucun participant à afficher');
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted">
                    Aucun participant trouvé
                </td>
            </tr>
        `;
        return;
    }
    
    participantsToShow.forEach((participant, index) => {
        console.log(`📝 Affichage participant ${index + 1}:`, participant);
        console.log(`🏷️ Statut affiché pour participant ${participant.id}: "${participant.status}" -> "${getStatusText(participant.status)}" (couleur: ${getStatusColor(participant.status)})`);
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${participant.id}</td>
            <td><strong>${participant.training_title || 'N/A'}</strong></td>
            <td>${participant.first_name} ${participant.last_name}</td>
            <td>${participant.email}</td>
            <td>${participant.phone || 'N/A'}</td>
            <td>${participant.company || 'N/A'}</td>
            <td>${participant.position || 'N/A'}</td>
            <td>${formatDate(participant.registration_date)}</td>
            <td><span class="badge bg-${getStatusColor(participant.status)}">${getStatusText(participant.status)}</span></td>
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
    
    console.log('✅ Affichage des participants terminé');
}

/**
 * Charge les formations pour l'administration
 */
async function loadAdminTrainings() {
    try {
        console.log('📋 Chargement des formations pour l\'administration...');
        const response = await fetch('/api/trainings.php');
        const result = await response.json();
        
        console.log('📄 Réponse formations:', result);
        
        if (result.success) {
            trainings = result.data;
            console.log(`✅ ${trainings.length} formations chargées`);
            displayAdminTrainings(trainings);
            updateTrainingCount(trainings.length);
            
            // Configurer les écouteurs d'événements pour les filtres de formation
            setupTrainingFilterEventListeners();
        } else {
            throw new Error(result.message || 'Erreur lors du chargement des formations');
        }
    } catch (error) {
        console.error('🚨 Erreur lors du chargement des formations:', error);
        showNotification('Erreur lors du chargement des formations', 'error');
    }
}

/**
 * Affiche les formations dans le tableau d'administration
 */
function displayAdminTrainings(trainingsToShow) {
    console.log(`📊 Affichage de ${trainingsToShow.length} formations...`);
    
    const tbody = document.getElementById('trainingsTableBody');
    if (!tbody) {
        console.error('❌ Élément trainingsTableBody non trouvé');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (trainingsToShow.length === 0) {
        console.log('📭 Aucune formation à afficher');
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="text-center text-muted">
                    Aucune formation trouvée
                </td>
            </tr>
        `;
        return;
    }
    
    trainingsToShow.forEach((training, index) => {
        console.log(`📝 Affichage formation ${index + 1}:`, training);
        
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${training.id}</td>
            <td><span class="badge bg-primary">${training.domain}</span></td>
            <td><strong>${training.title}</strong></td>
            <td>${training.location}</td>
            <td>${formatDate(training.date)}</td>
            <td>${training.duration} jour(s)</td>
            <td>${formatPrice(training.price)}</td>
            <td>
                <div class="btn-group btn-group-sm" role="group">
                    <button class="btn btn-outline-info" onclick="viewTraining(${training.id})" title="Voir les détails">
                        👁️
                    </button>
                    <button class="btn btn-outline-warning" onclick="editTraining(${training.id})" title="Modifier">
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
    
    console.log('✅ Affichage des formations terminé');
}

/**
 * Affiche le formulaire d'ajout de formation
 */
function showAddTrainingForm() {
    console.log('➕ Affichage du formulaire d\'ajout de formation...');
    
    const modal = new bootstrap.Modal(document.getElementById('trainingModal'));
    const modalTitle = document.getElementById('modalTitle');
    const trainingId = document.getElementById('trainingId');
    
    if (modalTitle) modalTitle.textContent = 'Ajouter une formation';
    if (trainingId) trainingId.value = '';
    
    // Réinitialiser le formulaire
    const form = document.getElementById('trainingForm');
    if (form) form.reset();
    
    modal.show();
    console.log('✅ Modal d\'ajout de formation affiché');
}

/**
 * Sauvegarde une formation
 */
async function saveTraining() {
    console.log('💾 Sauvegarde de la formation...');
    
    try {
        const form = document.getElementById('trainingForm');
        const formData = new FormData(form);
        
        const trainingData = {
            domain: formData.get('domain'),
            title: formData.get('title'),
            location: formData.get('location'),
            date: formData.get('date'),
            duration: parseInt(formData.get('duration')),
            price: parseFloat(formData.get('price')),
            animators: formData.get('animators'),
            program: formData.get('program')
        };
        
        console.log('📄 Données de formation:', trainingData);
        
        const trainingId = document.getElementById('trainingId').value;
        const method = trainingId ? 'PUT' : 'POST';
        const url = trainingId ? `/api/trainings.php/${trainingId}` : '/api/trainings.php';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(trainingData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            console.log('✅ Formation sauvegardée avec succès');
            showNotification('Formation sauvegardée avec succès', 'success');
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('trainingModal'));
            modal.hide();
            
            // Recharger les formations
            await loadAdminTrainings();
        } else {
            throw new Error(result.message || 'Erreur lors de la sauvegarde');
        }
    } catch (error) {
        console.error('🚨 Erreur lors de la sauvegarde:', error);
        showNotification('Erreur lors de la sauvegarde: ' + error.message, 'error');
    }
}

/**
 * Affiche les détails d'un participant
 */
async function viewParticipant(id) {
    console.log(`👁️ Affichage des détails du participant ${id}...`);
    
    try {
        const response = await fetch(`/api/participants.php/${id}`);
        const result = await response.json();
        
        console.log('📄 Réponse API participant:', result);
        
        if (result.success) {
            const participant = result.data;
            currentParticipantId = id;
            currentParticipant = participant; // Stocker le participant actuel
            
            console.log('📝 Participant récupéré:', participant);
            
            const content = document.getElementById('participantDetailsContent');
            if (!content) {
                console.error('❌ Élément participantDetailsContent non trouvé');
                showNotification('Erreur: élément modal non trouvé', 'error');
                return;
            }
            
            content.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>👤 Informations personnelles</h6>
                        <p><strong>Nom complet:</strong> ${participant.first_name} ${participant.last_name}</p>
                        <p><strong>Email:</strong> ${participant.email}</p>
                        <p><strong>Téléphone:</strong> ${participant.phone || 'Non renseigné'}</p>
                        <p><strong>Entreprise:</strong> ${participant.company || 'Non renseigné'}</p>
                        <p><strong>Poste:</strong> ${participant.position || 'Non renseigné'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>📚 Informations de formation</h6>
                        <p><strong>Formation:</strong> ${participant.training_title || 'N/A'}</p>
                        <p><strong>Domaine:</strong> ${participant.training_domain || 'N/A'}</p>
                        <p><strong>Date de formation:</strong> ${participant.training_date_formatted || formatDate(participant.training_date) || 'N/A'}</p>
                        <p><strong>Statut:</strong> <span class="badge bg-${getStatusColor(participant.status)}">${getStatusText(participant.status)}</span></p>
                        <p><strong>Date d'inscription:</strong> ${participant.registration_date_formatted || formatDate(participant.registration_date) || 'N/A'}</p>
                    </div>
                </div>
                ${participant.notes ? `
                <div class="mt-3">
                    <h6>📝 Notes</h6>
                    <p class="bg-light p-3 rounded">${participant.notes}</p>
                </div>
                ` : ''}
            `;
            
            console.log('✅ Contenu modal généré, ouverture du modal...');
            const modal = new bootstrap.Modal(document.getElementById('participantDetailsModal'));
            modal.show();
            console.log('✅ Modal participantDetailsModal ouvert');
        } else {
            console.error('❌ Erreur API participant:', result.message);
            showNotification('Erreur lors du chargement du participant', 'error');
        }
    } catch (error) {
        console.error('🚨 Erreur lors du chargement du participant:', error);
        showNotification('Erreur lors du chargement du participant', 'error');
    }
}

/**
 * Édite un participant
 */
async function editParticipant(id) {
    console.log(`✏️ Édition du participant ${id}...`);
    
    try {
        const response = await fetch(`/api/participants.php/${id}`);
        const result = await response.json();
        
        console.log('📄 Réponse API participant pour édition:', result);
        
        if (result.success) {
            const participant = result.data;
            currentParticipantId = id;
            currentParticipant = participant; // Stocker le participant actuel
            
            console.log('📝 Participant à éditer:', participant);
            
            // Vérifier que tous les éléments du formulaire existent
            const formElements = [
                'editParticipantId', 'editFirstName', 'editLastName', 'editEmail',
                'editPhone', 'editCompany', 'editPosition', 'editStatus', 'editNotes'
            ];
            
            for (const elementId of formElements) {
                const element = document.getElementById(elementId);
                if (!element) {
                    console.error(`❌ Élément ${elementId} non trouvé`);
                    showNotification(`Erreur: élément ${elementId} non trouvé`, 'error');
                    return;
                }
            }
            
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
            
            console.log('✅ Formulaire rempli, ouverture du modal...');
            const modal = new bootstrap.Modal(document.getElementById('participantEditModal'));
            modal.show();
            console.log('✅ Modal participantEditModal ouvert');
        } else {
            console.error('❌ Erreur API participant pour édition:', result.message);
            showNotification('Erreur lors du chargement du participant', 'error');
        }
    } catch (error) {
        console.error('🚨 Erreur lors du chargement du participant:', error);
        showNotification('Erreur lors du chargement du participant', 'error');
    }
}

/**
 * Sauvegarde un participant
 */
async function saveParticipant() {
    console.log('💾 Sauvegarde du participant...');
    console.log('🔍 Participant actuel:', currentParticipant);
    console.log('🔍 ID participant actuel:', currentParticipantId);
    
    const form = document.getElementById('participantEditForm');
    if (!form) {
        console.error('❌ Formulaire participantEditForm non trouvé');
        showNotification('Erreur: formulaire non trouvé', 'error');
        return;
    }
    
    if (!form.checkValidity()) {
        console.log('❌ Formulaire invalide');
        form.reportValidity();
        return;
    }
    
    console.log('✅ Formulaire valide, récupération des données...');
    
    // Récupérer le training_id du participant actuel ou utiliser une valeur par défaut
    const trainingId = currentParticipant?.training_id || 1;
    console.log('📚 Training ID récupéré:', trainingId);
    
    const participantData = {
        training_id: trainingId,
        first_name: document.getElementById('editFirstName').value,
        last_name: document.getElementById('editLastName').value,
        email: document.getElementById('editEmail').value,
        phone: document.getElementById('editPhone').value,
        company: document.getElementById('editCompany').value,
        position: document.getElementById('editPosition').value,
        status: document.getElementById('editStatus').value,
        notes: document.getElementById('editNotes').value
    };
    
    console.log('📄 Données participant à sauvegarder:', participantData);
    console.log('📤 Envoi de la requête PUT vers /api/participants.php/' + currentParticipantId);
    
    try {
        const response = await fetch(`/api/participants.php/${currentParticipantId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(participantData)
        });
        
        console.log('📥 Réponse reçue, statut:', response.status);
        
        const result = await response.json();
        console.log('📄 Réponse API sauvegarde:', result);
        
        if (result.success) {
            console.log('✅ Participant mis à jour avec succès');
            showNotification('Participant mis à jour avec succès', 'success');
            
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('participantEditModal'));
            if (modal) {
                modal.hide();
                console.log('✅ Modal fermé');
            }
            
            // Forcer le rechargement des participants avec cache busting
            console.log('🔄 Rechargement forcé des participants...');
            await loadParticipants();
            
            // Mettre à jour directement le statut dans le tableau
            const newStatus = participantData.status;
            console.log(`🔄 Mise à jour directe du statut: ${newStatus}`);
            updateParticipantStatusInTable(currentParticipantId, newStatus);
            
            // Vérifier que les données sont bien mises à jour
            console.log('🔍 Vérification des données mises à jour...');
            const updatedResponse = await fetch('/api/participants.php');
            const updatedResult = await updatedResponse.json();
            
            if (updatedResult.success) {
                const updatedParticipant = updatedResult.data.find(p => p.id == currentParticipantId);
                if (updatedParticipant) {
                    console.log(`✅ Participant ${currentParticipantId} mis à jour: statut = "${updatedParticipant.status}"`);
                } else {
                    console.log(`❌ Participant ${currentParticipantId} non trouvé dans les données mises à jour`);
                }
            }
        } else {
            console.error('❌ Erreur API sauvegarde:', result.message);
            showNotification(result.message || 'Erreur lors de la mise à jour du participant', 'error');
        }
    } catch (error) {
        console.error('🚨 Erreur lors de la mise à jour du participant:', error);
        showNotification('Erreur lors de la mise à jour du participant', 'error');
    }
}

/**
 * Supprime un participant
 */
async function deleteParticipant(id) {
    console.log(`🗑️ Suppression du participant ${id}...`);
    
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce participant ?')) {
        console.log('❌ Suppression annulée par l\'utilisateur');
        return;
    }
    
    try {
        console.log('📤 Envoi de la requête DELETE...');
        const response = await fetch(`/api/participants.php/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        console.log('📄 Réponse API suppression:', result);
        
        if (result.success) {
            console.log('✅ Participant supprimé avec succès');
            showNotification('Participant supprimé avec succès', 'success');
            
            // Fermer les modals si ouverts
            const detailsModal = bootstrap.Modal.getInstance(document.getElementById('participantDetailsModal'));
            const editModal = bootstrap.Modal.getInstance(document.getElementById('participantEditModal'));
            
            if (detailsModal) {
                detailsModal.hide();
                console.log('✅ Modal détails fermé');
            }
            if (editModal) {
                editModal.hide();
                console.log('✅ Modal édition fermé');
            }
            
            // Recharger les participants
            await loadParticipants();
        } else {
            console.error('❌ Erreur API suppression:', result.message);
            showNotification(result.message || 'Erreur lors de la suppression du participant', 'error');
        }
    } catch (error) {
        console.error('🚨 Erreur lors de la suppression du participant:', error);
        showNotification('Erreur lors de la suppression du participant', 'error');
    }
}

/**
 * Affiche les détails d'une formation
 */
async function viewTraining(id) {
    console.log(`👁️ Affichage des détails de la formation ${id}...`);
    
    try {
        const response = await fetch(`/api/trainings.php/${id}`);
        const result = await response.json();
        
        console.log('📄 Réponse API formation:', result);
        
        if (result.success) {
            const training = result.data;
            console.log('✅ Formation récupérée:', training);
            
            // Récupérer les éléments du modal
            const modal = document.getElementById('trainingDetailsModal');
            const modalTitle = document.getElementById('trainingDetailsTitle');
            const modalBody = document.getElementById('trainingDetailsBody');
            
            if (!modal || !modalTitle || !modalBody) {
                console.error('❌ Éléments du modal de détails non trouvés');
                showNotification('Erreur: Modal de détails non trouvé', 'error');
                return;
            }
            
            // Remplir le contenu du modal
            modalTitle.textContent = training.title;
            modalBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informations générales</h6>
                        <p><strong>Domaine:</strong> <span class="badge bg-primary">${training.domain}</span></p>
                        <p><strong>Lieu:</strong> ${training.location}</p>
                        <p><strong>Date:</strong> ${formatDate(training.date)}</p>
                        <p><strong>Durée:</strong> ${training.duration} jour(s)</p>
                        <p><strong>Prix:</strong> ${formatPrice(training.price)}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Animateurs</h6>
                        <p>${training.animators || 'Non spécifié'}</p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Programme détaillé</h6>
                        <div class="program-content">
                            ${formatTrainingProgram(training.program)}
                        </div>
                    </div>
                </div>
            `;
            
            // Afficher le modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            console.log('✅ Modal de détails affiché');
            
        } else {
            console.error('❌ Erreur API récupération formation:', result.message);
            showNotification(result.message || 'Erreur lors de la récupération de la formation', 'error');
        }
    } catch (error) {
        console.error('🚨 Erreur lors de la récupération de la formation:', error);
        showNotification('Erreur lors de la récupération de la formation', 'error');
    }
}

/**
 * Affiche le formulaire de modification d'une formation
 */
async function editTraining(id) {
    console.log(`✏️ Modification de la formation ${id}...`);
    
    try {
        const response = await fetch(`/api/trainings.php/${id}`);
        const result = await response.json();
        
        console.log('📄 Réponse API formation:', result);
        
        if (result.success) {
            const training = result.data;
            console.log('✅ Formation récupérée pour modification:', training);
            
            // Récupérer les éléments du modal
            const modal = document.getElementById('trainingModal');
            const modalTitle = document.getElementById('modalTitle');
            const trainingId = document.getElementById('trainingId');
            const form = document.getElementById('trainingForm');
            
            if (!modal || !modalTitle || !trainingId || !form) {
                console.error('❌ Éléments du modal d\'édition non trouvés');
                showNotification('Erreur: Modal d\'édition non trouvé', 'error');
                return;
            }
            
            // Remplir le formulaire avec les données de la formation
            modalTitle.textContent = 'Modifier la formation';
            trainingId.value = training.id;
            
            // Remplir les champs du formulaire
            form.querySelector('[name="domain"]').value = training.domain;
            form.querySelector('[name="title"]').value = training.title;
            form.querySelector('[name="location"]').value = training.location;
            form.querySelector('[name="date"]').value = training.date;
            form.querySelector('[name="duration"]').value = training.duration;
            form.querySelector('[name="price"]').value = training.price;
            form.querySelector('[name="animators"]').value = training.animators || '';
            form.querySelector('[name="program"]').value = training.program || '';
            
            console.log('✅ Formulaire rempli avec les données de la formation');
            
            // Afficher le modal
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
            console.log('✅ Modal d\'édition affiché');
            
        } else {
            console.error('❌ Erreur API récupération formation:', result.message);
            showNotification(result.message || 'Erreur lors de la récupération de la formation', 'error');
        }
    } catch (error) {
        console.error('🚨 Erreur lors de la récupération de la formation:', error);
        showNotification('Erreur lors de la récupération de la formation', 'error');
    }
}

/**
 * Supprime une formation
 */
async function deleteTraining(id) {
    console.log(`🗑️ Suppression de la formation ${id}...`);
    
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette formation ?')) {
        console.log('❌ Suppression annulée par l\'utilisateur');
        return;
    }
    
    try {
        console.log('📤 Envoi de la requête DELETE...');
        const response = await fetch(`/api/trainings.php/${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        console.log('📄 Réponse API suppression:', result);
        
        if (result.success) {
            console.log('✅ Formation supprimée avec succès');
            showNotification('Formation supprimée avec succès', 'success');
            
            // Fermer les modals si ouverts
            const detailsModal = bootstrap.Modal.getInstance(document.getElementById('trainingDetailsModal'));
            const editModal = bootstrap.Modal.getInstance(document.getElementById('trainingModal'));
            
            if (detailsModal) {
                detailsModal.hide();
                console.log('✅ Modal détails fermé');
            }
            if (editModal) {
                editModal.hide();
                console.log('✅ Modal édition fermé');
            }
            
            // Recharger les formations
            await loadAdminTrainings();
        } else {
            console.error('❌ Erreur API suppression:', result.message);
            showNotification(result.message || 'Erreur lors de la suppression de la formation', 'error');
        }
    } catch (error) {
        console.error('🚨 Erreur lors de la suppression de la formation:', error);
        showNotification('Erreur lors de la suppression de la formation', 'error');
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
 * Met à jour le compteur de participants
 */
function updateParticipantCount(count) {
    console.log(`📊 Mise à jour du compteur de participants: ${count}`);
    const element = document.getElementById('participantCount');
    if (element) {
        element.textContent = count;
        console.log('✅ Compteur de participants mis à jour');
    } else {
        console.error('❌ Élément participantCount non trouvé');
    }
}

/**
 * Met à jour le compteur de formations
 */
function updateTrainingCount(count) {
    console.log(`📊 Mise à jour du compteur de formations: ${count}`);
    const element = document.getElementById('trainingCount');
    if (element) {
        element.textContent = count;
        console.log('✅ Compteur de formations mis à jour');
    } else {
        console.error('❌ Élément trainingCount non trouvé');
    }
}

/**
 * Met à jour le statut d'un participant dans le tableau sans recharger
 */
function updateParticipantStatusInTable(participantId, newStatus) {
    console.log(`🔄 Mise à jour du statut du participant ${participantId} dans le tableau...`);
    
    const tbody = document.getElementById('participantsTableBody');
    if (!tbody) {
        console.error('❌ Élément participantsTableBody non trouvé');
        return;
    }
    
    const rows = tbody.querySelectorAll('tr');
    let updated = false;
    
    rows.forEach((row, index) => {
        const cells = row.querySelectorAll('td');
        if (cells.length > 0) {
            const idCell = cells[0];
            if (idCell.textContent.trim() == participantId) {
                console.log(`✅ Ligne trouvée pour le participant ${participantId}`);
                
                // Mettre à jour le statut dans la 9ème colonne (index 8)
                if (cells.length > 8) {
                    const statusCell = cells[8];
                    statusCell.innerHTML = `<span class="badge bg-${getStatusColor(newStatus)}">${getStatusText(newStatus)}</span>`;
                    console.log(`✅ Statut mis à jour dans le tableau: "${newStatus}" -> "${getStatusText(newStatus)}"`);
                    updated = true;
                } else {
                    console.error('❌ Colonne de statut non trouvée dans la ligne');
                }
            }
        }
    });
    
    if (!updated) {
        console.error(`❌ Participant ${participantId} non trouvé dans le tableau`);
    }
}

/**
 * Retourne la couleur du badge selon le statut
 */
function getStatusColor(status) {
    switch (status) {
        case 'confirmed': return 'success';
        case 'pending': return 'warning';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

/**
 * Retourne le texte du statut
 */
function getStatusText(status) {
    switch (status) {
        case 'confirmed': return 'Confirmé';
        case 'pending': return 'En attente';
        case 'cancelled': return 'Annulé';
        default: return 'Inconnu';
    }
}

/**
 * Formate une date
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
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
 * Formate le programme d'une formation pour l'affichage
 */
function formatTrainingProgram(program) {
    if (!program) {
        return '<p class="text-muted">Aucun programme détaillé disponible</p>';
    }
    
    // Diviser le programme en lignes
    const lines = program.split('\n');
    let formattedProgram = '';
    
    lines.forEach((line, index) => {
        const trimmedLine = line.trim();
        if (trimmedLine) {
            // Détecter les jours (lignes commençant par "Jour" ou "Day")
            if (trimmedLine.match(/^(Jour|Day)\s*\d+/i)) {
                formattedProgram += `<h6 class="text-primary mt-3">${trimmedLine}</h6>`;
            }
            // Détecter les points (lignes commençant par "-" ou "*")
            else if (trimmedLine.match(/^[-*]\s/)) {
                formattedProgram += `<p class="mb-1"><i class="bi bi-check-circle-fill text-success me-2"></i>${trimmedLine.substring(1).trim()}</p>`;
            }
            // Autres lignes
            else {
                formattedProgram += `<p class="mb-2">${trimmedLine}</p>`;
            }
        }
    });
    
    return formattedProgram || '<p class="text-muted">Programme non formaté</p>';
}

/**
 * Affiche une notification
 */
function showNotification(message, type = 'info') {
    console.log(`📢 Notification [${type}]: ${message}`);
    
    const toast = document.getElementById('notificationToast');
    const toastTitle = document.getElementById('toastTitle');
    const toastMessage = document.getElementById('toastMessage');
    
    if (toast && toastTitle && toastMessage) {
        // Définir le titre selon le type
        let title = 'Information';
        let bgClass = 'bg-info';
        
        switch (type) {
            case 'success':
                title = 'Succès';
                bgClass = 'bg-success';
                break;
            case 'error':
                title = 'Erreur';
                bgClass = 'bg-danger';
                break;
            case 'warning':
                title = 'Attention';
                bgClass = 'bg-warning';
                break;
        }
        
        toastTitle.textContent = title;
        toastMessage.textContent = message;
        
        // Appliquer la classe de couleur
        toast.className = `toast ${bgClass} text-white`;
        
        // Afficher le toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        console.log('✅ Notification affichée');
    } else {
        console.error('❌ Éléments de notification non trouvés');
        // Fallback: alert
        alert(`${title}: ${message}`);
    }
}

/**
 * Filtre les participants selon les critères sélectionnés
 */
function filterParticipants() {
    console.log('🔍 Filtrage des participants...');
    
    const searchTerm = document.getElementById('filterSearch').value.toLowerCase();
    const statusFilter = document.getElementById('filterStatus').value;
    const trainingFilter = document.getElementById('filterTraining').value;
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;
    
    console.log('📊 Critères de filtrage:', {
        searchTerm,
        statusFilter,
        trainingFilter,
        dateFrom,
        dateTo
    });
    
    const filteredParticipants = participants.filter(participant => {
        // Filtre de recherche textuelle
        const searchMatch = !searchTerm || 
            participant.first_name.toLowerCase().includes(searchTerm) ||
            participant.last_name.toLowerCase().includes(searchTerm) ||
            participant.email.toLowerCase().includes(searchTerm) ||
            (participant.company && participant.company.toLowerCase().includes(searchTerm)) ||
            (participant.position && participant.position.toLowerCase().includes(searchTerm)) ||
            (participant.training_title && participant.training_title.toLowerCase().includes(searchTerm));
        
        // Filtre par statut
        const statusMatch = !statusFilter || participant.status === statusFilter;
        
        // Filtre par formation
        const trainingMatch = !trainingFilter || participant.training_title === trainingFilter;
        
        // Filtre par date
        let dateMatch = true;
        if (dateFrom || dateTo) {
            const registrationDate = new Date(participant.registration_date);
            const fromDate = dateFrom ? new Date(dateFrom) : null;
            const toDate = dateTo ? new Date(dateTo) : null;
            
            if (fromDate && toDate) {
                dateMatch = registrationDate >= fromDate && registrationDate <= toDate;
            } else if (fromDate) {
                dateMatch = registrationDate >= fromDate;
            } else if (toDate) {
                dateMatch = registrationDate <= toDate;
            }
        }
        
        return searchMatch && statusMatch && trainingMatch && dateMatch;
    });
    
    console.log(`✅ ${filteredParticipants.length} participants filtrés sur ${participants.length} total`);
    
    displayParticipants(filteredParticipants);
    updateParticipantCount(filteredParticipants.length);
}

/**
 * Efface tous les filtres
 */
function clearFilters() {
    console.log('🧹 Effacement des filtres...');
    
    document.getElementById('filterSearch').value = '';
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterTraining').value = '';
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';
    
    // Réafficher tous les participants
    displayParticipants(participants);
    updateParticipantCount(participants.length);
    
    console.log('✅ Filtres effacés');
}

/**
 * Charge les formations dans le filtre de formation
 */
function loadTrainingFilter() {
    console.log('📚 Chargement des formations pour le filtre...');
    
    const trainingSelect = document.getElementById('filterTraining');
    if (!trainingSelect) {
        console.error('❌ Élément filterTraining non trouvé');
        return;
    }
    
    // Vider les options existantes sauf la première
    trainingSelect.innerHTML = '<option value="">Toutes les formations</option>';
    
    // Récupérer les formations uniques depuis les participants
    const uniqueTrainings = [...new Set(participants
        .map(p => p.training_title)
        .filter(title => title && title.trim() !== ''))];
    
    console.log(`📋 ${uniqueTrainings.length} formations uniques trouvées`);
    
    // Ajouter les options
    uniqueTrainings.sort().forEach(training => {
        const option = document.createElement('option');
        option.value = training;
        option.textContent = training;
        trainingSelect.appendChild(option);
    });
    
    console.log('✅ Filtre de formation mis à jour');
}

/**
 * Configure les écouteurs d'événements pour les filtres
 */
function setupFilterEventListeners() {
    console.log('🎧 Configuration des écouteurs d\'événements pour les filtres...');
    
    // Filtre de recherche textuelle
    const searchInput = document.getElementById('filterSearch');
    if (searchInput) {
        searchInput.addEventListener('input', filterParticipants);
        console.log('✅ Écouteur d\'événement ajouté pour la recherche');
    }
    
    // Filtre par statut
    const statusSelect = document.getElementById('filterStatus');
    if (statusSelect) {
        statusSelect.addEventListener('change', filterParticipants);
        console.log('✅ Écouteur d\'événement ajouté pour le statut');
    }
    
    // Filtre par formation
    const trainingSelect = document.getElementById('filterTraining');
    if (trainingSelect) {
        trainingSelect.addEventListener('change', filterParticipants);
        console.log('✅ Écouteur d\'événement ajouté pour la formation');
    }
    
    // Filtres de date
    const dateFromInput = document.getElementById('filterDateFrom');
    const dateToInput = document.getElementById('filterDateTo');
    
    if (dateFromInput) {
        dateFromInput.addEventListener('change', filterParticipants);
        console.log('✅ Écouteur d\'événement ajouté pour la date de début');
    }
    
    if (dateToInput) {
        dateToInput.addEventListener('change', filterParticipants);
        console.log('✅ Écouteur d\'événement ajouté pour la date de fin');
    }
    
    console.log('✅ Tous les écouteurs d\'événements configurés');
}

/**
 * Filtre les formations selon les critères sélectionnés
 */
function filterTrainings() {
    console.log('🔍 Filtrage des formations...');
    
    const searchTerm = document.getElementById('filterTrainingSearch').value.toLowerCase();
    const domainFilter = document.getElementById('filterTrainingDomain').value;
    const dateFrom = document.getElementById('filterTrainingDateFrom').value;
    const dateTo = document.getElementById('filterTrainingDateTo').value;
    const maxPrice = document.getElementById('filterTrainingPrice').value;
    
    console.log('📊 Critères de filtrage des formations:', {
        searchTerm,
        domainFilter,
        dateFrom,
        dateTo,
        maxPrice
    });
    
    const filteredTrainings = trainings.filter(training => {
        // Filtre de recherche textuelle
        const searchMatch = !searchTerm || 
            training.title.toLowerCase().includes(searchTerm) ||
            training.location.toLowerCase().includes(searchTerm) ||
            (training.animators && training.animators.toLowerCase().includes(searchTerm)) ||
            (training.program && training.program.toLowerCase().includes(searchTerm));
        
        // Filtre par domaine
        const domainMatch = !domainFilter || training.domain === domainFilter;
        
        // Filtre par date
        let dateMatch = true;
        if (dateFrom || dateTo) {
            const trainingDate = new Date(training.date);
            const fromDate = dateFrom ? new Date(dateFrom) : null;
            const toDate = dateTo ? new Date(dateTo) : null;
            
            if (fromDate && toDate) {
                dateMatch = trainingDate >= fromDate && trainingDate <= toDate;
            } else if (fromDate) {
                dateMatch = trainingDate >= fromDate;
            } else if (toDate) {
                dateMatch = trainingDate <= toDate;
            }
        }
        
        // Filtre par prix maximum
        let priceMatch = true;
        if (maxPrice && maxPrice.trim() !== '') {
            const price = parseFloat(training.price);
            const maxPriceValue = parseFloat(maxPrice);
            priceMatch = !isNaN(price) && !isNaN(maxPriceValue) && price <= maxPriceValue;
        }
        
        return searchMatch && domainMatch && dateMatch && priceMatch;
    });
    
    console.log(`✅ ${filteredTrainings.length} formations filtrées sur ${trainings.length} total`);
    
    displayAdminTrainings(filteredTrainings);
    updateTrainingCount(filteredTrainings.length);
}

/**
 * Efface tous les filtres de formation
 */
function clearTrainingFilters() {
    console.log('🧹 Effacement des filtres de formation...');
    
    document.getElementById('filterTrainingSearch').value = '';
    document.getElementById('filterTrainingDomain').value = '';
    document.getElementById('filterTrainingDateFrom').value = '';
    document.getElementById('filterTrainingDateTo').value = '';
    document.getElementById('filterTrainingPrice').value = '';
    
    // Réafficher toutes les formations
    displayAdminTrainings(trainings);
    updateTrainingCount(trainings.length);
    
    console.log('✅ Filtres de formation effacés');
}

/**
 * Configure les écouteurs d'événements pour les filtres de formation
 */
function setupTrainingFilterEventListeners() {
    console.log('🎧 Configuration des écouteurs d\'événements pour les filtres de formation...');
    
    // Filtre de recherche textuelle
    const searchInput = document.getElementById('filterTrainingSearch');
    if (searchInput) {
        searchInput.addEventListener('input', filterTrainings);
        console.log('✅ Écouteur d\'événement ajouté pour la recherche de formation');
    }
    
    // Filtre par domaine
    const domainSelect = document.getElementById('filterTrainingDomain');
    if (domainSelect) {
        domainSelect.addEventListener('change', filterTrainings);
        console.log('✅ Écouteur d\'événement ajouté pour le domaine');
    }
    
    // Filtres de date
    const dateFromInput = document.getElementById('filterTrainingDateFrom');
    const dateToInput = document.getElementById('filterTrainingDateTo');
    
    if (dateFromInput) {
        dateFromInput.addEventListener('change', filterTrainings);
        console.log('✅ Écouteur d\'événement ajouté pour la date de début de formation');
    }
    
    if (dateToInput) {
        dateToInput.addEventListener('change', filterTrainings);
        console.log('✅ Écouteur d\'événement ajouté pour la date de fin de formation');
    }
    
    // Filtre par prix
    const priceInput = document.getElementById('filterTrainingPrice');
    if (priceInput) {
        priceInput.addEventListener('input', filterTrainings);
        console.log('✅ Écouteur d\'événement ajouté pour le prix maximum');
    }
    
    console.log('✅ Tous les écouteurs d\'événements de formation configurés');
} 