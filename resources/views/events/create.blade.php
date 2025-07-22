@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
        <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-gradient-primary text-white py-5 position-relative">
                    <h1 class="display-4 fw-bold text-center mb-0" style="letter-spacing: 1px; text-shadow: 0 2px 8px rgba(0,0,0,0.12);">
                        <i class="fas fa-calendar-plus fa-fw me-2"></i>
                        Créer un événement
                    </h1>
                    <div class="position-absolute top-0 end-0 m-3 d-none d-md-block" style="opacity:0.08; font-size:7rem; pointer-events:none;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>

                <div class="card-body p-5">
                        @if($errors->any())
                        <div class="alert alert-danger animate__animated animate__shake">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm" class="needs-validation" novalidate>
                            @csrf
                        
                        <!-- Section Informations principales -->
                        <div class="section-wrapper mb-4">
                            <h5 class="section-title border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle text-primary me-2"></i>
                                Informations principales
                            </h5>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-floating">
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title') }}" 
                                               placeholder="Titre de l'événement" required>
                                        <label for="title">
                                            <i class="fas fa-heading text-muted me-1"></i>
                                            Titre de l'événement
                                        </label>
                                        <div class="invalid-feedback">Le titre est requis</div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control @error('content') is-invalid @enderror" 
                                                  id="content" name="content" style="height: 150px" 
                                                  placeholder="Description" required>{{ old('content') }}</textarea>
                                        <label for="content">
                                            <i class="fas fa-align-left text-muted me-1"></i>
                                            Description détaillée
                                        </label>
                                        <div class="invalid-feedback">La description est requise</div>
                                    </div>
                                </div>
                            </div>
                            </div>

                        <!-- Section Date et Catégorie -->
                        <div class="section-wrapper mb-4">
                            <h5 class="section-title border-bottom pb-2 mb-3">
                                <i class="fas fa-clock text-primary me-2"></i>
                                Date et catégorie
                            </h5>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="datetime-local" class="form-control @error('event_date') is-invalid @enderror" 
                                               id="event_date" name="event_date" value="{{ old('event_date') }}">
                                        <label for="event_date">
                                            <i class="fas fa-calendar-alt text-muted me-1"></i>
                                            Date et heure de l'événement
                                        </label>
                            </div>
                            </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                                id="category_id" name="category_id" required>
                                            <option value="">Sélectionner...</option>
                                    @foreach($categories as $category)
                                                <option value="{{ $category->id }}" 
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                    @endforeach
                                </select>
                                        <label for="category_id">
                                            <i class="fas fa-folder text-muted me-1"></i>
                                            Catégorie
                                        </label>
                                        <div class="invalid-feedback">Veuillez sélectionner une catégorie</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="datetime-local" class="form-control" 
                                               id="published_at" name="published_at" value="{{ old('published_at') }}">
                                        <label for="published_at">
                                            <i class="fas fa-clock text-muted me-1"></i>
                                            Date de publication
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Média -->
                        <div class="section-wrapper mb-4">
                            <h5 class="section-title border-bottom pb-2 mb-3">
                                <i class="fas fa-images text-primary me-2"></i>
                                Médias
                            </h5>

                            <div class="media-upload-wrapper p-3 bg-light rounded">
                                <div class="dz-default dz-message">
                                    <input type="file" class="form-control" id="media" name="media[]" 
                                           accept="image/*,video/*" multiple style="display: none;">
                                    <div class="text-center py-4" onclick="document.getElementById('media').click()">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                        <p class="mb-0">Glissez vos fichiers ici ou cliquez pour sélectionner</p>
                                        <small class="text-muted">Images et vidéos acceptées</small>
                                    </div>
                                </div>
                                <div id="preview" class="row g-2 mt-2"></div>
                            </div>
                            </div>

                            <input type="hidden" name="status" value="published">

                        <!-- Boutons d'action -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>
                                Retour
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-primary" id="saveBtn">
                                    <i class="fas fa-save me-1"></i>
                                    Brouillon
                                    </button>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-paper-plane me-1"></i>
                                    <span id="submitText">Publier</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
    }
    .card-header {
        border-top-left-radius: 2rem !important;
        border-top-right-radius: 2rem !important;
    }
    .card {
        border-radius: 2rem !important;
    }
    .display-4 {
        font-size: 2.8rem;
        font-weight: 700;
        letter-spacing: 1px;
    }
    @media (min-width: 992px) {
        .card-body {
            padding-left: 6rem !important;
            padding-right: 6rem !important;
        }
    }
    @media (max-width: 767px) {
        .display-4 {
            font-size: 2rem;
        }
        .card-header {
            padding: 2rem 1rem !important;
        }
    }
    .container-fluid {
        max-width: 100vw;
        padding-left: 0;
        padding-right: 0;
    }
    .col-12 {
        padding-left: 0;
        padding-right: 0;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.getElementById('eventForm');
    form.addEventListener('submit', function(e) {
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        form.classList.add('was-validated');
        
        if (form.checkValidity()) {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    submitBtn.disabled = true;
            submitText.textContent = 'Publication en cours...';
            submitBtn.classList.add('disabled');
        }
});

    // Gestion du brouillon
document.getElementById('saveBtn').addEventListener('click', function() {
    document.querySelector('input[name="status"]').value = 'draft';
        form.submit();
    });

    // Prévisualisation des médias
    document.getElementById('media').addEventListener('change', function(e) {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';
        
        [...e.target.files].forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'col-4 col-md-3';
                
                if (file.type.startsWith('image/')) {
                    div.innerHTML = `
                        <div class="card">
                            <img src="${e.target.result}" class="card-img-top" alt="${file.name}" style="height: 120px; object-fit: cover;">
                        </div>
                    `;
                } else if (file.type.startsWith('video/')) {
                    div.innerHTML = `
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-video fa-2x text-muted"></i>
                                <p class="small mb-0 mt-2">${file.name}</p>
                            </div>
                        </div>
                    `;
                }
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    });
});
</script>
@endpush
@endsection 