@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <!-- En-tête avec photo et informations principales -->
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            {!! \App\Helpers\ImageHelper::displayProfileImage(
                                $businessCard->logo, 
                                $businessCard->name, 
                                'img-fluid mb-3', 
                                ['style' => 'width: 150px; height: 150px; object-fit: cover; border: 3px solid #007bff;']
                            ) !!}
                        </div>
                        <div class="col-md-9">
                            <h2 class="mb-2">{{ $businessCard->name }}</h2>
                            <h4 class="text-muted mb-3">{{ $businessCard->position }}</h4>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-building text-primary me-2"></i>
                                <h5 class="mb-0">{{ $businessCard->company }}</h5>
                            </div>
                            <span class="badge bg-primary">{{ $businessCard->industry }}</span>
                        </div>
                    </div>

                    <!-- Informations de contact -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Coordonnées</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-envelope text-primary me-2"></i>
                                    <a href="mailto:{{ $businessCard->email }}" class="text-decoration-none">
                                        {{ $businessCard->email }}
                                    </a>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-phone text-primary me-2"></i>
                                    <a href="tel:{{ $businessCard->phone }}" class="text-decoration-none">
                                        {{ $businessCard->phone }}
                                    </a>
                                </li>
                                @if($businessCard->website)
                                    <li class="mb-2">
                                        <i class="fas fa-globe text-primary me-2"></i>
                                        <a href="{{ $businessCard->website }}" target="_blank" class="text-decoration-none">
                                            {{ $businessCard->website }}
                                        </a>
                                    </li>
                                @endif
                                @if($businessCard->address)
                                    <li class="mb-2">
                                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                        {{ $businessCard->address }}
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="border-bottom pb-2 mb-3">Réseaux sociaux</h5>
                            @php
                                $socialMedia = is_string($businessCard->social_media) ? json_decode($businessCard->social_media, true) : ($businessCard->social_media ?? []);
                            @endphp
                            @if(!empty($socialMedia))
                                <ul class="list-unstyled">
                                    @foreach($socialMedia as $platform => $url)
                                        @if($url)
                                            <li class="mb-2">
                                                <i class="fab fa-{{ $platform }} text-primary me-2"></i>
                                                <a href="{{ $url }}" target="_blank" class="text-decoration-none">
                                                    {{ ucfirst($platform) }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Aucun réseau social renseigné</p>
                            @endif
                        </div>
                    </div>

                    <!-- Biographie -->
                    @if($businessCard->bio)
                        <div class="mb-4">
                            <h5 class="border-bottom pb-2 mb-3">À propos</h5>
                            <p class="text-justify">{{ $businessCard->bio }}</p>
                        </div>
                    @endif

                    <!-- Chat -->
                    @auth
                        <div class="mt-4">
                            <h5 class="border-bottom pb-2 mb-3">Contacter {{ $businessCard->name }}</h5>
                            <div class="chat-container" style="height: 400px; overflow-y: auto;">
                                <div id="messages" class="messages p-3">
                                    <!-- Les messages seront chargés ici via JavaScript -->
                                </div>
                            </div>
                            <form id="message-form" class="mt-3">
                                <div class="input-group">
                                    <input type="text" id="message-input" class="form-control" 
                                           placeholder="Écrivez votre message..." required>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane"></i> Envoyer
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Connectez-vous pour discuter avec {{ $businessCard->name }}
                        </div>
                    @endauth

                    <!-- QR Code Section -->
                    <div class="mt-4">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-qrcode text-primary me-2"></i>Code QR
                        </h5>
                        <div class="text-center">
                            <div class="qr-code-container p-3 border rounded bg-light d-inline-block">
                                @if(isset($qrCode))
                                    {!! $qrCode !!}
                                @else
                                    <p class="text-danger">Le QR code n'a pas pu être généré.</p>
                                @endif
                            </div>
                            <p class="text-muted mt-2 mb-0">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    Scannez ce code QR pour accéder à cette carte de visite
                                </small>
                            </p>
                            <div class="mt-2">
                                {{-- La fonctionnalité de téléchargement peut être ajoutée plus tard si besoin --}}
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="{{ route('business-cards.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à l'annuaire
                        </a>
                        <div>
                            @auth
                                @if(auth()->id() !== $businessCard->user_id && $businessCard->user_id)
                                    <a href="{{ route('messages.show', $businessCard->user_id) }}" class="btn btn-success me-2">
                                        <i class="fas fa-comments me-2"></i>Discuter
                                    </a>
                                @endif
                            @endauth
                            @if(auth()->check() && auth()->id() === $businessCard->user_id)
                                <a href="{{ route('business-cards.edit', $businessCard) }}" class="btn btn-primary me-2">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <form action="{{ route('business-cards.destroy', $businessCard) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" 
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette carte de visite ?')">
                                        <i class="fas fa-trash me-2"></i>Supprimer
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@auth
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messages');
    const messageForm = document.getElementById('message-form');
    const messageInput = document.getElementById('message-input');
    const userId = {{ auth()->id() }};
    const receiverId = {{ $businessCard->user_id }};

    // Charger les messages existants
    loadMessages();

    // Envoyer un nouveau message
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const content = messageInput.value.trim();
        
        if (content) {
            sendMessage(content);
            messageInput.value = '';
        }
    });

    // Fonction pour charger les messages
    function loadMessages() {
        fetch(`/messages/${receiverId}`)
            .then(response => response.json())
            .then(data => {
                messagesContainer.innerHTML = '';
                data.forEach(message => {
                    appendMessage(message);
                });
                scrollToBottom();
            });
    }

    // Fonction pour envoyer un message
    function sendMessage(content) {
        fetch(`/messages/${receiverId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ content })
        })
        .then(response => response.json())
        .then(data => {
            appendMessage(data.message);
            scrollToBottom();
        });
    }

    // Fonction pour ajouter un message à la conversation
    function appendMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${message.sender_id === userId ? 'sent' : 'received'} mb-3`;
        
        const content = `
            <div class="message-content p-3 rounded">
                <div class="message-text">${message.content}</div>
                <small class="text-muted">${message.created_at->translatedFormat('d F Y à H:i')}</small>
            </div>
        `;
        
        messageDiv.innerHTML = content;
        messagesContainer.appendChild(messageDiv);
    }

    // Fonction pour faire défiler vers le bas
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Rafraîchir les messages toutes les 5 secondes
    setInterval(loadMessages, 5000);
    
    // La fonction generateQRCode() est maintenant supprimée car le QR code est rendu côté serveur.
});
</script>

@push('styles')
<style>
.chat-container {
    border: 1px solid #dee2e6;
    border-radius: 0.25rem;
    background-color: #f8f9fa;
}

.messages {
    display: flex;
    flex-direction: column;
}

.message {
    max-width: 80%;
}

.message.sent {
    align-self: flex-end;
}

.message.received {
    align-self: flex-start;
}

.message-content {
    background-color: #fff;
    border: 1px solid #dee2e6;
}

.message.sent .message-content {
    background-color: #007bff;
    color: #fff;
}

.message.received .message-content {
    background-color: #fff;
}

.message-text {
    margin-bottom: 0.25rem;
}

.text-justify {
    text-align: justify;
}

.card {
    border: none;
    border-radius: 15px;
}

.card-body {
    border-radius: 15px;
}

.badge {
    font-size: 0.9em;
    padding: 0.5em 1em;
}

a {
    color: inherit;
}

a:hover {
    color: var(--bs-primary);
}
</style>
@endpush
@endpush
@endauth
@endsection 