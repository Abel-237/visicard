@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Événements par période</h2>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour aux rapports
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Ce rapport montre l'évolution du nombre d'événements créés sur la période sélectionnée.
                        Utilisez les filtres ci-dessous pour affiner votre analyse.
                    </p>

                    <!-- Formulaire de filtres -->
                    <form action="{{ route('admin.reports.events-by-period') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="period" class="form-label">Période</label>
                            <select class="form-select" id="period" name="period">
                                <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Journalier</option>
                                <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                                <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Mensuel</option>
                                <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Annuel</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.reports.events-by-period') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Graphique -->
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Évolution du nombre d'événements</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="eventsChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau de données -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Données détaillées</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Période</th>
                                                    <th class="text-center">Nombre d'événements</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($data as $item)
                                                    <tr>
                                                        <td>{{ $item['date'] }}</td>
                                                        <td class="text-center">{{ $item['count'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center">Aucune donnée disponible pour cette période</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer bg-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted">
                                            Total : <strong>{{ $data->sum('count') }} événements</strong>
                                        </span>
                                        <button class="btn btn-sm btn-outline-primary" id="exportBtn">
                                            <i class="fas fa-download"></i> Exporter en CSV
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique
        const labels = @json($chartLabels);
        const data = @json($chartData);
        
        // Création du graphique
        const ctx = document.getElementById('eventsChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre d\'événements',
                    data: data,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Exportation en CSV
        document.getElementById('exportBtn').addEventListener('click', function() {
            // Préparation des données
            let csvContent = "data:text/csv;charset=utf-8,Période,Nombre d'événements\n";
            
            @json($data).forEach(function(item) {
                csvContent += item.date + "," + item.count + "\n";
            });
            
            // Création du lien de téléchargement
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "evenements_par_periode_{{ now()->format('Y-m-d') }}.csv");
            document.body.appendChild(link);
            
            // Téléchargement
            link.click();
        });
    });
</script>
@endsection 