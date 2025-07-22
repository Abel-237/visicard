@extends('layouts.app')

@section('content')
<div class="container-fluid py-5">
    <!-- En-tête avec statistiques -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="bg-gradient-primary text-white p-4 rounded-3 shadow">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="display-5 fw-bold mb-2">
                            <i class="fas fa-address-book me-3"></i>
                            Annuaire Professionnel
                        </h1>
                        <p class="lead mb-0">Découvrez et connectez-vous avec des professionnels de votre secteur</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="bg-white bg-opacity-25 rounded p-3">
                                    <h3 class="mb-0 fw-bold">{{ $totalCards }}</h3>
                                    <small>Professionnels</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white bg-opacity-25 rounded p-3">
                                    <h3 class="mb-0 fw-bold">{{ $totalIndustries }}</h3>
                                    <small>Secteurs</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white bg-opacity-25 rounded p-3">
                                    <h3 class="mb-0 fw-bold">{{ $totalCompanies }}</h3>
                                    <small>Entreprises</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('business-cards.directory') }}" method="GET" id="searchForm">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">
                                    <i class="fas fa-search me-2"></i>Recherche
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="search" 
                                       name="search" 
                                       placeholder="Nom, entreprise, poste..." 
                                       value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="industry" class="form-label">
                                    <i class="fas fa-industry me-2"></i>Secteur d'activité
                                </label>
                                <select class="form-select form-select-lg" id="industry" name="industry">
                                    <option value="">Tous les secteurs</option>
                                    @foreach($industries as $industry)
                                        <option value="{{ $industry }}" {{ request('industry') == $industry ? 'selected' : '' }}>
                                            {{ $industry }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="company" class="form-label">
                                    <i class="fas fa-building me-2"></i>Entreprise
                                </label>
                                <select class="form-select form-select-lg" id="company" name="company">
                                    <option value="">Toutes les entreprises</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company }}" {{ request('company') == $company ? 'selected' : '' }}>
                                            {{ $company }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i>Filtrer
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Filtres avancés -->
                        <div class="row mt-3" id="advancedFilters" style="display: none;">
                            <div class="col-md-3">
                                <label for="position" class="form-label">Poste</label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       placeholder="Développeur, Manager..." value="{{ request('position') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="location" class="form-label">Localisation</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       placeholder="Ville, région..." value="{{ request('location') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="experience" class="form-label">Expérience</label>
                                <select class="form-select" id="experience" name="experience">
                                    <option value="">Tous niveaux</option>
                                    <option value="junior" {{ request('experience') == 'junior' ? 'selected' : '' }}>Junior (0-2 ans)</option>
                                    <option value="intermediate" {{ request('experience') == 'intermediate' ? 'selected' : '' }}>Intermédiaire (3-5 ans)</option>
                                    <option value="senior" {{ request('experience') == 'senior' ? 'selected' : '' }}>Senior (5+ ans)</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid">
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                        <i class="fas fa-times me-2"></i>Effacer
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-link" onclick="toggleAdvancedFilters()">
                                <i class="fas fa-filter me-2"></i>
                                <span id="filterText">Afficher les filtres avancés</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Résultats -->
    <div class="row">
        <div class="col-12">
            <!-- En-tête des résultats -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-users me-2"></i>
                        {{ $businessCards->total() }} professionnel(s) trouvé(s)
                    </h4>
                    @if(request('search') || request('industry') || request('company'))
                        <small class="text-muted">
                            Filtres actifs: 
                            @if(request('search')) <span class="badge bg-primary me-1">{{ request('search') }}</span> @endif
                            @if(request('industry')) <span class="badge bg-info me-1">{{ request('industry') }}</span> @endif
                            @if(request('company')) <span class="badge bg-success me-1">{{ request('company') }}</span> @endif
                        </small>
                    @endif
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="setViewMode('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="setViewMode('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    <select class="form-select" style="width: auto;" onchange="setSort(this.value)">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Par nom</option>
                        <option value="company" {{ request('sort') == 'company' ? 'selected' : '' }}>Par entreprise</option>
                        <option value="industry" {{ request('sort') == 'industry' ? 'selected' : '' }}>Par secteur</option>
                    </select>
                </div>
            </div>

            <!-- Grille des cartes de visite -->
            <div class="row" id="cardsGrid">
                @forelse($businessCards as $card)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm hover-shadow border-0">
                            <div class="card-body p-4">
                                <!-- En-tête avec photo et infos principales -->
                                <div class="text-center mb-4">
                                    <div class="position-relative d-inline-block">
                                        @if($card->logo)
                                            <img src="{{ asset('storage/' . $card->logo) }}" 
                                                 alt="{{ $card->name }}" 
                                                 class="rounded-circle img-thumbnail mb-3" 
                                                 style="width: 120px; height: 120px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-gradient-primary d-flex align-items-center justify-content-center mx-auto mb-3" 
                                                 style="width: 120px; height: 120px;">
                                                <i class="fas fa-user text-white fa-3x"></i>
                                            </div>
                                        @endif
                                        <div class="position-absolute top-0 end-0">
                                            <span class="badge bg-primary">{{ $card->industry }}</span>
                                        </div>
                                    </div>
                                    <h5 class="card-title mb-1 fw-bold">{{ $card->name }}</h5>
                                    <p class="text-muted mb-2">{{ $card->position }}</p>
                                    <h6 class="text-primary mb-3">{{ $card->company }}</h6>
                                </div>

                                <!-- Informations de contact -->
                                <div class="contact-info mb-4">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-envelope text-primary me-3" style="width: 20px;"></i>
                                        <a href="mailto:{{ $card->email }}" class="text-decoration-none text-dark">
                                            {{ Str::limit($card->email, 25) }}
                                        </a>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="fas fa-phone text-primary me-3" style="width: 20px;"></i>
                                        <a href="tel:{{ $card->phone }}" class="text-decoration-none text-dark">
                                            {{ $card->phone }}
                                        </a>
                                    </div>
                                    @if($card->website)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-globe text-primary me-3" style="width: 20px;"></i>
                                            <a href="{{ $card->website }}" target="_blank" class="text-decoration-none text-dark">
                                                {{ Str::limit($card->website, 25) }}
                                            </a>
                                        </div>
                                    @endif
                                    @if($card->address)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-map-marker-alt text-primary me-3" style="width: 20px;"></i>
                                            <span class="text-muted">{{ Str::limit($card->address, 30) }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Bio -->
                                @if($card->bio)
                                    <div class="mb-4">
                                        <p class="card-text text-muted small">
                                            <i class="fas fa-quote-left me-2"></i>
                                            {{ Str::limit($card->bio, 120) }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Réseaux sociaux -->
                                @if($card->social_media)
                                    @php
                                        $socialMedia = is_string($card->social_media) ? json_decode($card->social_media, true) : $card->social_media;
                                    @endphp
                                    @if(!empty($socialMedia))
                                        <div class="mb-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                @foreach($socialMedia as $platform => $url)
                                                    @if($url)
                                                        <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                                            <i class="fab fa-{{ $platform }}"></i>
                                                        </a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Actions -->
                                <div class="text-center">
                                    <a href="{{ route('business-cards.show', $card) }}" class="btn btn-primary btn-sm me-2">
                                        <i class="fas fa-eye me-1"></i>Voir profil
                                    </a>
                                    @if(auth()->check() && auth()->id() === $card->user_id)
                                        <a href="{{ route('business-cards.edit', $card) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-edit me-1"></i>Modifier
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-search fa-4x text-muted mb-4"></i>
                            <h4 class="text-muted mb-3">Aucun professionnel trouvé</h4>
                            <p class="text-muted mb-4">
                                Aucune carte de visite ne correspond à vos critères de recherche.
                            </p>
                            <button type="button" class="btn btn-primary" onclick="clearFilters()">
                                <i class="fas fa-times me-2"></i>Effacer les filtres
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($businessCards->hasPages())
                <div class="row mt-5">
                    <div class="col-12">
                        <nav aria-label="Pagination des résultats">
                            {{ $businessCards->appends(request()->query())->links() }}
                        </nav>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .hover-shadow {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0,0,0,.175)!important;
    }
    
    .card {
        border-radius: 15px;
        overflow: hidden;
    }
    
    .img-thumbnail {
        border: 3px solid #fff;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
    }
    
    .badge {
        font-size: 0.8em;
        padding: 0.5em 1em;
    }
    
    .contact-info a:hover {
        color: var(--bs-primary) !important;
    }
    
    .btn-group .btn.active {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: white;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleAdvancedFilters() {
        const filters = document.getElementById('advancedFilters');
        const filterText = document.getElementById('filterText');
        
        if (filters.style.display === 'none') {
            filters.style.display = 'block';
            filterText.innerHTML = '<i class="fas fa-filter me-2"></i>Masquer les filtres avancés';
        } else {
            filters.style.display = 'none';
            filterText.innerHTML = '<i class="fas fa-filter me-2"></i>Afficher les filtres avancés';
        }
    }
    
    function clearFilters() {
        document.getElementById('searchForm').reset();
        document.getElementById('searchForm').submit();
    }
    
    function setViewMode(mode) {
        const buttons = document.querySelectorAll('.btn-group .btn');
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Ici vous pouvez ajouter la logique pour changer l'affichage
        // Pour l'instant, on garde la vue grille
    }
    
    function setSort(value) {
        const url = new URL(window.location);
        url.searchParams.set('sort', value);
        window.location = url;
    }
    
    // Auto-submit sur changement de filtre
    document.getElementById('industry').addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });
    
    document.getElementById('company').addEventListener('change', function() {
        document.getElementById('searchForm').submit();
    });
</script>
@endpush
@endsection 