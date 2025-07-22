@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Analyse des tendances</h2>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour aux rapports
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Ce rapport analyse les tendances et préférences des utilisateurs : catégories populaires, tags les plus utilisés,
                        utilisateurs les plus actifs et heures de publication les plus fréquentes.
                    </p>

                    <!-- Formulaire de filtres -->
                    <form action="{{ route('admin.reports.trends') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-5">
                            <label for="start_date" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-5">
                            <label for="end_date" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Graphiques -->
                    <div class="row mb-4">
                        <!-- Catégories populaires -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Catégories populaires</h4>
                                </div>
                                <div class="card-body">
                                    @if($popularCategories->count() > 0)
                                        <canvas id="categoriesChart" height="250"></canvas>
                                    @else
                                        <div class="alert alert-info">
                                            Aucune donnée disponible pour cette période
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tags populaires -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Tags les plus utilisés</h4>
                                </div>
                                <div class="card-body">
                                    @if($popularTags->count() > 0)
                                        <canvas id="tagsChart" height="250"></canvas>
                                    @else
                                        <div class="alert alert-info">
                                            Aucune donnée disponible pour cette période
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Utilisateurs actifs -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Utilisateurs les plus actifs</h4>
                                </div>
                                <div class="card-body">
                                    @if($activeUsers->count() > 0)
                                        <canvas id="usersChart" height="250"></canvas>
                                    @else
                                        <div class="alert alert-info">
                                            Aucune donnée disponible pour cette période
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Heures de publication -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Heures de publication</h4>
                                </div>
                                <div class="card-body">
                                    @if($popularHours->count() > 0)
                                        <canvas id="hoursChart" height="250"></canvas>
                                    @else
                                        <div class="alert alert-info">
                                            Aucune donnée disponible pour cette période
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableaux de données -->
                    <div class="row">
                        <!-- Catégories -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Détail des catégories</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Catégorie</th>
                                                    <th class="text-center">Événements</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($popularCategories as $category)
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-{{ $category->color ?? 'primary' }}">
                                                                {{ $category->name }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">{{ $category->events_count }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center">Aucune donnée disponible</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tags -->
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Détail des tags</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Tag</th>
                                                    <th class="text-center">Utilisation</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($popularTags as $tag)
                                                    <tr>
                                                        <td>{{ $tag->name }}</td>
                                                        <td class="text-center">{{ $tag->count }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-center">Aucune donnée disponible</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
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
        // Palettes de couleurs
        const colorPalette = [
            'rgba(54, 162, 235, 0.7)', 'rgba(255, 99, 132, 0.7)', 'rgba(75, 192, 192, 0.7)', 
            'rgba(255, 206, 86, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)',
            'rgba(199, 199, 199, 0.7)', 'rgba(83, 102, 255, 0.7)', 'rgba(40, 159, 64, 0.7)', 
            'rgba(210, 105, 30, 0.7)'
        ];
        
        // Graphique des catégories
        if (document.getElementById('categoriesChart')) {
            const categoryNames = @json($categoryNames);
            const categoryColors = @json($categoryColors);
            const categoryData = @json($categoryEventCounts);
            
            if (categoryNames.length > 0) {
                const backgroundColors = categoryColors.map(color => `rgba(var(--bs-${color}-rgb), 0.7)`);
                
                new Chart(document.getElementById('categoriesChart'), {
                    type: 'pie',
                    data: {
                        labels: categoryNames,
                        datasets: [{
                            data: categoryData,
                            backgroundColor: colorPalette.slice(0, categoryNames.length),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                            }
                        }
                    }
                });
            }
        }
        
        // Graphique des tags
        if (document.getElementById('tagsChart') && @json($tagNames).length > 0) {
            const tagNames = @json($tagNames);
            const tagCounts = @json($tagCounts);
            
            new Chart(document.getElementById('tagsChart'), {
                type: 'bar',
                data: {
                    labels: tagNames,
                    datasets: [{
                        label: 'Utilisation',
                        data: tagCounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Graphique des utilisateurs actifs
        if (document.getElementById('usersChart') && @json($userNames).length > 0) {
            const userNames = @json($userNames);
            const userComments = @json($userCommentCounts);
            
            new Chart(document.getElementById('usersChart'), {
                type: 'bar',
                data: {
                    labels: userNames,
                    datasets: [{
                        label: 'Commentaires',
                        data: userComments,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
        
        // Graphique des heures de publication
        if (document.getElementById('hoursChart') && @json($hours).length > 0) {
            const hours = @json($hours);
            const hourCounts = @json($hourCounts);
            
            // Préparer des labels d'heures formatés
            const hourLabels = hours.map(hour => {
                const hourNum = parseInt(hour);
                return `${hourNum}h00`;
            });
            
            new Chart(document.getElementById('hoursChart'), {
                type: 'line',
                data: {
                    labels: hourLabels,
                    datasets: [{
                        label: 'Publications',
                        data: hourCounts,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: 'rgba(255, 99, 132, 1)',
                        pointRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
        }
    });
</script>
@endsection