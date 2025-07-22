@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h1 class="card-title mb-4">Bienvenue, {{ Auth::user()->name }}!</h1>
                    <p class="card-text">Gérez vos événements et votre calendrier en toute simplicité.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Statistiques -->
        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-3x text-primary mb-3"></i>
                    <h3 class="count-up" data-target="{{ $eventCount }}">0</h3>
                    <p class="text-muted mb-0">Événements créés</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-bell fa-3x text-warning mb-3"></i>
                    <h3 class="count-up" data-target="{{ $unreadNotifications }}">0</h3>
                    <p class="text-muted mb-0">Notifications non lues</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-success mb-3"></i>
                    <h3 class="count-up" data-target="{{ $upcomingEvents->count() }}">0</h3>
                    <p class="text-muted mb-0">Événements à venir</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card h-100">
                <div class="card-body text-center">
                    <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
                    <h3 class="count-up" data-target="{{ $recentNotifications->count() }}">0</h3>
                    <p class="text-muted mb-0">Notifications récentes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Événements à venir -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-week text-primary me-2"></i>
                        Événements à venir
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($upcomingEvents as $event)
                            <a href="{{ route('events.show', $event) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $event->title }}</h6>
                                    <small class="text-muted">{{ $event->event_date->format('d/m/Y') }}</small>
                                </div>
                                <small class="text-muted">{{ $event->location }}</small>
                            </a>
                        @empty
                            <div class="list-group-item">
                                <p class="text-muted mb-0">Aucun événement à venir</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('events.index') }}" class="btn btn-outline-primary btn-sm">
                        Voir tous les événements
                    </a>
                </div>
            </div>
        </div>

        <!-- Dernières notifications -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bell text-warning me-2"></i>
                        Dernières notifications
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentNotifications as $notification)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $notification->title }}</h6>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ $notification->message }}</p>
                            </div>
                        @empty
                            <div class="list-group-item">
                                <p class="text-muted mb-0">Aucune notification récente</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('notifications.index') }}" class="btn btn-outline-warning btn-sm">
                        Voir toutes les notifications
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .list-group-item {
        border-left: none;
        border-right: none;
    }
    .list-group-item:first-child {
        border-top: none;
    }
    .list-group-item:last-child {
        border-bottom: none;
    }
    .count-up {
        font-size: 2.5rem;
        font-weight: bold;
        color: #333;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation des compteurs
        const countElements = document.querySelectorAll('.count-up');
        
        countElements.forEach(el => {
            const target = parseInt(el.getAttribute('data-target'));
            const duration = 2000; // 2 secondes
            const step = Math.ceil(target / (duration / 16)); // 60fps
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                el.textContent = current;
                
                if (current >= target) {
                    el.textContent = target;
                    clearInterval(timer);
                }
            }, 16);
        });
    });
</script>
@endpush
