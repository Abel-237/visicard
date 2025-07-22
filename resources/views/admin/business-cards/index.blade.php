    <div class="container">
        <h1>Gestion des Cartes de Visite</h1>
        <div class="mb-3">
            <input type="text" id="searchCard" class="form-control" placeholder="Rechercher une carte de visite...">
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Entreprise</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="cardTableBody">
                <!-- Card data will be populated here -->
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchCard');
            const cardTableBody = document.getElementById('cardTableBody');

            // Function to fetch and display cards
            function fetchCards() {
                const searchTerm = searchInput.value;

                fetch(`/admin/business-cards?search=${searchTerm}`)
                    .then(response => response.json())
                    .then(cards => {
                        cardTableBody.innerHTML = '';
                        cards.forEach(card => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${card.name}</td>
                                <td>${card.company}</td>
                                <td>${card.email}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editCard(${card.id})">Modifier</button>
                                    <button class="btn btn-sm btn-warning" onclick="hideCard(${card.id})">Masquer</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteCard(${card.id})">Supprimer</button>
                                    <button class="btn btn-sm btn-info" onclick="exportCard(${card.id}, 'pdf')">PDF</button>
                                    <button class="btn btn-sm btn-info" onclick="exportCard(${card.id}, 'vcard')">vCard</button>
                                    <button class="btn btn-sm btn-info" onclick="exportCard(${card.id}, 'excel')">Excel</button>
                                </td>
                            `;
                            cardTableBody.appendChild(row);
                        });
                    });
            }

            // Event listener for search
            searchInput.addEventListener('input', fetchCards);

            // Initial fetch
            fetchCards();
        });

        // Functions to handle card actions
        function editCard(cardId) {
            // Implement edit card logic
        }

        function hideCard(cardId) {
            // Implement hide card logic
        }

        function deleteCard(cardId) {
            // Implement delete card logic
        }

        function exportCard(cardId, format) {
            // Implement export card logic
        }
    </script> 