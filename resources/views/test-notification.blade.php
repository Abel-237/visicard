@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4>Test des Notifications</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('notifications.test') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre</label>
                            <input type="text" class="form-control" id="title" name="title" value="Nouveau message" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required>Vous avez reçu un nouveau message de Jean Dupont</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="message">Message</option>
                                <option value="event">Événement</option>
                                <option value="reminder">Rappel</option>
                                <option value="system">Système</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Envoyer Notification</button>
                    </form>
                    
                    <hr>
                    
                    <div class="mt-3">
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-bell me-2"></i>Voir mes notifications
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 