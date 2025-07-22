@extends('layouts.app')

@section('content')
<div class="search-bg d-flex align-items-start min-vh-100 py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h1 class="fw-bold display-5 mb-2" style="letter-spacing:1px;">Recherche avancée</h1>
                <p class="text-muted mb-0">Trouvez rapidement les événements qui vous intéressent</p>
            </div>
        </div>
        <!-- Search Form -->
        <div class="row mb-5 justify-content-center">
            <div class="col-lg-10 col-xl-8">
                <div class="search-card shadow-lg border-0 rounded-4 bg-white p-4 p-md-5 mx-auto mb-4">
                    <div class="mb-4 text-center">
                        <span class="search-icon d-inline-block mb-2"><i class="fas fa-search fa-2x text-primary"></i></span>
                        <h3 class="fw-semibold mb-0">Filtres de recherche</h3>
                    </div>
                    <form action="{{ route('search.index') }}" method="GET" class="row g-3">
                        <div class="col-md-12">
                            <label for="keyword" class="form-label">Mots clés</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-key"></i></span>
                                <input type="text" class="form-control" id="keyword" name="keyword" value="{{ $keyword ?? '' }}" placeholder="Rechercher par titre, contenu...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="date_from" class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="date_to" class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo ?? '' }}">
                        </div>
                        <div class="col-md-12">
                            <label for="location" class="form-label">Lieu</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" id="location" name="location" value="{{ $location ?? '' }}" placeholder="Rechercher par lieu...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Catégorie</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ (isset($category) && $category == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="tag" class="form-label">Tag</label>
                            <select class="form-select" id="tag" name="tag">
                                <option value="">Tous les tags</option>
                                @foreach($tags as $t)
                                    <option value="{{ $t->id }}" {{ (isset($tag) && $tag == $t->id) ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <a href="{{ route('search.index') }}" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-redo"></i> Réinitialiser
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Search Results -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                    <h2 class="fw-semibold h3 mb-0">Résultats de la recherche</h2>
                    <span class="badge bg-primary fs-6">{{ $events->total() }} événement(s) trouvé(s)</span>
                </div>
                @if($events->count() > 0)
                    <div class="row">
                        @foreach($events as $event)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="event-card card h-100 border-0 shadow-sm card-hover rounded-4 overflow-hidden">
                                    @if($event->media->where('file_type', 'image')->first())
                                        <img src="{{ asset('storage/' . $event->media->where('file_type', 'image')->first()->file_path) }}" 
                                            class="card-img-top event-img" alt="{{ $event->title }}">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center event-img">
                                            <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body pb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-{{ $event->category->color ?? 'primary' }} px-3 py-2">{{ $event->category->name }}</span>
                                            <small class="text-muted">{{ $event->published_at->format('d/m/Y') }}</small>
                                        </div>
                                        <h5 class="card-title fw-bold">{{ $event->title }}</h5>
                                        <p class="card-text text-muted">{{ Str::limit($event->excerpt ?? $event->content, 80) }}</p>
                                        @if($event->location)
                                            <p class="card-text text-muted small mb-1">
                                                <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                                            </p>
                                        @endif
                                        @if($event->tags->count() > 0)
                                            <div class="mb-2">
                                                @foreach($event->tags as $t)
                                                    <a href="{{ route('search.index', ['tag' => $t->id]) }}" class="badge bg-light text-dark text-decoration-none me-1">
                                                        #{{ $t->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-white d-flex justify-content-between align-items-center border-0 pt-0">
                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Voir les détails</a>
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
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->withQueryString()->links() }}
                    </div>
                @else
                    <div class="alert alert-info rounded-4 shadow-sm border-0">
                        <i class="fas fa-info-circle me-2"></i> Aucun événement trouvé correspondant à vos critères de recherche.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .search-bg {
        background: linear-gradient(135deg, #f5f8ff 0%, #e6eaff 100%);
        min-height: 100vh;
    }
    .search-card {
        border-radius: 2rem !important;
        background: #fff;
        box-shadow: 0 8px 32px 0 rgba(36,45,224,0.10);
    }
    .search-icon {
        background: #f5f8ff;
        border-radius: 50%;
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(36,45,224,0.08);
    }
    .event-card {
        border-radius: 1.5rem !important;
        transition: transform 0.3s, box-shadow 0.3s;
        background: #fff;
    }
    .event-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 1rem 2rem rgba(36,45,224,0.10) !important;
    }
    .event-img {
        height: 180px;
        object-fit: cover;
        border-top-left-radius: 1.5rem !important;
        border-top-right-radius: 1.5rem !important;
    }
    .form-label {
        color: #222;
        font-weight: 600;
    }
    .input-group-text {
        border-radius: 8px 0 0 8px !important;
        border-right: 0 !important;
    }
    .form-control, .form-select {
        border-radius: 0 8px 8px 0 !important;
        border-left: 0 !important;
        box-shadow: none !important;
    }
    .btn-primary {
        background: #6B73FF;
        border-color: #6B73FF;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .btn-primary:hover {
        background: #000DFF;
        border-color: #000DFF;
    }
    .btn-outline-primary {
        border-color: #6B73FF;
        color: #6B73FF;
    }
    .btn-outline-primary:hover {
        background: #6B73FF;
        color: #fff;
    }
    .badge.bg-primary {
        background: #6B73FF !important;
    }
    @media (max-width: 767px) {
        .search-card { padding: 1.5rem !important; }
        .search-bg { padding: 0 !important; }
        .event-img { height: 140px; }
    }
</style>
@endpush