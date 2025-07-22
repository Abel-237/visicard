    <div class="container">
        <h1>Système de Signalement et Modération</h1>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Gestion des Signalements</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="reportTableBody">
                                <!-- Report data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">Journal des Actions d'Administration</div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Modérateur</th>
                                    <th>Motif</th>
                                </tr>
                            </thead>
                            <tbody id="logTableBody">
                                <!-- Log data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to fetch and display reports
            function fetchReports() {
                fetch('/admin/reports')
                    .then(response => response.json())
                    .then(reports => {
                        const reportTableBody = document.getElementById('reportTableBody');
                        reportTableBody.innerHTML = '';
                        reports.forEach(report => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${report.id}</td>
                                <td>${report.type}</td>
                                <td>${report.description}</td>
                                <td>${report.status}</td>
                                <td>
                                    <button class="btn btn-sm btn-success" onclick="approveReport(${report.id})">Approuver</button>
                                    <button class="btn btn-sm btn-warning" onclick="ignoreReport(${report.id})">Ignorer</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteReport(${report.id})">Supprimer</button>
                                </td>
                            `;
                            reportTableBody.appendChild(row);
                        });
                    });
            }

            // Function to fetch and display logs
            function fetchLogs() {
                fetch('/admin/logs')
                    .then(response => response.json())
                    .then(logs => {
                        const logTableBody = document.getElementById('logTableBody');
                        logTableBody.innerHTML = '';
                        logs.forEach(log => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${log.date}</td>
                                <td>${log.action}</td>
                                <td>${log.moderator}</td>
                                <td>${log.reason}</td>
                            `;
                            logTableBody.appendChild(row);
                        });
                    });
            }

            // Initial fetch
            fetchReports();
            fetchLogs();
        });

        // Functions to handle report actions
        function approveReport(reportId) {
            // Implement approve report logic
        }

        function ignoreReport(reportId) {
            // Implement ignore report logic
        }

        function deleteReport(reportId) {
            // Implement delete report logic
        }
    </script> 