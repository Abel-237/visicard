@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Rapports de participation</h2>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-arrow-left"></i> Retour aux rapports
                    </a>
                </div>
                <div class="card-body">
                    <p class="text-muted">
                        Ce rapport analyse la participation des utilisateurs aux événements en termes de vues, commentaires et likes.
                        Utilisez les filtres ci-dessous pour affiner votre analyse.
                    </p>

                    <!-- Formulaire de filtres -->
                    <form action="{{ route('admin.reports.participation') }}" method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="{{ $startDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="end_date" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="{{ $endDate->format('Y-m-d') }}">
                        </div>
                        <div class="col-md-4">
                            <label for="sort" class="form-label">Trier par</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="views" {{ $sort == 'views' ? 'selected' : '' }}>Vues</option>
                                <option value="comments" {{ $sort == 'comments' ? 'selected' : '' }}>Commentaires</option>
                                <option value="likes" {{ $sort == 'likes' ? 'selected' : '' }}>Likes</option>
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrer
                            </button>
                            <a href="{{ route('admin.reports.participation') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i> Réinitialiser
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <!-- Statistiques générales -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white text-center h-100">
                                <div class="card-body">
                                    <i class="fas fa-eye fa-3x mb-3"></i>
                                    <h3 class="card-title">{{ number_format($totalViews) }}</h3>
                                    <p class="card-text">Vues totales</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white text-center h-100">
                                <div class="card-body">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <h3 class="card-title">{{ number_format($totalComments) }}</h3>
                                    <p class="card-text">Commentaires</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white text-center h-100">
                                <div class="card-body">
                                    <i class="fas fa-heart fa-3x mb-3"></i>
                                    <h3 class="card-title">{{ number_format($totalLikes) }}</h3>
                                    <p class="card-text">Likes</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Graphique de comparaison -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">Top 10 des événements les plus populaires</h4>
                                </div>
                                <div class="card-body">
                                    <canvas id="eventsChart" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des événements -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h4 class="mb-0">Liste des événements ({{ $events->count() }})</h4>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary" id="exportBtn">
                                            <i class="fas fa-download"></i> Exporter en CSV
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Titre</th>
                                                    <th>Catégorie</th>
                                                    <th class="text-center">Date</th>
                                                    <th class="text-center">Vues</th>
                                                    <th class="text-center">Commentaires</th>
                                                    <th class="text-center">Likes</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($events as $event)
                                                    <tr>
                                                        <td>{{ Str::limit($event->title, 40) }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $event->category->color ?? 'primary' }}">
                                                                {{ $event->category->name }}
                                                            </span>
                                                        </td>
                                                        <td class="text-center">{{ $event->published_at->format('d/m/Y') }}</td>
                                                        <td class="text-center">{{ number_format($event->views) }}</td>
                                                        <td class="text-center">{{ number_format($event->comments->count()) }}</td>
                                                        <td class="text-center">{{ number_format($event->likes->count()) }}</td>
                                                        <td class="text-center">
                                                            <a href="{{ route('events.show', $event->slug) }}" 
                                                               class="btn btn-sm btn-outline-primary" target="_blank">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">Aucun événement trouvé pour cette période</td>
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
        // Utilise la variable préparée côté contrôleur
        const labels = @json($eventsNamesLimited);
        const viewsData = @json($eventsViews->toArray());
        const commentsData = @json($eventsComments->toArray());
        const likesData = @json($eventsLikes->toArray());

        // Limiter aux 10 premiers événements pour le graphique
        const top10Labels = labels.slice(0, 10);
        const top10ViewsData = viewsData.slice(0, 10);
        const top10CommentsData = commentsData.slice(0, 10);
        const top10LikesData = likesData.slice(0, 10);

        // Création du graphique
        const ctx = document.getElementById('eventsChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: top10Labels,
                datasets: [
                    {
                        label: 'Vues',
                        data: top10ViewsData,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Commentaires',
                        data: top10CommentsData,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Likes',
                        data: top10LikesData,
                        backgroundColor: 'rgba(255, 99, 132, 0.7)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
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
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });

        // Exportation en CSV
        document.getElementById('exportBtn').addEventListener('click', function() {
            // Utilise la variable préparée côté contrôleur
            const events = @json($eventsExport);
            let csv = 'Titre,Catégorie,Date,Vues,Commentaires,Likes\\n';
            events.forEach(event => {
                csv += `${event.title},${event.category},${event.date},${event.views},${event.comments},${event.likes}\\n`;
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = `rapport-participation-${new Date().toISOString().split('T')[0]}.csv`;
            link.click();
        });
    });
</script>
@endsection 