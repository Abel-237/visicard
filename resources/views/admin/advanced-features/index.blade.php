    <div class="container">
        <h1>Fonctionnalités Avancées</h1>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Génération de QR Codes</div>
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="generateQRCode()">Générer QR Code</button>
                        <div id="qrCode" class="mt-3"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Suivi des Échanges</div>
                    <div class="card-body">
                        <p>Échanges en temps réel: <span id="realTimeExchanges">0</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Intégration API</div>
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="integrateAPI()">Intégrer API</button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header">Matchmaking Intelligent</div>
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="suggestContacts()">Suggérer des Contacts</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to generate QR code
            function generateQRCode() {
                const qrData = {
                    name: 'John Doe',
                    title: 'CEO',
                    company: 'Example Corp',
                    phone: '1234567890',
                    email: 'john@example.com',
                    website: 'www.example.com',
                    address: '123 Main St'
                };

                const qrString = JSON.stringify(qrData);
                const qrCode = new QRCode(document.getElementById('qrCode'), {
                    text: qrString,
                    width: 100,
                    height: 100
                });
            }

            // Function to fetch real-time exchanges
            function fetchRealTimeExchanges() {
                fetch('/admin/real-time-exchanges')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('realTimeExchanges').textContent = data.count;
                    });
            }

            // Initial fetch
            fetchRealTimeExchanges();
        });

        // Function to integrate API
        function integrateAPI() {
            // Implement API integration logic
        }

        // Function to suggest contacts
        function suggestContacts() {
            // Implement contact suggestion logic
        }
    </script> 