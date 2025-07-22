@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-edit me-2"></i> Modifier l'événement</h4>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('events.update', $event->slug) }}" method="POST" enctype="multipart/form-data" id="eventForm">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $event->title) }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="excerpt" class="form-label">Extrait</label>
                                <textarea class="form-control" id="excerpt" name="excerpt" rows="2" maxlength="500">{{ old('excerpt', $event->excerpt) }}</textarea>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Résumé court de l'événement (max 500 caractères)
                                </small>
                            </div>
                            <div class="mb-3">
                                <label for="content" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="content" name="content" rows="5" required>{{ old('content', $event->content) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Date et heure de l'événement</label>
                                <input type="datetime-local" class="form-control" id="event_date" name="event_date" 
                                       value="{{ old('event_date', $event->event_date ? $event->event_date->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="mb-3">
                                <label for="location" class="form-label">Emplacement</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="{{ old('location', $event->location) }}" placeholder="Adresse ou lieu de l'événement">
                            </div>
                            <div class="mb-3">
                                <label for="published_at" class="form-label">Date de publication</label>
                                <input type="datetime-local" class="form-control" id="published_at" name="published_at" 
                                       value="{{ old('published_at', $event->published_at ? $event->published_at->format('Y-m-d\TH:i') : '') }}">
                            </div>
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="tags" class="form-label">Tags</label>
                                <div class="tags-container p-3 border rounded bg-light">
                                    <div class="row g-2">
                                        @foreach($tags as $tag)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           name="tags[]" 
                                                           value="{{ $tag->id }}" 
                                                           id="tag_{{ $tag->id }}"
                                                           {{ (collect(old('tags', $event->tags->pluck('id')))->contains($tag->id)) ? 'checked' : '' }}>
                                                    <label class="form-check-label badge bg-light text-dark border" 
                                                           for="tag_{{ $tag->id }}"
                                                           style="cursor: pointer; padding: 8px 12px; font-size: 0.9rem;">
                                                        <i class="fas fa-tag me-1"></i>{{ $tag->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>Sélectionnez un ou plusieurs tags pour catégoriser votre événement
                                </small>
                            </div>

                            <!-- Affichage des médias existants -->
                            @if($event->media && $event->media->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label">Médias existants</label>
                                    <div class="row g-2">
                                        @foreach($event->media as $media)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="position-relative">
                                                    @if($media->file_type === 'image')
                                                        <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                             alt="Média {{ $loop->iteration }}" 
                                                             class="img-thumbnail" 
                                                             style="width: 100%; height: 150px; object-fit: cover;">
                                                    @else
                                                        <div class="bg-secondary d-flex align-items-center justify-content-center" 
                                                             style="width: 100%; height: 150px;">
                                                            <i class="fas fa-video text-white fa-2x"></i>
                                                        </div>
                                                    @endif
                                                    <div class="form-check position-absolute top-0 start-0 m-2">
                                                        <input class="form-check-input" 
                                                               type="checkbox" 
                                                               name="delete_media[]" 
                                                               value="{{ $media->id }}" 
                                                               id="delete_media_{{ $media->id }}">
                                                        <label class="form-check-label text-white bg-danger rounded px-1" 
                                                               for="delete_media_{{ $media->id }}"
                                                               style="font-size: 0.7rem; cursor: pointer;">
                                                            <i class="fas fa-trash"></i>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Cochez les cases pour supprimer les médias
                                    </small>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="media" class="form-label">Ajouter des images ou vidéos</label>
                                <input type="file" class="form-control" id="media" name="media[]" accept="image/*,video/*" multiple>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Vous pouvez sélectionner plusieurs fichiers (max 10MB chacun)
                                </small>
                            </div>

                            <input type="hidden" name="status" value="{{ old('status', $event->status) }}">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('events.show', $event->slug) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2" id="submitBtn">
                                        <i class="fas fa-save"></i> <span id="submitText">Mettre à jour</span>
                                    </button>
                                    <button type="button" class="btn btn-success" id="saveBtn">
                                        <i class="fas fa-save"></i> Enregistrer comme brouillon
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
document.getElementById('eventForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const submitText = document.getElementById('submitText');
    
    // Désactiver le bouton et changer le texte
    submitBtn.disabled = true;
    submitText.textContent = 'Mise à jour en cours...';
    submitBtn.classList.remove('btn-primary');
    submitBtn.classList.add('btn-secondary');
});

document.getElementById('saveBtn').addEventListener('click', function() {
    // Changer le statut en "draft" avant de soumettre
    document.querySelector('input[name="status"]').value = 'draft';
    // Soumettre le formulaire
    document.getElementById('eventForm').submit();
});

// Aperçu des fichiers sélectionnés
document.getElementById('media').addEventListener('change', function(e) {
    const files = e.target.files;
    const container = document.getElementById('mediaPreview');
    
    if (!container) {
        const previewDiv = document.createElement('div');
        previewDiv.id = 'mediaPreview';
        previewDiv.className = 'mt-2';
        e.target.parentNode.appendChild(previewDiv);
    }
    
    const previewDiv = document.getElementById('mediaPreview');
    previewDiv.innerHTML = '';
    
    if (files.length > 0) {
        previewDiv.innerHTML = '<h6 class="mt-2">Nouveaux fichiers sélectionnés :</h6>';
        
        Array.from(files).forEach((file, index) => {
            const fileDiv = document.createElement('div');
            fileDiv.className = 'alert alert-info py-2 mb-2';
            fileDiv.innerHTML = `
                <i class="fas fa-file me-2"></i>
                <strong>${file.name}</strong> (${(file.size / 1024 / 1024).toFixed(2)} MB)
            `;
            previewDiv.appendChild(fileDiv);
        });
    }
});
</script>
@endpush
@endsection 