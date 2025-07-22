@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2">Tableau de bord</h1>
    <div>
        <span class="badge bg-primary">{{ now()->format('d/m/Y') }}</span>
    </div>
</div>

<div class="row mb-4">
    <!-- Users Count -->
    <div class="col-md-3">
        <div class="card card-dashboard text-white bg-primary mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Utilisateurs</h6>
                        <h2 class="display-4 fw-bold mb-0">{{ $stats['usersCount'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small>+{{ $stats['newUsersToday'] }} aujourd'hui</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Count -->
    <div class="col-md-3">
        <div class="card card-dashboard text-white bg-success mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Événements</h6>
                        <h2 class="display-4 fw-bold mb-0">{{ $stats['eventsCount'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-calendar-check fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small>{{ $stats['publishedEventsCount'] }} publiés / +{{ $stats['newEventsToday'] }} aujourd'hui</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Count -->
    <div class="col-md-3">
        <div class="card card-dashboard text-white bg-info mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Catégories</h6>
                        <h2 class="display-4 fw-bold mb-0">{{ $stats['categoriesCount'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-tags fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small>Utilisées pour organiser les événements</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Count -->
    <div class="col-md-3">
        <div class="card card-dashboard text-white bg-warning mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Commentaires</h6>
                        <h2 class="display-4 fw-bold mb-0">{{ $stats['commentsCount'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-comments fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small>+{{ $stats['newCommentsToday'] }} aujourd'hui</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Business Cards Count -->
    <div class="col-md-3">
        <div class="card card-dashboard text-white bg-purple mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Cartes de visite</h6>
                        <h2 class="display-4 fw-bold mb-0">{{ $stats['businessCardsCount'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-id-card fa-3x opacity-50"></i>
                    </div>
                </div>
                <div class="mt-3">
                    <small>+{{ $stats['newBusinessCardsToday'] }} aujourd'hui</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Business Cards Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Statistiques des cartes de visite</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-id-card fa-2x text-purple me-3"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Total des cartes</h6>
                                <h3 class="mb-0">{{ $stats['businessCardsCount'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-calendar-day fa-2x text-success me-3"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Nouvelles aujourd'hui</h6>
                                <h3 class="mb-0">{{ $stats['newBusinessCardsToday'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-building fa-2x text-info me-3"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Entreprises représentées</h6>
                                <h3 class="mb-0">{{ $stats['uniqueCompaniesCount'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Événements créés par mois en {{ date('Y') }}</h5>
            </div>
            <div class="card-body">
                <canvas id="eventsChart" height="300"></canvas>
            </div>
        </div>
    </div>

    <!-- Upcoming Events -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Événements à venir</h5>
                <span class="badge bg-primary rounded-pill">{{ $upcomingEvents->count() }}</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($upcomingEvents as $event)
                        <a href="{{ route('events.show', $event->slug) }}" class="list-group-item list-group-item-action">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $event->title }}</h6>
                                <small>{{ $event->event_date->format('d/m/Y') }}</small>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> {{ $event->location ?: 'Non défini' }}
                            </small>
                        </a>
                    @empty
                        <div class="list-group-item">
                            <p class="text-muted mb-0">Aucun événement à venir</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Popular Events -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Événements populaires</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Catégorie</th>
                                <th>Vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($popularEvents as $event)
                                <tr>
                                    <td>
                                        <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $event->category->color ?? 'secondary' }}">
                                            {{ $event->category->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <i class="far fa-eye"></i> {{ $event->views }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">Aucun événement trouvé</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Comments -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Derniers commentaires</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($latestComments as $comment)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $comment->user->name }}</h6>
                                <small>{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ Str::limit($comment->content, 100) }}</p>
                            <small class="text-muted">
                                <i class="fas fa-calendar-alt"></i> 
                                <a href="{{ route('events.show', $comment->event->slug) }}">{{ $comment->event->title }}</a>
                            </small>
                        </div>
                    @empty
                        <div class="list-group-item">
                            <p class="text-muted mb-0">Aucun commentaire trouvé</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Business Cards -->
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title">Dernières cartes de visite</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($latestBusinessCards as $card)
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between">
                                <h6 class="mb-1">{{ $card->name }}</h6>
                                <small>{{ $card->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-1">{{ $card->position }} chez {{ $card->company }}</p>
                            <small class="text-muted">
                                <i class="fas fa-envelope"></i> {{ $card->email }} |
                                <i class="fas fa-phone"></i> {{ $card->phone }}
                            </small>
                        </div>
                    @empty
                        <div class="list-group-item">
                            <p class="text-muted mb-0">Aucune carte de visite trouvée</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get chart data from the data attribute
        var monthlyEventsData = {!! json_encode($monthlyEvents) !!};
        
        // Prepare data for Chart.js
        var labels = [];
        var data = [];
        var monthNames = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];
        
        // Initialize all months with zero
        for (var i = 1; i <= 12; i++) {
            labels.push(monthNames[i-1]);
            data.push(0);
        }
        
        // Fill in the actual data
        for (var j = 0; j < monthlyEventsData.length; j++) {
            var item = monthlyEventsData[j];
            var monthIndex = parseInt(item.month) - 1;
            data[monthIndex] = item.total;
        }
        
        // Create the chart
        var ctx = document.getElementById('eventsChart').getContext('2d');
        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre d\'événements',
                    data: data,
                    backgroundColor: 'rgba(0, 123, 255, 0.5)',
                    borderColor: 'rgba(0, 123, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0
                    }
                }
            }
        });
    });
</script>
@endsection 