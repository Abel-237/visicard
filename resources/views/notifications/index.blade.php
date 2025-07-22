@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-bell me-2"></i>Mes notifications
                        <span class="badge bg-danger ms-2" id="notificationCount">0</span>
                    </h4>
                    <div>
                        <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-check-double me-1"></i>Tout marquer comme lu
                            </button>
                        </form>
                        <form action="{{ route('notifications.clearAll') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Voulez-vous vraiment supprimer toutes vos notifications?')">
                                <i class="fas fa-trash me-1"></i>Supprimer tout
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        </div>
                    @endif
                    
                    @if($notifications->isEmpty())
                        <div class="text-center p-4">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <p class="lead">Vous n'avez aucune notification.</p>
                        </div>
                    @else
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <div class="list-group-item list-group-item-action notification-item {{ $notification->is_read ? 'notification-read' : 'notification-unread' }}" 
                                     data-notification-id="{{ $notification->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <h6 class="mb-0 me-2">
                                                    @if($notification->type === 'message')
                                                        <i class="fas fa-envelope text-primary me-2"></i>
                                                    @elseif($notification->type === 'event')
                                                        <i class="fas fa-calendar text-success me-2"></i>
                                                    @elseif($notification->type === 'reminder')
                                                        <i class="fas fa-clock text-warning me-2"></i>
                                                    @else
                                                        <i class="fas fa-bell text-info me-2"></i>
                                                    @endif
                                                    {{ $notification->title }}
                                                </h6>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-danger">Nouveau</span>
                                                @endif
                                    </div>
                                            <p class="mb-2 text-muted">{{ $notification->message }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div class="d-flex flex-column align-items-end">
                                            <div class="btn-group-vertical btn-group-sm">
                                                @if($notification->type === 'message' && $notification->sender_id)
                                                    <a href="{{ route('messages.show', $notification->sender_id) }}" 
                                                       class="btn btn-outline-primary btn-sm mb-1" 
                                                       title="Répondre au message">
                                                        <i class="fas fa-reply"></i>
                                                    </a>
                                                @endif
                                                @if($notification->event)
                                                    <a href="{{ route('events.show', $notification->event->slug) }}" 
                                                       class="btn btn-outline-success btn-sm mb-1" 
                                                       title="Voir l'événement">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endif
                                            @if(!$notification->is_read)
                                                <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                        <button type="submit" class="btn btn-outline-secondary btn-sm mb-1" title="Marquer comme lu">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                </form>
                                            @endif
                                                <a href="{{ route('messages.index') }}" 
                                                   class="btn btn-outline-info btn-sm mb-1" 
                                                   title="Aller aux messages">
                                                    <i class="fas fa-envelope"></i>
                                                </a>
                                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <div class="form-check">
                        <form action="{{ route('notifications.updatePreferences') }}" method="POST">
                            @csrf
                            <div class="d-flex align-items-center">
                                <input type="checkbox" class="form-check-input" id="notification_preferences" name="notification_preferences" value="1" {{ Auth::user()->notification_preferences ? 'checked' : '' }} onChange="this.form.submit()">
                                <label class="form-check-label ms-2" for="notification_preferences">
                                    <i class="fas fa-envelope me-1"></i>Recevoir des notifications par email pour les nouveaux événements
                                </label>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.notification-unread {
    background-color: #f8f9fa;
    border-left: 4px solid #007bff;
    font-weight: 500;
}

.notification-read {
    background-color: #ffffff;
    border-left: 4px solid #e9ecef;
    opacity: 0.8;
}

.notification-item {
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
    margin-bottom: 8px;
    border-radius: 8px;
}

.notification-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.notification-item .btn-group-vertical .btn {
    border-radius: 4px;
    margin-bottom: 2px;
}

.notification-item .btn-group-vertical .btn:last-child {
    margin-bottom: 0;
}

.badge {
    font-size: 0.75em;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Marquer automatiquement toutes les notifications comme lues quand on entre dans la page
    markAllNotificationsAsRead();
    
    // Mettre à jour le compteur de notifications
    updateNotificationCount();
});

function markAllNotificationsAsRead() {
    // Marquer toutes les notifications non lues comme lues
    const unreadNotifications = document.querySelectorAll('.notification-unread');
    unreadNotifications.forEach(notification => {
        const notificationId = notification.dataset.notificationId;
        if (notificationId) {
            fetch(`/notifications/mark-as-read/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            }).then(response => {
                if (response.ok) {
                    // Changer l'apparence de la notification
                    notification.classList.remove('notification-unread');
                    notification.classList.add('notification-read');
                    
                    // Supprimer le badge "Nouveau"
                    const badge = notification.querySelector('.badge');
                    if (badge) {
                        badge.remove();
                    }
                    
                    // Masquer le bouton "Marquer comme lu"
                    const markAsReadBtn = notification.querySelector('form[action*="mark-as-read"]');
                    if (markAsReadBtn) {
                        markAsReadBtn.style.display = 'none';
                    }
                }
            });
        }
    });
}

function updateNotificationCount() {
    const unreadCount = document.querySelectorAll('.notification-unread').length;
    const countElement = document.getElementById('notificationCount');
    if (countElement) {
        countElement.textContent = unreadCount;
        countElement.style.display = unreadCount > 0 ? 'inline-block' : 'none';
    }
}
</script>
@endsection 