@extends('layouts.app')

@section('content')
<div class="container-fluid py-0">
    <!-- Hero Section -->
    <div class="hero-container mb-5 position-relative">
        <div class="hero-background"></div>
        <div class="container position-relative">
            <div class="row align-items-center min-vh-75">
                <div class="col-lg-6 py-5 hero-content">
                    <h1 class="display-4 fw-bold mb-4 text-shadow">Bienvenue sur votre <span class="text-primary">plateforme sociale</span> d'événements</h1>
                    <p class="lead mb-4 hero-text">
                        Découvrez, participez et connectez-vous avec d'autres personnes à travers des événements qui vous passionnent.
                    </p>
                    @guest
                        <div class="mt-4 d-flex flex-wrap gap-3 hero-buttons">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg pulse-button">
                                <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                            </a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Créer un compte
                            </a>
                        </div>
                    @else
                        <div class="mt-4 hero-buttons">
                            <a href="{{ route('calendar.index') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-calendar-alt me-2"></i>Voir le calendrier
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-lg ms-2">
                                <i class="fas fa-compass me-2"></i>Explorer
                            </a>
                        </div>
                    @endguest
                </div>
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="hero-image-container">
                        <img src="{{ asset('images/event-illustration.svg') }}" alt="Events Illustration" class="img-fluid floating-animation">
                        <div class="floating-card card-1">
                            <div class="card shadow-lg rounded-4 border-0">
                                <div class="card-body d-flex align-items-center">
                                    <div class="avatar-group me-3">
                                        <span class="avatar avatar-sm">🎉</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Festival de musique</h6>
                                        <small class="text-muted">12 personnes y participent</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="floating-card card-2">
                            <div class="card shadow-lg rounded-4 border-0">
                                <div class="card-body d-flex align-items-center">
                                    <div class="avatar-group me-3">
                                        <span class="avatar avatar-sm">📚</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">Atelier formation</h6>
                                        <small class="text-muted">Commence dans 3 jours</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wave-divider">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 120">
                <path fill="#ffffff" fill-opacity="1" d="M0,64L80,69.3C160,75,320,85,480,80C640,75,800,53,960,48C1120,43,1280,53,1360,58.7L1440,64L1440,120L1360,120C1280,120,1120,120,960,120C800,120,640,120,480,120C320,120,160,120,80,120L0,120Z"></path>
            </svg>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="container mb-5">
        <div class="card border-0 shadow-sm rounded-4 sticky-top quick-actions-card">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div class="quick-actions">
                        <a href="{{ route('calendar.index') }}" class="quick-action-btn active">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Calendrier</span>
                        </a>
                        <a href="{{ route('search.index') }}" class="quick-action-btn">
                            <i class="fas fa-search"></i>
                            <span>Recherche</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-plus-circle"></i>
                            <span>Créer</span>
                        </a>
                        <a href="#" class="quick-action-btn">
                            <i class="fas fa-star"></i>
                            <span>Favoris</span>
                        </a>
                        <a href="#" class="quick-action-btn d-none d-md-flex">
                            <i class="fas fa-bell"></i>
                            <span>Notifications</span>
                        </a>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="search-mini d-none d-md-block me-2">
                            <form action="{{ route('search.quick') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="q" class="form-control" placeholder="Rechercher...">
                                    <button type="submit" class="btn btn-light">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="view-toggle d-none d-md-flex">
                            <button type="button" id="viewGrid" class="btn btn-sm btn-light active">
                                <i class="fas fa-th-large"></i>
                            </button>
                            <button type="button" id="viewList" class="btn btn-sm btn-light">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <!-- Stats Cards -->
        <div class="row mb-5 stats-container">
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card stats-card border-0 rounded-4 shadow-sm animate-on-scroll fadeInUp" data-delay="0">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stats-icon bg-primary-soft me-3">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <h5 class="card-title mb-0">Total des événements</h5>
                        </div>
                        <p class="card-text display-5 fw-bold mb-2 count-up" data-target="{{ $stats['totalEvents'] }}">0</p>
                        <div class="stats-trend positive">
                            <i class="fas fa-arrow-up me-1"></i>
                            <span>{{ rand(1, 15) }}% ce mois</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="card stats-card border-0 rounded-4 shadow-sm animate-on-scroll fadeInUp" data-delay="200">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stats-icon bg-success-soft me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="card-title mb-0">Événements à venir</h5>
                        </div>
                        <p class="card-text display-5 fw-bold mb-2 count-up" data-target="{{ $stats['upcomingEvents'] }}">0</p>
                        <div class="stats-trend">
                            <i class="fas fa-calendar-day me-1"></i>
                            <span>Prochain: <span class="fw-bold">{{ now()->addDays(rand(1, 7))->format('d/m') }}</span></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stats-card border-0 rounded-4 shadow-sm animate-on-scroll fadeInUp" data-delay="400">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="stats-icon bg-info-soft me-3">
                                <i class="fas fa-tags"></i>
                            </div>
                            <h5 class="card-title mb-0">Catégories</h5>
                        </div>
                        <p class="card-text display-5 fw-bold mb-2 count-up" data-target="{{ $stats['categories'] }}">0</p>
                        <div class="stats-trend">
                            <a href="#" class="btn btn-sm btn-light rounded-pill">
                                <i class="fas fa-list me-1"></i>Voir toutes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Events -->
        @if(isset($featuredEvents) && $featuredEvents->count() > 0)
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h4 class="section-heading mb-0">
                        <i class="fas fa-star text-warning me-2"></i>Événements à la une
                    </h4>
                    <a href="#" class="text-decoration-none link-primary">Voir tous</a>
                </div>
                
                <div class="featured-events-carousel">
                    <div class="row flex-nowrap overflow-auto featured-row pb-2">
                        @foreach($featuredEvents as $event)
                        <div class="col-md-6 col-lg-4 featured-col">
                            <div class="card h-100 border-0 rounded-4 shadow-sm featured-card">
                                <div class="position-relative">
                                    @if($event->media->where('file_type', 'image')->first())
                                        <img src="{{ asset('storage/' . $event->media->where('file_type', 'image')->first()->file_path) }}" 
                                            class="card-img-top rounded-top-4" alt="{{ $event->title }}" style="height: 180px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 180px;">
                                            <i class="fas fa-calendar-alt fa-3x text-secondary"></i>
                                        </div>
                                    @endif
                                    <div class="featured-badge">En vedette</div>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-{{ $event->category->color ?? 'primary' }} rounded-pill">
                                            {{ $event->category->name }}
                                        </span>
                                        @if($event->event_date)
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>{{ $event->event_date->format('d/m/Y') }}
                                        </small>
                                        @endif
                                    </div>
                                    <h5 class="card-title">{{ $event->title }}</h5>
                                    <p class="card-text small text-secondary">{{ Str::limit($event->excerpt ?? $event->content, 80) }}</p>
                                    
                                    @if($event->location)
                                    <div class="d-flex align-items-center mb-3 location-info">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <span class="small text-secondary">{{ Str::limit($event->location, 25) }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="attendee-avatars">
                                            <span class="avatar-count">{{ rand(5, 25) }}</span>
                                            <span class="avatar-label">participants</span>
                                        </div>
                                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-primary rounded-pill">
                                            Voir détails
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="row mb-5">
            <div class="col">
                <div class="alert alert-info rounded-4 border-0 shadow-sm">
                    <i class="fas fa-info-circle me-2"></i>Aucun événement à la une pour le moment.
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content Area -->
        <div class="row">
            <!-- Sidebar - Left Column -->
            <div class="col-lg-3 mb-4 mb-lg-0">
                <div class="position-sticky sidebar" style="top: 90px;">
                    <!-- Categories Card -->
                    <div class="card sidebar-card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-header border-0 bg-transparent pt-3 pb-0">
                            <h5 class="card-title">
                                <i class="fas fa-layer-group text-primary me-2"></i>Catégories
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush sidebar-list" style="max-height: 350px; overflow-y: auto;">
                                <a href="{{ route('home') }}" class="list-group-item border-0 list-group-item-action d-flex justify-content-between align-items-center {{ !isset($categoryId) ? 'active' : '' }}">
                                    <span><i class="fas fa-th-list me-2"></i>Toutes les catégories</span>
                                    <span class="badge bg-primary rounded-pill">{{ $stats['totalEvents'] }}</span>
                                </a>
                                @if(isset($categories))
                                    @foreach($categories as $category)
                                        <a href="{{ route('home', ['category' => $category->id]) }}" 
                                        class="list-group-item border-0 list-group-item-action d-flex justify-content-between align-items-center {{ isset($categoryId) && $categoryId == $category->id ? 'active' : '' }}">
                                            <span>{{ $category->name }}</span>
                                            <span class="badge bg-primary rounded-pill">{{ $category->events_count }}</span>
                                        </a>
                                    @endforeach
                                @endif
                            </div>
                            @if(method_exists($categories, 'links'))
                                <div class="mt-2 px-2 d-none">
                                    {{-- Pagination supprimée, scroll activé --}}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="card sidebar-card border-0 rounded-4 shadow-sm mb-4">
                        <div class="card-header border-0 bg-transparent pt-3 pb-0">
                            <h5 class="card-title">
                                <i class="far fa-calendar-alt text-primary me-2"></i>Prochains événements
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush sidebar-list">
                                @forelse($upcomingEvents as $event)
                                    <a href="{{ route('events.show', $event->slug) }}" class="list-group-item border-0 list-group-item-action">
                                        <div class="d-flex align-items-center">
                                            <div class="event-date-mini me-3">
                                                <div class="date-day">{{ $event->event_date->format('d') }}</div>
                                                <div class="date-month">{{ $event->event_date->locale('fr')->format('M') }}</div>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 text-truncate" style="max-width: 180px;">{{ $event->title }}</h6>
                                                <small class="text-muted d-flex align-items-center">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <span class="text-truncate" style="max-width: 150px;">{{ $event->location ?? 'Non spécifié' }}</span>
                                                </small>
                                            </div>
                                        </div>
                                    </a>
                                @empty
                                    <div class="list-group-item border-0">
                                        <p class="mb-0 text-muted">Aucun événement à venir</p>
                                    </div>
                                @endforelse
                                <div class="list-group-item border-0 text-center">
                                    <a href="{{ route('calendar.index') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-calendar me-1"></i>Voir le calendrier
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Suggestions Card -->
                    <div class="card sidebar-card border-0 rounded-4 shadow-sm">
                        <div class="card-header border-0 bg-transparent pt-3 pb-0">
                            <h5 class="card-title">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Suggestions
                            </h5>
                        </div>
                        <div class="card-body pt-2">
                            <div class="suggestion-item d-flex align-items-center mb-3">
                                <div class="suggestion-icon me-3 bg-primary-soft">
                                    <i class="fas fa-user-friends"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Invitez vos amis</h6>
                                    <p class="mb-0 small text-muted">Partagez vos événements préférés</p>
                                </div>
                            </div>
                            <div class="suggestion-item d-flex align-items-center mb-3">
                                <div class="suggestion-icon me-3 bg-success-soft">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Activez les notifications</h6>
                                    <p class="mb-0 small text-muted">Pour ne manquer aucun événement</p>
                                </div>
                            </div>
                            <div class="suggestion-item d-flex align-items-center">
                                <div class="suggestion-icon me-3 bg-info-soft">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Téléchargez l'application</h6>
                                    <p class="mb-0 small text-muted">Pour une meilleure expérience</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content - Events -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="section-heading mb-0">
                        <i class="fas fa-stream text-primary me-2"></i>Événements récents
                    </h4>
                    <div class="d-flex align-items-center">
                        <div class="dropdown me-2 d-none d-md-block">
                            <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter me-1"></i>Filtrer
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                <li><a class="dropdown-item" href="#">Tous les événements</a></li>
                                <li><a class="dropdown-item" href="#">Cette semaine</a></li>
                                <li><a class="dropdown-item" href="#">Ce mois-ci</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">Personnaliser...</a></li>
                            </ul>
                        </div>
                        <select class="form-select form-select-sm" id="sortEvents" onchange="window.location.href=this.value">
                            <option value="{{ route('home', array_merge(request()->query(), ['sort' => 'latest'])) }}" 
                                    {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Plus récents</option>
                            <option value="{{ route('home', array_merge(request()->query(), ['sort' => 'oldest'])) }}" 
                                    {{ request('sort') == 'oldest' ? 'selected' : '' }}>Plus anciens</option>
                            <option value="{{ route('home', array_merge(request()->query(), ['sort' => 'popular'])) }}" 
                                    {{ request('sort') == 'popular' ? 'selected' : '' }}>Les plus vus</option>
                            <option value="{{ route('home', array_merge(request()->query(), ['sort' => 'upcoming'])) }}" 
                                    {{ request('sort') == 'upcoming' ? 'selected' : '' }}>À venir</option>
                        </select>
                    </div>
                </div>

                @if(isset($events) && $events->count() > 0)
                    <!-- Grid View (default) -->
                    <div id="gridView" class="row g-3">
                        @foreach($events as $key => $event)
                            <div class="col-md-6 animate-on-scroll fadeInUp" data-delay="{{ $key * 100 }}">
                                <div class="card h-100 event-card border-0 rounded-4 shadow-sm">
                                    <div class="position-relative">
                                        @if($event->media->where('file_type', 'image')->first())
                                            <img src="{{ asset('storage/' . $event->media->where('file_type', 'image')->first()->file_path) }}" 
                                                class="card-img-top rounded-top-4" alt="{{ $event->title }}" style="height: 180px; object-fit: cover;">
                                        @else
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center rounded-top-4" style="height: 180px;">
                                                <i class="fas fa-calendar-alt fa-3x text-secondary"></i>
                                            </div>
                                        @endif
                                        @if($event->event_date && $event->event_date > now())
                                            <div class="event-date-badge position-absolute top-0 end-0 m-3">
                                                <div class="date-day">{{ $event->event_date->format('d') }}</div>
                                                <div class="date-month">{{ $event->event_date->locale('fr')->format('M') }}</div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-{{ $event->category->color ?? 'primary' }} rounded-pill">
                                                {{ $event->category->name }}
                                            </span>
                                            <small class="text-muted">
                                                <i class="far fa-eye me-1"></i>{{ $event->views }}
                                            </small>
                                        </div>
                                        <h5 class="card-title mb-1">{{ $event->title }}</h5>
                                        <p class="card-text small text-secondary mb-2">{{ Str::limit($event->excerpt ?? $event->content, 80) }}</p>
                                        
                                        @if($event->tags->count() > 0)
                                            <div class="mb-3 tags-container">
                                                @foreach($event->tags->take(3) as $tag)
                                                    <a href="{{ route('home', ['tag' => $tag->id]) }}" class="tag-badge">
                                                        #{{ $tag->name }}
                                                    </a>
                                                @endforeach
                                                @if($event->tags->count() > 3)
                                                    <span class="tag-badge">+{{ $event->tags->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if($event->location)
                                            <div class="d-flex align-items-center mb-3 location-info">
                                                <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                                <span class="small text-secondary">{{ Str::limit($event->location, 25) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center">
                                        <div class="event-info">
                                            @if($event->event_date)
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>{{ $event->event_date->format('H:i') }}
                                                </small>
                                            @endif
                                        </div>
                                        <div class="event-actions">
                                            <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-primary rounded-pill">
                                                <i class="fas fa-info-circle me-1 d-none d-sm-inline-block"></i>Détails
                                            </a>
                                            @if($event->event_date && $event->event_date > now())
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill ms-1">
                                                    <i class="fas fa-bell"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- List View (hidden by default) -->
                    <div id="listView" class="d-none">
                        <div class="list-group">
                            @foreach($events as $key => $event)
                                <a href="{{ route('events.show', $event->slug) }}" class="list-group-item list-group-item-action mb-3 rounded-4 shadow-sm event-list-item border-0 animate-on-scroll fadeInRight" data-delay="{{ $key * 100 }}">
                                    <div class="row g-0">
                                        <div class="col-md-3 position-relative">
                                            @if($event->media->where('file_type', 'image')->first())
                                                <img src="{{ asset('storage/' . $event->media->where('file_type', 'image')->first()->file_path) }}" 
                                                    class="img-fluid rounded-start h-100" alt="{{ $event->title }}" style="object-fit: cover; width: 100%;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center h-100 rounded-start">
                                                    <i class="fas fa-calendar-alt fa-3x text-secondary"></i>
                                                </div>
                                            @endif
                                            @if($event->event_date && $event->event_date > now())
                                                <div class="event-date-badge-list position-absolute top-0 start-0 m-2">
                                                    <div class="date-day">{{ $event->event_date->format('d') }}</div>
                                                    <div class="date-month">{{ $event->event_date->locale('fr')->format('M') }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between">
                                                    <h5 class="card-title fw-bold mb-1">{{ $event->title }}</h5>
                                                    <span class="badge bg-{{ $event->category->color ?? 'primary' }} rounded-pill">{{ $event->category->name }}</span>
                                                </div>
                                                <p class="card-text small text-secondary">{{ Str::limit($event->excerpt ?? $event->content, 120) }}</p>
                                                
                                                @if($event->tags->count() > 0)
                                                    <div class="mb-2 tags-container">
                                                        @foreach($event->tags->take(3) as $tag)
                                                            <span class="tag-badge">#{{ $tag->name }}</span>
                                                        @endforeach
                                                        @if($event->tags->count() > 3)
                                                            <span class="tag-badge">+{{ $event->tags->count() - 3 }}</span>
                                                        @endif
                                                    </div>
                                                @endif
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <small class="text-muted me-3">
                                                            <i class="far fa-calendar-alt me-1"></i>
                                                            {{ $event->event_date ? $event->event_date->format('d/m/Y') : 'Non défini' }}
                                                        </small>
                                                        <small class="text-muted me-3">
                                                            <i class="far fa-eye me-1"></i>{{ $event->views }} vues
                                                        </small>
                                                        @if($event->location)
                                                            <small class="text-muted">
                                                                <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($event->location, 25) }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="text-end">
                                                        <button class="btn btn-sm btn-primary rounded-pill">
                                                            <i class="fas fa-arrow-right me-1"></i>Détails
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $events->links() }}
                    </div>
                @else
                    <div class="alert alert-info rounded-4 shadow-sm border-0">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-info-circle fa-2x me-3 text-primary"></i>
                            <div>
                                <h5 class="mb-1">Aucun événement trouvé</h5>
                                <p class="mb-0">
                                    @if(isset($categoryId))
                                        <a href="{{ route('home') }}" class="alert-link">Voir tous les événements</a>
                                    @else
                                        Essayez de modifier vos critères de recherche.
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    :root {
        --fb-blue: #1877F2;
        --fb-hover: #166FE5;
        --fb-bg: #F0F2F5;
        --card-radius: 0.75rem;
        --shadow-sm: 0 2px 5px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 10px rgba(0, 0, 0, 0.08);
    }

    body {
        background-color: var(--fb-bg);
        color: #1c1e21;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }
    
    /* Hero section */
    .hero-container {
        padding-top: 40px;
        padding-bottom: 80px;
        background-color: #ffffff;
        overflow: hidden;
    }

    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background-image: radial-gradient(circle at 10% 20%, rgba(236,113,38,0.05) 0%, rgba(0,179,255,0.05) 90%);
    }

    .min-vh-75 {
        min-height: 75vh;
    }

    .hero-image-container {
        position: relative;
        height: 100%;
        padding: 30px;
    }

    .floating-animation {
        animation: float 6s ease-in-out infinite;
        max-width: 80%;
    }

    .floating-card {
        position: absolute;
        z-index: 2;
    }

    .card-1 {
        top: 20%;
        left: 0;
        animation: float-card1 5s ease-in-out infinite;
    }

    .card-2 {
        bottom: 20%;
        right: 0;
        animation: float-card2 6s ease-in-out infinite 1s;
    }

    .avatar-group {
        display: flex;
        align-items: center;
    }

    .avatar {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background-color: #e4e6eb;
        font-size: 18px;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 16px;
    }

    .wave-divider {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        overflow: hidden;
        line-height: 0;
    }

    /* Quick Actions Bar */
    .quick-actions-card {
        top: 70px;
        z-index: 900;
        margin-top: -20px;
        background-color: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .quick-actions {
        display: flex;
        overflow-x: auto;
        -ms-overflow-style: none;
        scrollbar-width: none;
        padding: 5px 0;
    }

    .quick-actions::-webkit-scrollbar {
        display: none;
    }

    .quick-action-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 8px 16px;
        color: #65676b;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .quick-action-btn i {
        font-size: 20px;
        margin-bottom: 4px;
    }

    .quick-action-btn:hover,
    .quick-action-btn.active {
        background-color: rgba(24, 119, 242, 0.1);
        color: var(--fb-blue);
    }

    .search-mini .input-group {
        background-color: #F0F2F5;
        border-radius: 20px;
        overflow: hidden;
    }

    .search-mini .form-control {
        border: none;
        background-color: transparent;
        padding-left: 16px;
        font-size: 14px;
    }

    .search-mini .btn {
        border: none;
        background-color: transparent;
    }

    /* Stats Cards */
    .stats-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md) !important;
    }

    .stats-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-size: 20px;
    }

    .bg-primary-soft {
        background-color: rgba(24, 119, 242, 0.1);
        color: var(--fb-blue);
    }

    .bg-success-soft {
        background-color: rgba(42, 187, 155, 0.1);
        color: #2abb9b;
    }

    .bg-info-soft {
        background-color: rgba(0, 186, 255, 0.1);
        color: #00baff;
    }

    .stats-trend {
        font-size: 14px;
        color: #65676b;
        margin-top: 10px;
    }

    .stats-trend.positive {
        color: #2abb9b;
    }

    /* Featured Events */
    .section-heading {
        font-weight: 700;
        font-size: 20px;
        color: #1c1e21;
    }

    .featured-row {
        scroll-snap-type: x mandatory;
        padding-bottom: 15px;
        margin-right: -10px;
        margin-left: -10px;
    }

    .featured-col {
        scroll-snap-align: start;
        padding-right: 10px;
        padding-left: 10px;
        min-width: 300px;
    }

    .featured-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .featured-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md) !important;
    }

    .featured-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: linear-gradient(45deg, #ff9a9e 0%, #fad0c4 99%, #fad0c4 100%);
        color: #1c1e21;
        padding: 5px 10px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .attendee-avatars {
        display: flex;
        align-items: center;
    }

    .avatar-count {
        font-weight: 600;
        color: #1c1e21;
        margin-right: 4px;
    }

    .avatar-label {
        color: #65676b;
        font-size: 13px;
    }

    /* Sidebar */
    .sidebar-card {
        border-radius: var(--card-radius);
        margin-bottom: 20px;
    }

    .sidebar-card .card-title {
        font-size: 17px;
        font-weight: 600;
    }

    .sidebar-list .list-group-item {
        padding: 12px 15px;
        transition: background-color 0.2s;
    }

    .sidebar-list .list-group-item:hover {
        background-color: rgba(24, 119, 242, 0.05);
    }

    .sidebar-list .list-group-item.active {
        background-color: rgba(24, 119, 242, 0.1);
        color: var(--fb-blue);
        font-weight: 500;
    }

    .event-date-mini {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #f0f2f5;
        border-radius: 8px;
        text-align: center;
    }

    .event-date-mini .date-day {
        font-weight: 600;
        font-size: 14px;
        line-height: 1;
    }

    .event-date-mini .date-month {
        font-size: 11px;
        text-transform: uppercase;
        line-height: 1;
    }

    .suggestion-icon {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 16px;
    }

    /* Events */
    .event-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md) !important;
    }

    .event-date-badge {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 6px 10px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .event-date-badge .date-day {
        font-weight: 600;
        font-size: 18px;
        line-height: 1;
    }

    .event-date-badge .date-month {
        font-size: 12px;
        text-transform: uppercase;
        line-height: 1;
    }

    .event-date-badge-list {
        background-color: #ffffff;
        border-radius: 8px;
        padding: 6px 10px;
        text-align: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .location-info {
        font-size: 13px;
    }

    .tag-badge {
        display: inline-block;
        background-color: #f0f2f5;
        color: #65676b;
        padding: 4px 10px;
        margin-right: 5px;
        margin-bottom: 5px;
        border-radius: 15px;
        font-size: 12px;
        transition: all 0.2s;
    }

    .tag-badge:hover {
        background-color: rgba(24, 119, 242, 0.1);
        color: var(--fb-blue);
    }

    .btn-primary {
        background-color: var(--fb-blue);
        border-color: var(--fb-blue);
    }

    .btn-primary:hover {
        background-color: var(--fb-hover);
        border-color: var(--fb-hover);
    }

    .btn-outline-primary {
        color: var(--fb-blue);
        border-color: var(--fb-blue);
    }

    .btn-outline-primary:hover {
        background-color: var(--fb-blue);
        border-color: var(--fb-blue);
    }
    
    .rounded-pill {
        border-radius: 20px !important;
    }
    
    .rounded-4 {
        border-radius: var(--card-radius) !important;
    }

    /* Animations */
    @keyframes float {
        0% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-15px);
        }
        100% {
            transform: translateY(0px);
        }
    }

    @keyframes float-card1 {
        0% {
            transform: translateY(0px) rotate(-2deg);
        }
        50% {
            transform: translateY(-10px) rotate(0deg);
        }
        100% {
            transform: translateY(0px) rotate(-2deg);
        }
    }

    @keyframes float-card2 {
        0% {
            transform: translateY(0px) rotate(2deg);
        }
        50% {
            transform: translateY(-12px) rotate(0deg);
        }
        100% {
            transform: translateY(0px) rotate(2deg);
        }
    }

    .animate-on-scroll {
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease;
    }

    .pulse-button {
        position: relative;
        overflow: hidden;
    }

    .pulse-button:after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 120%;
        height: 120%;
        border-radius: inherit;
        transform: translate(-50%, -50%) scale(0);
        animation: pulse 2s infinite;
        background-color: rgba(255, 255, 255, 0.3);
        z-index: 0;
    }

    @keyframes pulse {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 0.8;
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 0;
        }
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .hero-container {
            padding-top: 20px;
            padding-bottom: 60px;
        }
        
        .quick-actions-card {
            top: 56px;
        }
        
        .quick-action-btn span {
            font-size: 12px;
        }
        
        .section-heading {
            font-size: 18px;
        }
        
        .floating-card {
            display: none;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation on scroll
        const animateElements = document.querySelectorAll('.animate-on-scroll');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const delay = el.getAttribute('data-delay') || 0;
                    
                    setTimeout(() => {
                        el.style.opacity = 1;
                        el.style.transform = 'translateY(0)';
                    }, delay);
                    
                    observer.unobserve(el);
                }
            });
        }, {
            threshold: 0.1
        });
        
        animateElements.forEach(el => {
            observer.observe(el);
        });
        
        // Count-up animation
        const countElements = document.querySelectorAll('.count-up');
        
        countElements.forEach(el => {
            const target = parseInt(el.getAttribute('data-target'));
            const duration = 2000; // 2 seconds
            const step = Math.ceil(target / (duration / 16)); // 60fps
            let current = 0;
            
            const observer = new IntersectionObserver((entries) => {
                if (entries[0].isIntersecting) {
                    const timer = setInterval(() => {
                        current += step;
                        el.textContent = current;
                        
                        if (current >= target) {
                            el.textContent = target;
                            clearInterval(timer);
                        }
                    }, 16);
                    
                    observer.unobserve(el);
                }
            });
            
            observer.observe(el);
        });
        
        // View switching functionality
        const gridView = document.getElementById('gridView');
        const listView = document.getElementById('listView');
        const viewGridBtn = document.getElementById('viewGrid');
        const viewListBtn = document.getElementById('viewList');
        
        if (viewGridBtn && viewListBtn) {
            viewGridBtn.addEventListener('click', function() {
                gridView.classList.remove('d-none');
                listView.classList.add('d-none');
                viewGridBtn.classList.add('active');
                viewListBtn.classList.remove('active');
                localStorage.setItem('eventViewPreference', 'grid');
            });
            
            viewListBtn.addEventListener('click', function() {
                gridView.classList.add('d-none');
                listView.classList.remove('d-none');
                viewGridBtn.classList.remove('active');
                viewListBtn.classList.add('active');
                localStorage.setItem('eventViewPreference', 'list');
            });
            
            // Check for saved preference
            const savedView = localStorage.getItem('eventViewPreference');
            if (savedView === 'list') {
                viewListBtn.click();
            }
        }
        
        // Sticky quick actions bar
        const quickActionsCard = document.querySelector('.quick-actions-card');
        if (quickActionsCard) {
            const sticky = quickActionsCard.offsetTop;
            
            window.onscroll = function() {
                if (window.pageYOffset > sticky) {
                    quickActionsCard.classList.add('sticky-top');
                } else {
                    quickActionsCard.classList.remove('sticky-top');
                }
            };
        }
    });
</script>
@endsection


