<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('sdgegg.png') }}">
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/notifications.css') }}" rel="stylesheet">
    <style>
        /* Pagination Bootstrap élégante et compacte */
        .pagination {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.15rem;
        }
        .pagination .page-item {
            margin: 0;
        }
        .pagination .page-link {
            padding: 0.25rem 0.6rem;
            font-size: 0.85rem;
            border-radius: 0.3rem;
            border: 1px solid #dee2e6;
            color: #0d6efd;
            background: #fff;
            transition: all 0.2s;
            box-shadow: none;
        }
        .pagination .page-link:hover,
        .pagination .page-link:focus {
            background: #f1f3f5;
            color: #0a58ca;
            border-color: #b6d4fe;
        }
        .pagination .page-item.active .page-link {
            background: #0d6efd;
            color: #fff;
            border-color: #0d6efd;
        }
        .pagination .page-item.disabled .page-link {
            color: #adb5bd;
            background: #fff;
            border-color: #dee2e6;
            pointer-events: none;
        }
        /* Petits écrans */
        @media (max-width: 576px) {
            .pagination .page-link {
                padding: 0.18rem 0.38rem;
                font-size: 0.75rem;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <i class="fas fa-calendar-alt me-2"></i>
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('events.*') ? 'active' : '' }}" href="{{ route('events.index') }}">
                                <i class="fas fa-calendar-week me-1"></i> {{ __('app.events') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('calendar.*') ? 'active' : '' }}" href="{{ route('calendar.index') }}">
                                <i class="fas fa-calendar me-1"></i> {{ __('app.calendar') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('search.*') ? 'active' : '' }}" href="{{ route('search.index') }}">
                                <i class="fas fa-search me-1"></i> {{ __('app.search') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('business-card') ? 'active' : '' }}" href="{{ route('business-card') }}">
                                <i class="fas fa-id-card me-1"></i> {{ __('app.business_cards') }}
                            </a>
                        </li>
                        @auth
                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-cogs me-1"></i> Administration
                                    </a>
                                </li>
                            @endif
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        @guest
                            <!-- Sélecteur de langue pour les invités -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-globe me-1"></i> {{ __('app.language') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item {{ app()->getLocale() == 'fr' ? 'active' : '' }}" href="{{ route('language.switch', 'fr') }}">
                                            <i class="fas fa-flag me-2"></i> {{ __('app.french') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                                            <i class="fas fa-flag me-2"></i> {{ __('app.english') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('app.login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('app.register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('business-cards.index') }}">
                                    <i class="fas fa-address-card me-1"></i>{{ __('app.business_cards') }}
                                </a>
                            </li>


                            <!-- Sélecteur de langue -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-globe me-1"></i> {{ __('app.language') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item {{ app()->getLocale() == 'fr' ? 'active' : '' }}" href="{{ route('language.switch', 'fr') }}">
                                            <i class="fas fa-flag me-2"></i> {{ __('app.french') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item {{ app()->getLocale() == 'en' ? 'active' : '' }}" href="{{ route('language.switch', 'en') }}">
                                            <i class="fas fa-flag me-2"></i> {{ __('app.english') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    <i class="fas fa-user-circle me-1"></i> {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user me-2"></i> Profil
                                    </a>
                                    <a class="dropdown-item position-relative" href="{{ route('notifications.index') }}">
                                        <i class="fas fa-bell me-2"></i> {{ __('app.notifications') }}
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="display: none;">
                                            0
                                        </span>
                                    </a>
                                    <a class="dropdown-item position-relative" href="{{ route('messages.index') }}">
                                        <i class="fas fa-envelope me-2"></i> {{ __('app.messages') }}
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="unreadMessagesCount" style="display: none;">
                                            0
                                        </span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @auth
        <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Get notification count
                fetchNotificationCount();
                
                // Notifications dropdown
                const notificationsDropdown = document.getElementById('notificationsDropdown');
                if (notificationsDropdown) {
                    notificationsDropdown.addEventListener('show.bs.dropdown', function() {
                        fetchNotifications();
                    });
                }
                
                // Setup Pusher
                const pusher = new Pusher('{{ env("PUSHER_APP_KEY") }}', {
                    cluster: '{{ env("PUSHER_APP_CLUSTER") }}',
                    encrypted: true
                });
                
                // Subscribe to private channel
                const channel = pusher.subscribe('notifications.{{ Auth::id() }}');
                
                // Listen for notification events
                channel.bind('new.notification', function(data) {
                    // Increase notification count
                    incrementNotificationCount();
                    
                    // Show browser notification if supported
                    if (Notification.permission === 'granted') {
                        const notification = new Notification(data.notification.title, {
                            body: data.notification.message,
                            icon: '/favicon.ico',
                            tag: 'notification-' + data.notification.id
                        });
                        
                        notification.onclick = function() {
                            window.focus();
                            if (data.notification.type === 'message') {
                                // Rediriger vers les messages si c'est une notification de message
                                window.location.href = '/messages';
                            } else if (data.notification.event) {
                                // Rediriger vers l'événement si c'est une notification d'événement
                                window.location.href = '/events/' + data.notification.event.slug;
                            }
                            notification.close();
                        };
                        
                        // Auto-close notification after 5 seconds
                        setTimeout(() => {
                            notification.close();
                        }, 5000);
                    }
                });
                
                // Subscribe to messages channel
                const messagesChannel = pusher.subscribe('messages.{{ Auth::id() }}');
                
                // Listen for new message events
                messagesChannel.bind('App\\Events\\NewMessage', function(data) {
                    // Update unread messages count
                    updateUnreadMessagesCount(data.unread_count);
                    
                    // Show browser notification if supported
                    if (Notification.permission === 'granted') {
                        const notification = new Notification('Nouveau message de ' + data.message.sender.name, {
                            body: data.message.content,
                            icon: '/favicon.ico',
                            tag: 'message-' + data.message.id
                        });
                        
                        notification.onclick = function() {
                            window.focus();
                            window.location.href = '/messages/' + data.message.sender_id;
                            notification.close();
                        };
                        
                        // Auto-close notification after 5 seconds
                        setTimeout(() => {
                            notification.close();
                        }, 5000);
                    }
                });
                
                // Request notification permission
                if (Notification.permission !== 'granted' && Notification.permission !== 'denied') {
                    Notification.requestPermission();
                }
            });
            
            function fetchNotificationCount() {
                fetch('{{ route("notifications.count") }}')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('notificationCount');
                        const badgeNav = document.getElementById('notificationCountNav');
                        
                        if (badge) {
                            badge.textContent = data.count;
                            badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                        }
                        
                        if (badgeNav) {
                            badgeNav.textContent = data.count;
                            badgeNav.style.display = data.count > 0 ? 'inline-block' : 'none';
                        }
                        
                        // Ajouter un effet de pulsation si il y a des notifications
                        if (data.count > 0) {
                            addNotificationPulse();
                        } else {
                            removeNotificationPulse();
                        }
                    });
            }
            
            function incrementNotificationCount() {
                const badge = document.getElementById('notificationCount');
                const badgeNav = document.getElementById('notificationCountNav');
                
                if (badge) {
                    const currentCount = parseInt(badge.textContent) || 0;
                    badge.textContent = currentCount + 1;
                    badge.style.display = 'inline-block';
                }
                
                if (badgeNav) {
                    const currentCount = parseInt(badgeNav.textContent) || 0;
                    badgeNav.textContent = currentCount + 1;
                    badgeNav.style.display = 'inline-block';
                }
                
                addNotificationPulse();
            }
            
            function addNotificationPulse() {
                const bellIcon = document.querySelector('.fa-bell');
                if (bellIcon) {
                    bellIcon.classList.add('notification-pulse');
                }
            }
            
            function removeNotificationPulse() {
                const bellIcon = document.querySelector('.fa-bell');
                if (bellIcon) {
                    bellIcon.classList.remove('notification-pulse');
                }
            }
            
            function updateUnreadMessagesCount(count) {
                const badge = document.getElementById('unreadMessagesCount');
                const badgeNav = document.getElementById('unreadMessagesCountNav');
                
                if (badge) {
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'inline-block' : 'none';
                }
                
                if (badgeNav) {
                    badgeNav.textContent = count;
                    badgeNav.style.display = count > 0 ? 'inline-block' : 'none';
                }
                
                // Ajouter un effet de pulsation si il y a des messages non lus
                if (count > 0) {
                    addMessagePulse();
                } else {
                    removeMessagePulse();
                }
            }
            
            function addMessagePulse() {
                const envelopeIcon = document.querySelector('.fa-envelope');
                if (envelopeIcon) {
                    envelopeIcon.classList.add('message-pulse');
                }
            }
            
            function removeMessagePulse() {
                const envelopeIcon = document.querySelector('.fa-envelope');
                if (envelopeIcon) {
                    envelopeIcon.classList.remove('message-pulse');
                }
            }
            
            function fetchNotifications() {
                const container = document.getElementById('notifications-content');
                if (container) {
                    container.innerHTML = `
                        <div class="text-center p-3">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                        </div>
                    `;
                    
                    fetch('{{ route("notifications.unread") }}')
                        .then(response => response.json())
                        .then(data => {
                            if (data.length === 0) {
                                container.innerHTML = `
                                    <div class="text-center p-3">
                                        <p class="text-muted mb-0">Aucune notification non lue</p>
                                    </div>
                                `;
                            } else {
                                let html = '';
                                data.forEach(notification => {
                                    html += `
                                        <a href="${notification.event ? '{{ url("events") }}/' + notification.event.slug : '#'}" class="dropdown-item p-3 border-bottom">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">${notification.title}</h6>
                                                <small>${notification.created_at}</small>
                                            </div>
                                            <p class="text-muted mb-0">${notification.message}</p>
                                        </a>
                                    `;
                                });
                                container.innerHTML = html;
                            }
                        });
                }
            }
            
            function incrementNotificationCount() {
                const badge = document.getElementById('notificationCount');
                if (badge) {
                    let count = parseInt(badge.textContent) || 0;
                    count += 1;
                    badge.textContent = count;
                    badge.style.display = 'inline-block';
                }
            }
            
            function updateUnreadMessagesCount(count) {
                const badge = document.getElementById('unreadMessagesCount');
                if (badge) {
                    if (count > 0) {
                        badge.textContent = count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        </script>
    @endauth
    
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour mettre à jour le compteur de messages non lus
        function updateUnreadCount() {
            fetch('/messages/unread/count')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('unreadMessagesCount');
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                });
        }

        // Mettre à jour le compteur toutes les 30 secondes
        updateUnreadCount();
        setInterval(updateUnreadCount, 30000);
    });
    </script>
    @endpush
    
    @yield('scripts')

    @php
        $hideFooter = request()->routeIs('login') || request()->routeIs('register');
    @endphp
    @unless($hideFooter)
    <footer class="footer-main bg-primary text-white mt-5 pt-4 pb-3 shadow-lg position-relative" style="border-top-left-radius: 2.5rem; border-top-right-radius: 2.5rem; box-shadow: 0 -4px 24px rgba(0,0,0,0.08); overflow: hidden;">
        <div class="footer-gradient position-absolute top-0 start-0 w-100 h-100" style="pointer-events:none;"></div>
        <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 position-relative" style="z-index:2;">
            <div class="mb-3 mb-md-0 text-center text-md-start">
                <span class="fw-bold" style="font-size: 1.3rem;"><i class="fas fa-calendar-alt me-2"></i>{{ config('app.name', 'Gestion Événements') }}</span>
                <span class="d-block small mt-1 text-white-50">La plateforme moderne pour gérer vos événements et cartes de visite.</span>
            </div>
            <div class="mb-3 mb-md-0 text-center">
                <a href="{{ route('events.index') }}" class="footer-link me-3">Événements</a>
                <a href="{{ route('calendar.index') }}" class="footer-link me-3">Calendrier</a>
                <a href="{{ route('business-cards.index') }}" class="footer-link me-3">Cartes de visite</a>
                <a href="{{ route('search.index') }}" class="footer-link">Recherche</a>
            </div>
            <div class="text-center text-md-end">
                <a href="#" class="footer-social me-2" title="Twitter"><i class="fab fa-twitter fa-lg"></i></a>
                <a href="#" class="footer-social me-2" title="Facebook"><i class="fab fa-facebook fa-lg"></i></a>
                <a href="#" class="footer-social me-2" title="LinkedIn"><i class="fab fa-linkedin fa-lg"></i></a>
                <a href="#" class="footer-social" title="Instagram"><i class="fab fa-instagram fa-lg"></i></a>
            </div>
        </div>
        <div class="container text-center mt-4 position-relative" style="z-index:2;">
            <hr class="footer-separator my-2" style="border-color:rgba(255,255,255,0.12);">
            <small class="text-white-50">&copy; {{ date('Y') }} {{ config('app.name', 'Gestion Événements') }}. Tous droits réservés.</small>
        </div>
        <style>
            .footer-main {
                margin-top: 4rem;
                background: linear-gradient(135deg, rgb(36, 45, 224) 0%, #000DFF 100%);
                border-top-left-radius: 2.5rem;
                border-top-right-radius: 2.5rem;
                box-shadow: 0 -4px 24px rgba(0,0,0,0.08);
                position: relative;
                overflow: hidden;
                transition: background 0.3s;
            }
            .footer-main:hover {
                background: linear-gradient(120deg, #000DFF 0%, rgb(36, 45, 224) 100%);
            }
            .footer-gradient {
                background: radial-gradient(circle at  0%, rgba(255,255,255,0.08) 0, rgba(0,0,0,0) 70%);
                z-index:1;
            }
            .footer-link {
                color: #fff;
                text-decoration: none;
                font-weight: 500;
                transition: color 0.2s, text-shadow 0.2s;
                text-shadow: 0 1px 2px rgba(0,0,0,0.08);
                padding: 0.2rem 0.5rem;
                border-radius: 6px;
            }
            .footer-link:hover {
                color: #ffe082;
                background: rgba(255,255,255,0.08);
                text-shadow: 0 2px 8px rgba(0,0,0,0.12);
            }
            .footer-social {
                color: #fff;
                transition: color 0.2s, box-shadow 0.2s, transform 0.2s;
                display: inline-block;
                border-radius: 50%;
                padding: 0.4rem;
                background: rgba(255,255,255,0.06);
                margin-bottom: 2px;
            }
            .footer-social:hover {
                color: #ffe082;
                background: rgba(255,255,255,0.18);
                box-shadow: 0 2px 12px 0 rgba(255,224,130,0.18);
                transform: scale(1.18) rotate(-6deg);
            }
            .footer-main .container {
                max-width: 1200px;
            }
            .footer-separator {
                border: none;
                border-top: 1.5px solid rgba(255,255,255,0.12);
                margin: 0 auto 0.5rem auto;
                width: 80%;
            }
            @media (max-width: 767px) {
                .footer-main {
                    border-top-left-radius: 1.2rem;
                    border-top-right-radius: 1.2rem;
                    padding-left: 0.5rem;
                    padding-right: 0.5rem;
                }
                .footer-main .container {
                    flex-direction: column !important;
                    gap: 1.5rem !important;
                    text-align: center;
                }
                .footer-separator {
                    width: 95%;
                }
            }
        </style>
    </footer>
    @endunless
</body>
</html>
