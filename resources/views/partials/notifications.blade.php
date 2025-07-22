<!-- Dropdown Notifications Content -->
<div class="dropdown-menu dropdown-menu-end notifications-dropdown" aria-labelledby="notificationsDropdown">
    <div class="dropdown-header d-flex justify-content-between align-items-center">
        <span>Notifications</span>
        <div>
            <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none me-2">Tout marquer comme lu</button>
            </form>
            <a href="{{ route('notifications.index') }}" class="text-decoration-none">Voir tout</a>
        </div>
    </div>
    <div class="dropdown-divider"></div>
    <div class="notifications-container" style="max-height: 300px; overflow-y: auto;">
        <div id="notifications-content">
            <!-- Notifications will be loaded here -->
            <div class="text-center p-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
        </div>
    </div>
</div> 