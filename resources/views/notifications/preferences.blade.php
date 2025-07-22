@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-bell me-2"></i> Préférences de notification</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('notifications.preferences.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Catégories à suivre</label>
                            <select name="categories[]" class="form-select" multiple>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ isset($preference) && in_array($category->id, (array)($preference->categories ?? [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Ctrl+clic pour sélectionner plusieurs catégories</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Types d'alertes</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="types[]" value="event" id="type_event"
                                    {{ isset($preference) && in_array('event', (array)($preference->types ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_event">Nouveaux événements</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="types[]" value="comment" id="type_comment"
                                    {{ isset($preference) && in_array('comment', (array)($preference->types ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_comment">Nouveaux commentaires</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="types[]" value="like" id="type_like"
                                    {{ isset($preference) && in_array('like', (array)($preference->types ?? [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="type_like">Nouveaux likes</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 