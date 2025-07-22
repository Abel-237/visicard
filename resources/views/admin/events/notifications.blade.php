@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Notifications - {{ $event->title }}</h1>
        <div>
            <a href="{{ route('admin.events.dashboard', $event->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Historique des notifications -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Historique des notifications</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Destinataires</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notifications as $notification)
                                    <tr>
                                        <td>{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $notification->type === 'email' ? 'primary' : 'info' }}">
                                                {{ $notification->type }}
                                            </span>
                                        </td>
                                        <td>{{ $notification->recipients_count }} participants</td>
                                        <td>
                                            <span class="badge bg-{{ $notification->status === 'sent' ? 'success' : 
                                                                   ($notification->status === 'failed' ? 'danger' : 'warning') }}">
                                                {{ $notification->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewNotificationModal{{ $notification->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal de détails de la notification -->
                                    <div class="modal fade" id="viewNotificationModal{{ $notification->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Détails de la notification</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <h6>Sujet</h6>
                                                        <p>{{ $notification->subject }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Message</h6>
                                                        <p>{{ $notification->content }}</p>
                                                    </div>
                                                    <div class="mb-3">
                                                        <h6>Destinataires</h6>
                                                        <ul class="list-group">
                                                            @foreach($notification->recipients as $recipient)
                                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                    {{ $recipient->name }}
                                                                    <span class="badge bg-{{ $recipient->pivot->status === 'sent' ? 'success' : 
                                                                                            ($recipient->pivot->status === 'failed' ? 'danger' : 'warning') }}">
                                                                        {{ $recipient->pivot->status }}
                                                                    </span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        Fermer
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune notification envoyée</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $notifications->links() }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Nouvelle notification -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Envoyer une notification</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.events.notifications.send', $event->id) }}" method="POST">
                        @csrf

                        <!-- Type de notification -->
                        <div class="mb-3">
                            <label class="form-label">Type de notification</label>
                            <select class="form-select" name="type" required>
                                <option value="email">Email</option>
                                <option value="push">Notification push</option>
                            </select>
                        </div>

                        <!-- Destinataires -->
                        <div class="mb-3">
                            <label class="form-label">Destinataires</label>
                            <select class="form-select" name="recipients" required>
                                <option value="all">Tous les participants</option>
                                <option value="approved">Participants approuvés uniquement</option>
                                <option value="pending">Participants en attente uniquement</option>
                                <option value="custom">Sélection personnalisée</option>
                            </select>
                        </div>

                        <!-- Sujet -->
                        <div class="mb-3">
                            <label class="form-label">Sujet</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="subject" 
                                   required
                                   placeholder="Sujet de la notification">
                        </div>

                        <!-- Message -->
                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea class="form-control" 
                                      name="content" 
                                      rows="5" 
                                      required
                                      placeholder="Contenu de la notification"></textarea>
                        </div>

                        <!-- Options -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="schedule" 
                                       id="scheduleNotification">
                                <label class="form-check-label" for="scheduleNotification">
                                    Planifier l'envoi
                                </label>
                            </div>
                        </div>

                        <!-- Date de planification (initialement caché) -->
                        <div class="mb-3" id="scheduleDateContainer" style="display: none;">
                            <label class="form-label">Date d'envoi</label>
                            <input type="datetime-local" 
                                   class="form-control" 
                                   name="scheduled_at">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-paper-plane"></i> Envoyer la notification
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const scheduleCheckbox = document.getElementById('scheduleNotification');
    const scheduleDateContainer = document.getElementById('scheduleDateContainer');

    scheduleCheckbox.addEventListener('change', function() {
        scheduleDateContainer.style.display = this.checked ? 'block' : 'none';
    });
});
</script>
@endpush 