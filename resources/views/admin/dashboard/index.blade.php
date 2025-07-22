    <div class="container">
        <h1>Tableau de Bord Administrateur</h1>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Gestion des Utilisateurs</div>
                    <div class="card-body">
                        <p>Création, modification, suspension, attribution de rôles.</p>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Gérer les Utilisateurs</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Gestion des Cartes de Visite</div>
                    <div class="card-body">
                        <p>Modération, suppression, export.</p>
                        <a href="{{ route('admin.business-cards.index') }}" class="btn btn-primary">Gérer les Cartes de Visite</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Gestion des Événements</div>
                    <div class="card-body">
                        <p>Création, planning, affectation de participants.</p>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-primary">Gérer les Événements</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Statistiques</div>
                    <div class="card-body">
                        <p>Statistiques détaillées sur la participation, les échanges de cartes, les profils les plus actifs.</p>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Voir les Statistiques</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Modération et Sécurité</div>
                    <div class="card-body">
                        <p>Système de modération et de sécurité (signalement, historique d'activités, contrôle des accès).</p>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-primary">Gérer la Modération</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Paramétrage Global</div>
                    <div class="card-body">
                        <p>Paramétrage global (logo, thème, politiques, fonctionnalités activables).</p>
                        <a href="{{ route('admin.settings.index') }}" class="btn btn-primary">Paramètres</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Outils Avancés</div>
                    <div class="card-body">
                        <p>Outils avancés (génération de QR codes, intégration API, suivi en temps réel des échanges, options de personnalisation pour chaque événement).</p>
                        <a href="{{ route('admin.advanced-features.index') }}" class="btn btn-primary">Outils Avancés</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to fetch and display statistics
            function fetchStats() {
                fetch('/admin/dashboard/stats')
                    .then(response => response.json())
                    .then(stats => {
                        document.getElementById('totalCards').textContent = stats.totalCards;
                        document.getElementById('eventRegistrations').textContent = stats.eventRegistrations;
                        document.getElementById('userActivity').textContent = stats.userActivity;
                        document.getElementById('topUsers').textContent = stats.topUsers;
                    });
            }

            // Initial fetch
            fetchStats();
        });

        // Function to export data
        function exportData(format) {
            // Implement export logic
        }
    </script> 