@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Modifier ma carte de visite</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-info">Consultations : {{ $businessCard->views ?? 0 }}</span>
                        </div>
                        <div>
                            @if(isset($qrCode))
                                <div class="mb-2">{!! $qrCode !!}</div>
                            @endif
                            <a href="{{ route('business-cards.vcard', $businessCard) }}" class="btn btn-outline-success btn-sm" target="_blank">
                                <i class="fas fa-id-card"></i> Télécharger vCard
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('business-cards.update', $businessCard) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Informations personnelles -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $businessCard->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Poste *</label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position', $businessCard->position) }}" required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="company" class="form-label">Entreprise *</label>
                                <input type="text" class="form-control @error('company') is-invalid @enderror" 
                                       id="company" name="company" value="{{ old('company', $businessCard->company) }}" required>
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="industry" class="form-label">Secteur d'activité *</label>
                                <select class="form-select @error('industry') is-invalid @enderror" 
                                        id="industry" name="industry" required>
                                    <option value="">Sélectionnez un secteur</option>
                                    <option value="Informatique" {{ old('industry', $businessCard->industry) == 'Informatique' ? 'selected' : '' }}>Informatique</option>
                                    <option value="Marketing" {{ old('industry', $businessCard->industry) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                    <option value="Finance" {{ old('industry', $businessCard->industry) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                    <option value="Santé" {{ old('industry', $businessCard->industry) == 'Santé' ? 'selected' : '' }}>Santé</option>
                                    <option value="Éducation" {{ old('industry', $businessCard->industry) == 'Éducation' ? 'selected' : '' }}>Éducation</option>
                                    <option value="Commerce" {{ old('industry', $businessCard->industry) == 'Commerce' ? 'selected' : '' }}>Commerce</option>
                                    <option value="Autre" {{ old('industry', $businessCard->industry) == 'Autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('industry')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Coordonnées -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email professionnel *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', $businessCard->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Téléphone *</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', $businessCard->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="website" class="form-label">Site web</label>
                                <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                       id="website" name="website" value="{{ old('website', $businessCard->website) }}">
                                @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="address" class="form-label">Adresse</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                       id="address" name="address" value="{{ old('address', $businessCard->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Bio -->
                        <div class="mb-3">
                            <label for="bio" class="form-label">Biographie</label>
                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                      id="bio" name="bio" rows="3">{{ old('bio', $businessCard->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Réseaux sociaux -->
                        <div class="mb-3">
                            <label class="form-label">Réseaux sociaux</label>
                            @php
                                $socialMedia = json_decode($businessCard->social_media, true) ?? [];
                            @endphp
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-linkedin"></i></span>
                                        <input type="url" class="form-control" name="social_media[linkedin]" 
                                               placeholder="URL LinkedIn" value="{{ old('social_media.linkedin', $socialMedia['linkedin'] ?? '') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-twitter"></i></span>
                                        <input type="url" class="form-control" name="social_media[twitter]" 
                                               placeholder="URL Twitter" value="{{ old('social_media.twitter', $socialMedia['twitter'] ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logo -->
                        <div class="mb-4">
                            <label for="logo" class="form-label">Logo</label>
                            @if($businessCard->logo)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $businessCard->logo) }}" 
                                         alt="Logo actuel" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            @endif
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/*">
                            <div class="form-text">Format accepté : JPG, PNG, GIF. Taille max : 2MB</div>
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Visibilité -->
                        <div class="mb-3">
                            <label for="visibility" class="form-label">Visibilité</label>
                            <select class="form-select" id="visibility" name="visibility">
                                <option value="public" {{ old('visibility', $businessCard->visibility) == 'public' ? 'selected' : '' }}>Publique</option>
                                <option value="private" {{ old('visibility', $businessCard->visibility) == 'private' ? 'selected' : '' }}>Privée</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                            <a href="{{ route('business-cards.show', $businessCard) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Prévisualisation du logo
    document.getElementById('logo').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.createElement('img');
                preview.src = e.target.result;
                preview.style.maxWidth = '200px';
                preview.style.marginTop = '10px';
                preview.classList.add('img-thumbnail');
                
                const container = document.getElementById('logo').parentElement;
                const existingPreview = container.querySelector('img:not(.img-thumbnail)');
                if (existingPreview) {
                    container.removeChild(existingPreview);
                }
                container.appendChild(preview);
            }
            reader.readAsDataURL(file);
        }
    });

    // Liens cliquables en modale (pas de redirection)
    document.querySelectorAll('a[data-modal-link]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            const modal = document.createElement('div');
            modal.style.position = 'fixed';
            modal.style.top = 0;
            modal.style.left = 0;
            modal.style.width = '100vw';
            modal.style.height = '100vh';
            modal.style.background = 'rgba(0,0,0,0.5)';
            modal.style.display = 'flex';
            modal.style.alignItems = 'center';
            modal.style.justifyContent = 'center';
            modal.innerHTML = `<div style="background:#fff;padding:2rem;max-width:90vw;max-height:90vh;overflow:auto;position:relative;">
                <button onclick="this.parentNode.parentNode.remove()" style="position:absolute;top:10px;right:10px;" class='btn btn-danger btn-sm'>Fermer</button>
                <iframe src="${url}" style="width:80vw;height:70vh;border:none;"></iframe>
            </div>`;
            document.body.appendChild(modal);
        });
    });
</script>
@endpush
@endsection 