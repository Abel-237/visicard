@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>Créer un événement</h2>
    <form method="POST" action="{{ route('events.store') }}">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Titre</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Date et heure</label>
            <input type="datetime-local" class="form-control" id="start_date" name="start_date" required>
        </div>
        <div class="mb-3">
            <label for="location" class="form-label">Lieu</label>
            <input type="text" class="form-control" id="location" name="location" required>
        </div>
        <div class="mb-3">
            <label for="capacity" class="form-label">Nombre de places</label>
            <input type="number" class="form-control" id="capacity" name="capacity" min="1" required>
        </div>
        <button type="submit" class="btn btn-primary">Créer</button>
    </form>
</div>
@endsection
