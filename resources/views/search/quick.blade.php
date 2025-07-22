@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1 class="h2">Résultats de recherche pour "{{ $q }}"</h1>
            <p>{{ $events->total() }} événement(s) trouvé(s)</p>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('search.quick') }}" method="GET" class="d-flex">
                        <input type="text" class="form-control me-2" name="q" value="{{ $q }}" placeholder="Rechercher...">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </form>
                    <div class="mt-2">
                        <a href="{{ route('search.index') }}" class="text-decoration-none">
                            <i class="fas fa-sliders-h me-1"></i> Recherche avancée
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Results -->
    <div class="row">
        @if($events->count() > 0)
            @foreach($events as $event)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm card-hover">
                        @if($event->media->where('file_type', 'image')->first())
                            <img src="{{ asset('storage/' . $event->media->where('file_type', 'image')->first()->file_path) }}" 
                                class="card-img-top" alt="{{ $event->title }}" style="height: 180px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-{{ $event->category->color ?? 'primary' }}">{{ $event->category->name }}</span>
                                <small class="text-muted">{{ $event->published_at->format('d/m/Y') }}</small>
                            </div>
                            <h5 class="card-title">{{ $event->title }}</h5>
                            <p class="card-text">{{ Str::limit($event->excerpt ?? $event->content, 80) }}</p>
                            
                            @if($event->location)
                                <p class="card-text text-muted small">
                                    <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                                </p>
                            @endif
                            
                            @if($event->tags->count() > 0)
                                <div class="mb-2">
                                    @foreach($event->tags as $tag)
                                        <span class="badge bg-light text-dark text-decoration-none me-1">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-outline-primary">Voir les détails</a>
                            <div>
                                <small class="text-muted me-2">
                                    <i class="far fa-eye"></i> {{ $event->views }}
                                </small>
                                @if($event->event_date)
                                    <small class="text-muted">
                                        <i class="far fa-calendar-alt"></i>
                                        {{ $event->event_date->format('d/m/Y') }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="d-flex justify-content-center mt-4">
                {{ $events->withQueryString()->links() }}
            </div>
        @else
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Aucun événement trouvé pour "{{ $q }}".
                </div>
                <p>Suggestions :</p>
                <ul>
                    <li>Vérifiez l'orthographe des mots-clés saisis</li>
                    <li>Essayez d'autres mots-clés</li>
                    <li>Utilisez des termes plus généraux</li>
                    <li><a href="{{ route('search.index') }}">Essayez la recherche avancée</a> pour plus d'options de filtrage</li>
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    .card-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .highlight {
        background-color: #fff3cd;
        padding: 0 2px;
    }
</style>
@endsection 