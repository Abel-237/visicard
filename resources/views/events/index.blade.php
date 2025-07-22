@extends('layouts.app')

@section('content')
<div class="container py-4">
    @if(Auth::check())
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('events.create') }}" class="btn btn-success btn-lg shadow">
                <i class="fas fa-save me-2"></i> Créer un événement
            </a>
        </div>
    @endif
    <div class="row">
        <!-- Filtres et tri -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Filtres</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('events.index') }}" method="GET">
                        <!-- Filtre par catégorie -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Catégorie</label>
                            <select name="category" id="category" class="form-select">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $categoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tri -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Trier par</label>
                            <select name="sort" id="sort" class="form-select">
                                <option value="latest" {{ $sort == 'latest' ? 'selected' : '' }}>Plus récent</option>
                                <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Plus ancien</option>
                                <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Plus populaire</option>
                                <option value="upcoming" {{ $sort == 'upcoming' ? 'selected' : '' }}>Prochainement</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Appliquer les filtres</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Liste des événements -->
        <div class="col-md-9">
            @if($events->isEmpty())
                <div class="alert alert-info">
                    Aucun événement trouvé avec ces critères.
                </div>
            @else
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    @foreach($events as $event)
                        <div class="col">
                            <div class="card h-100">
                                @if($event->media->isNotEmpty())
                                    <div class="text-center p-3">
                                    <img src="{{ Storage::url($event->media->first()->file_path) }}" 
                                             class="img-fluid rounded" 
                                         alt="{{ $event->title }}"
                                             style="max-height: 180px; max-width: 100%; object-fit: contain;">
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="{{ route('events.show', $event->slug) }}" class="text-decoration-none">
                                            {{ $event->title }}
                                            @if($event->status === 'published' && $event->published_at && $event->published_at->isFuture())
                                                <span class="badge bg-warning text-dark ms-2">À venir</span>
                                            @endif
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <small>
                                            <i class="fas fa-calendar-alt"></i> 
                                            {{ $event->event_date ? $event->event_date->format('d/m/Y H:i') : 'Date non définie' }}
                                        </small>
                                    </p>
                                    <p class="card-text">
                                        {{ Str::limit($event->excerpt ?? Str::limit(strip_tags($event->content), 150), 150) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary">
                                            {{ $event->category->name }}
                                        </span>
                                        <small class="text-muted">
                                            <i class="fas fa-eye"></i> {{ $event->views }}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $event->user->getProfileImage() }}" 
                                                 alt="{{ $event->user->name }}" 
                                                 class="rounded-circle me-2 user-avatar" 
                                                 style="width: 32px; height: 32px; object-fit: cover;"
                                                 onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                                            <small class="text-muted">{{ $event->user->name }}</small>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $event->published_at->diffForHumans() }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $events->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 