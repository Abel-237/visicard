<nav class="col-md-2 d-none d-md-block bg-dark sidebar min-vh-100">
    <div class="sidebar-sticky">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Tableau de bord
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i> Utilisateurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.business-cards.*') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.business-cards.index') }}">
                    <i class="fas fa-id-card"></i> Cartes de visite
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.events.*') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.events.index') }}">
                    <i class="fas fa-calendar-alt"></i> Événements
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.reports.index') }}">
                    <i class="fas fa-flag"></i> Signalements
                    @if($pendingReportsCount = \App\Models\Report::where('status', 'pending')->count())
                        <span class="badge badge-danger">{{ $pendingReportsCount }}</span>
                    @endif
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }} text-white" 
                   href="{{ route('admin.settings.index') }}">
                    <i class="fas fa-cog"></i> Paramètres
                </a>
            </li>
        </ul>

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Rapports</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('admin.reports.users') }}">
                    <i class="fas fa-chart-line"></i> Utilisateurs
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('admin.reports.exchanges') }}">
                    <i class="fas fa-exchange-alt"></i> Échanges
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="{{ route('admin.reports.events') }}">
                    <i class="fas fa-calendar-check"></i> Événements
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar-sticky {
    position: relative;
    top: 0;
    height: calc(100vh - 48px);
    padding-top: .5rem;
    overflow-x: hidden;
    overflow-y: auto;
}

.nav-link {
    padding: 1rem;
    transition: all 0.3s;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
}

.sidebar-heading {
    font-size: .75rem;
    text-transform: uppercase;
}

.badge {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}
</style> 