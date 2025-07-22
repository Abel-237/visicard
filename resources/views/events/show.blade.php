@extends('layouts.app')

@section('content')
<div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('categories.show', $event->category->slug) }}">{{ $event->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $event->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card mb-4 shadow-sm">
                <!-- Event Header -->
                <div class="card-header bg-white">
                    <h1 class="card-title h2 mb-0">{{ $event->title }}</h1>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div>
                            <span class="badge bg-{{ $event->category->color ?? 'primary' }}">{{ $event->category->name }}</span>
                            @if($event->status === 'published')
                                <span class="badge bg-success">Publié</span>
                                @if($event->published_at && $event->published_at->isFuture())
                                    <span class="badge bg-warning text-dark">À venir</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Brouillon</span>
                            @endif
                            @if($event->featured)
                                <span class="badge bg-warning text-dark">À la une</span>
                            @endif
                        </div>
                        <div>
                            <small class="text-muted">
                                Publié le {{ $event->published_at ? $event->published_at->format('d/m/Y') : 'N/A' }} par {{ $event->user->name }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Event Images Gallery -->
                @if($event->media->where('file_type', 'image')->count() > 0)
                    <div class="text-center p-4">
                        @if($event->media->where('file_type', 'image')->count() == 1)
                            <!-- Single Image -->
                    <img src="{{ asset('storage/' . $event->media->where('file_type', 'image')->first()->file_path) }}" 
                                 alt="{{ $event->title }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 300px; max-width: 100%; object-fit: contain;">
                        @else
                            <!-- Multiple Images - Gallery -->
                            <div class="row g-3 justify-content-center">
                                @foreach($event->media->where('file_type', 'image') as $media)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="text-center">
                                            <img src="{{ asset('storage/' . $media->file_path) }}" 
                                                 alt="{{ $event->title }} - Image {{ $loop->iteration }}" 
                                                 class="img-fluid rounded shadow-sm event-image" 
                                                 style="max-height: 200px; max-width: 100%; object-fit: contain; cursor: pointer;"
                                                 onclick="openImageModal('{{ asset('storage/' . $media->file_path) }}', '{{ $event->title }}')">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Event Body -->
                <div class="card-body">
                    <!-- Event Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            @if($event->event_date)
                                <div class="mb-2">
                                    <i class="far fa-calendar-alt text-primary"></i>
                                    <strong>Date:</strong> {{ $event->event_date ? $event->event_date->format('d/m/Y à H:i') : 'N/A' }}
                                </div>
                            @endif
                            @if($event->location)
                                <div>
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                    <strong>Lieu:</strong> {{ $event->location }}
                                </div>
                            @endif
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="mb-2">
                                <i class="far fa-eye text-primary"></i>
                                <strong>Vues:</strong> {{ $event->views }}
                            </div>
                            <div>
                                <i class="far fa-comment text-primary"></i>
                                <strong>Commentaires:</strong> {{ $event->comments->count() }}
                            </div>
                        </div>
                    </div>

                    <!-- Event Content -->
                    <div class="event-content mb-4">
                        {!! nl2br(e($event->content)) !!}
                    </div>

                    <!-- Event Tags -->
                    @if($event->tags->count() > 0)
                        <div class="mt-4">
                            <h5>Tags:</h5>
                            <div>
                                @foreach($event->tags as $tag)
                                    <a href="{{ route('events.index', ['tag' => $tag->id]) }}" class="badge bg-light text-dark text-decoration-none me-1">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Event Media -->
                    @if($event->media->where('file_type', '!=', 'image')->count() > 0)
                        <div class="mt-4">
                            <h5>Documents et médias:</h5>
                            <div class="row">
                                @foreach($event->media->where('file_type', '!=', 'image') as $media)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $media->name }}</h6>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        {{ $media->file_type }} - {{ $media->formatted_size }}
                                                    </small>
                                                </p>
                                                <a href="{{ asset('storage/' . $media->file_path) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   target="_blank">
                                                    <i class="fas fa-download"></i> Télécharger
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <div>
                            {{-- Bouton Like/Unlike --}}
                            @auth
                                <form id="like-form" action="{{ route('events.toggleLike', $event->slug) }}" method="POST">
                                    @csrf
                                    <button type="submit" id="like-btn" class="btn btn-outline-primary">
                                        <span id="like-text">{{ $event->likes->where('user_id', auth()->id())->count() ? "Je n'aime plus" : "J'aime" }}</span>
                                        <span id="likes-count">({{ $event->likes->count() }})</span>
                                    </button>
                                </form>
                                <script>
                                    document.getElementById('like-form').addEventListener('submit', function(e) {
                                        e.preventDefault();
                                        fetch(this.action, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': this.querySelector('[name=_token]').value,
                                                'Accept': 'application/json',
                                            },
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.liked !== undefined) {
                                                document.getElementById('like-text').textContent = data.liked ? "Je n'aime plus" : "J'aime";
                                                document.getElementById('likes-count').textContent = '(' + data.likes_count + ')';
                                            }
                                        });
                                    });
                                </script>
                            @else
                                <button class="btn btn-sm btn-outline-danger" disabled>
                                    <i class="far fa-heart"></i> 
                                    {{ $event->likes->count() }}
                                </button>
                            @endauth
                            
                            <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                                <i class="fas fa-print"></i> Imprimer
                            </button>
                            
                            <a href="{{ route('events.pdf', $event->slug) }}" class="btn btn-sm btn-outline-danger">
                                <i class="far fa-file-pdf"></i> Exporter en PDF
                            </a>
                            
                            <a href="#comments" class="btn btn-sm btn-outline-primary">
                                <i class="far fa-comment"></i> Commenter
                            </a>
                        </div>
                        
                        <div class="share-buttons">
                            <button class="btn btn-sm btn-outline-secondary" onclick="shareOnFacebook()">
                                <i class="fab fa-facebook-f"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="shareOnTwitter()">
                                <i class="fab fa-twitter"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="shareOnWhatsApp()">
                                <i class="fab fa-whatsapp"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="shareByEmail()">
                                <i class="fas fa-envelope"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Author Info -->
                <div class="card-footer bg-white">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <img src="{{ $event->user->getProfileImage() }}" 
                                 class="rounded-circle" 
                                 alt="{{ $event->user->name }}"
                                 style="width: 50px; height: 50px; object-fit: cover;"
                                 onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ $event->user->name }}</h5>
                            <p class="text-muted mb-0">
                                <small>{{ ucfirst($event->user->role) }}</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="card mb-4 shadow-sm" id="comments">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Commentaires ({{ $comments->count() }})</h4>
                </div>
                <div class="card-body">
                    @auth
                        <div class="mb-4">
                            <form action="{{ route('events.comment', $event->slug) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <textarea class="form-control" name="content" rows="3" placeholder="Ajouter un commentaire..." required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Commenter</button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info mb-4">
                            <a href="{{ route('login') }}">Connectez-vous</a> pour laisser un commentaire.
                        </div>
                    @endauth

                    @if($comments->count() > 0)
                        <div class="comments">
                            @foreach($comments as $comment)
                                <div class="comment mb-4 pb-3 border-bottom" id="comment-{{ $comment->id }}">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="{{ $comment->user->getProfileImage() }}" 
                                                 class="rounded-circle" 
                                                 alt="{{ $comment->user->name }}"
                                                 style="width: 40px; height: 40px; object-fit: cover;"
                                                 onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">{{ $comment->user->name }}</h5>
                                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>
                                            <p class="mb-2">{{ $comment->content }}</p>
                                            <div class="d-flex">
                                                @auth
                                                    <button class="btn btn-sm btn-link text-muted reply-button" data-comment-id="{{ $comment->id }}">
                                                        Répondre
                                                    </button>
                                                    
                                                    @if(Auth::id() == $comment->user_id || Auth::user()->role === 'admin')
                                                        <form action="{{ route('admin.comments.delete', $comment->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-link text-danger">Supprimer</button>
                                                        </form>
                                                    @endif
                                                @endauth
                                            </div>
                                            
                                            <!-- Reply Form (Hidden by default) -->
                                            <div class="reply-form mt-3" id="reply-form-{{ $comment->id }}" style="display: none;">
                                                <form action="{{ route('events.comment', $event->slug) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                    <div class="mb-2">
                                                        <textarea class="form-control" name="content" rows="2" placeholder="Répondre à ce commentaire..." required></textarea>
                                                    </div>
                                                    <div class="text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary cancel-reply" data-comment-id="{{ $comment->id }}">Annuler</button>
                                                        <button type="submit" class="btn btn-sm btn-primary">Répondre</button>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <!-- Replies -->
                                            @if($comment->replies->count() > 0)
                                                <div class="replies mt-3">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="reply mt-3 ps-3 border-start">
                                                            <div class="d-flex">
                                                                <div class="flex-shrink-0">
                                                                    <img src="{{ $reply->user->getProfileImage() }}" 
                                                                         class="rounded-circle" 
                                                                         alt="{{ $reply->user->name }}"
                                                                         style="width: 30px; height: 30px; object-fit: cover;"
                                                                         onerror="this.src='{{ asset('images/default-avatar.svg') }}'">
                                                                </div>
                                                                <div class="flex-grow-1 ms-2">
                                                                    <div class="d-flex justify-content-between align-items-center">
                                                                        <h6 class="mb-0">{{ $reply->user->name }}</h6>
                                                                        <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                                                                    </div>
                                                                    <p class="mb-1">{{ $reply->content }}</p>
                                                                    <div>
                                                                        @auth
                                                                            @if(Auth::id() == $reply->user_id || Auth::user()->role === 'admin')
                                                                                <form action="{{ route('admin.comments.delete', $reply->id) }}" method="POST" class="d-inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0">Supprimer</button>
                                                                                </form>
                                                                            @endif
                                                                        @endauth
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted">Aucun commentaire pour le moment. Soyez le premier à commenter!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Event actions for admin -->
            @auth
                @if(Auth::id() == $event->user_id || Auth::user()->role === 'admin' || Auth::user()->role === 'editor')
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white">
                            Actions
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('events.edit', $event->slug) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-edit"></i> Modifier l'événement
                                </a>
                                <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="fas fa-trash-alt"></i> Supprimer l'événement
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Event Date/Time -->
            @if($event->event_date)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        Quand?
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 me-3">
                                <div class="event-date-box text-center">
                                    <div class="month">{{ $event->event_date->format('M') }}</div>
                                    <div class="day">{{ $event->event_date->format('d') }}</div>
                                    <div class="year">{{ $event->event_date->format('Y') }}</div>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $event->event_date->format('l') }}</div>
                                <div>{{ $event->event_date->format('H:i') }}</div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="{{ route('calendar.download', $event->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="far fa-calendar-plus"></i> Ajouter à mon calendrier
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#reminderModal">
                                <i class="far fa-bell"></i> Me le rappeler
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Event Location -->
            @if($event->location)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-info text-white">
                        Où?
                    </div>
                    <div class="card-body">
                        <p>{{ $event->location }}</p>
                        <div class="ratio ratio-16x9">
                            <iframe 
                                src="https://www.google.com/maps/embed/v1/place?key=YOUR_API_KEY&q={{ urlencode($event->location) }}" 
                                allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Related Events -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    Événements similaires
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($relatedEvents as $relatedEvent)
                            <a href="{{ route('events.show', $relatedEvent->slug) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">{{ $relatedEvent->title }}</h6>
                                    <small>{{ $relatedEvent->published_at->format('d/m/Y') }}</small>
                                </div>
                                <small class="text-muted">{{ Str::limit($relatedEvent->excerpt ?? $relatedEvent->content, 60) }}</small>
                            </a>
                        @empty
                            <div class="list-group-item">
                                <p class="text-muted mb-0">Aucun événement similaire trouvé.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cet événement? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('events.destroy', $event->slug) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Image de l'événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <a id="downloadImage" href="" download class="btn btn-primary">
                    <i class="fas fa-download"></i> Télécharger
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Reminder Modal -->
<div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reminderModalLabel">Définir un rappel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('calendar.reminder', $event->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Quand souhaitez-vous être rappelé pour cet événement ?</p>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="reminder_time" id="reminder_hour" value="hour">
                        <label class="form-check-label" for="reminder_hour">
                            1 heure avant l'événement
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="reminder_time" id="reminder_day" value="day" checked>
                        <label class="form-check-label" for="reminder_day">
                            1 jour avant l'événement
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="reminder_time" id="reminder_week" value="week">
                        <label class="form-check-label" for="reminder_week">
                            1 semaine avant l'événement
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Définir le rappel</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .event-date-box {
        background-color: #f8f9fa;
        border-radius: 4px;
        width: 60px;
        border: 1px solid #dee2e6;
        overflow: hidden;
    }
    
    .event-date-box .month {
        background-color: #007bff;
        color: white;
        padding: 2px 0;
        font-size: 12px;
        text-transform: uppercase;
    }
    
    .event-date-box .day {
        font-size: 24px;
        font-weight: bold;
        padding: 2px 0;
    }
    
    .event-date-box .year {
        font-size: 12px;
        color: #6c757d;
        padding: 2px 0;
        border-top: 1px solid #dee2e6;
    }
    
    .event-content {
        line-height: 1.8;
    }
    
    /* Image gallery styles */
    .event-image {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .event-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    /* Modal image styles */
    #imageModal .modal-body img {
        max-height: 70vh;
        object-fit: contain;
    }
    
    /* User avatar styles */
    .user-avatar {
        border: 2px solid #e9ecef;
        transition: border-color 0.3s ease;
    }
    
    .user-avatar:hover {
        border-color: #007bff;
    }
    
    .comment-avatar {
        border: 1px solid #dee2e6;
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reply functionality
        document.querySelectorAll('.reply-button').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-comment-id');
                document.getElementById(`reply-form-${commentId}`).style.display = 'block';
            });
        });
        
        document.querySelectorAll('.cancel-reply').forEach(button => {
            button.addEventListener('click', function() {
                const commentId = this.getAttribute('data-comment-id');
                document.getElementById(`reply-form-${commentId}`).style.display = 'none';
            });
        });
    });
    
    // Social sharing functions
    function shareOnFacebook() {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
    }
    
    function shareOnTwitter() {
        window.open(`https://twitter.com/intent/tweet?url=${encodeURIComponent(window.location.href)}&text=${encodeURIComponent('{{ $event->title }}')}`, '_blank');
    }
    
    function shareOnWhatsApp() {
        window.open(`https://wa.me/?text=${encodeURIComponent('{{ $event->title }} - ' + window.location.href)}`, '_blank');
    }
    
    function shareByEmail() {
        window.location.href = `mailto:?subject=${encodeURIComponent('{{ $event->title }}')}&body=${encodeURIComponent('Découvrez cet événement : ' + window.location.href)}`;
    }
    
    // Image modal functionality
    function openImageModal(imageSrc, imageAlt) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalImage').alt = imageAlt;
        document.getElementById('imageModalLabel').textContent = imageAlt;
        document.getElementById('downloadImage').href = imageSrc;
        
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        imageModal.show();
    }
</script>
@endsection 