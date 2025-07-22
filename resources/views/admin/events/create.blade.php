@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-plus-circle me-2"></i> Créer un nouvel événement</h4>
                    <div>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
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

                    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="content" class="form-label">Contenu <span class="text-danger">*</span></label>
                                            <textarea class="form-control" id="content" name="content" rows="10" required>{{ old('content') }}</textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label for="excerpt" class="form-label">Résumé</label>
                                            <textarea class="form-control" id="excerpt" name="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">Sélectionner une catégorie</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <label for="event_date" class="form-label">Date de l'événement <span class="text-danger">*</span></label>
                                            <input type="datetime-local" class="form-control" id="event_date" name="event_date" value="{{ old('event_date') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="location" class="form-label">Lieu <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="status" class="form-label">Statut</label>
                                            <select class="form-select" id="status" name="status" required>
                                                <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié</option>
                                            </select>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="featured" 
                                                       name="featured" 
                                                       value="1" 
                                                       {{ old('featured', false) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="featured">
                                                    Mettre en avant
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image</label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Tags</label>
                                            <div class="tags-container p-3 border rounded bg-light">
                                                <div class="row g-2">
                                                    @foreach($tags as $tag)
                                                        <div class="col-6">
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="checkbox" 
                                                                       name="tags[]" 
                                                                       value="{{ $tag->id }}" 
                                                                       id="tag_{{ $tag->id }}"
                                                                       {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
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
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-success" id="submitBtn">
                                                <i class="fas fa-save"></i> <span id="submitText">Enregistrer</span>
                                            </button>
                                            <button type="button" class="btn btn-primary" id="saveDraftBtn">
                                                <i class="fas fa-save"></i> Enregistrer comme brouillon
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
    
    submitBtn.disabled = true;
    submitText.textContent = 'Enregistrement en cours...';
    submitBtn.classList.remove('btn-success');
    submitBtn.classList.add('btn-secondary');
});

document.getElementById('saveDraftBtn').addEventListener('click', function() {
    document.querySelector('select[name="status"]').value = 'draft';
    document.getElementById('eventForm').submit();
});
</script>
@endpush
@endsection 