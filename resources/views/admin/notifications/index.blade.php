    <div class="container">
        <h1>Notifications et Communication</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Envoi de Notifications Ciblées</div>
                    <div class="card-body">
                        <form id="notificationForm">
                            <div class="mb-3">
                                <label for="userGroup" class="form-label">Groupe d'Utilisateurs</label>
                                <select class="form-select" id="userGroup" name="userGroup">
                                    <option value="all">Tous les Utilisateurs</option>
                                    <option value="active">Utilisateurs Actifs</option>
                                    <option value="inactive">Utilisateurs Inactifs</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="channel" class="form-label">Canal de Communication</label>
                                <select class="form-select" id="channel" name="channel">
                                    <option value="email">Email</option>
                                    <option value="sms">SMS</option>
                                    <option value="inApp">Notification In-App</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Envoyer</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Création d'Annonces Importantes</div>
                    <div class="card-body">
                        <form id="announcementForm">
                            <div class="mb-3">
                                <label for="announcementTitle" class="form-label">Titre de l'Annonce</label>
                                <input type="text" class="form-control" id="announcementTitle" name="announcementTitle">
                            </div>
                            <div class="mb-3">
                                <label for="announcementContent" class="form-label">Contenu de l'Annonce</label>
                                <textarea class="form-control" id="announcementContent" name="announcementContent" rows="5"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Publier</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Personnalisation des Messages</div>
                    <div class="card-body">
                        <form id="messageForm">
                            <div class="mb-3">
                                <label for="welcomeMessage" class="form-label">Message de Bienvenue</label>
                                <textarea class="form-control" id="welcomeMessage" name="welcomeMessage" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="reminderMessage" class="form-label">Message de Rappel</label>
                                <textarea class="form-control" id="reminderMessage" name="reminderMessage" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to send targeted notifications
            document.getElementById('notificationForm').addEventListener('submit', function(event) {
                event.preventDefault();
                // Implement send notification logic
            });

            // Function to create important announcements
            document.getElementById('announcementForm').addEventListener('submit', function(event) {
                event.preventDefault();
                // Implement create announcement logic
            });

            // Function to customize messages
            document.getElementById('messageForm').addEventListener('submit', function(event) {
                event.preventDefault();
                // Implement customize message logic
            });
        });
    </script> 