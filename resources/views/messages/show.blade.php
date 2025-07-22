@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <!-- Header -->
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('messages.index') }}" class="btn btn-outline-light btn-sm me-3">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="d-flex align-items-center">
                            @if($user->businessCard && $user->businessCard->logo)
                                <img src="{{ asset('storage/' . $user->businessCard->logo) }}" 
                                     alt="{{ $user->name }}" 
                                     class="rounded-circle me-3" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                            @endif
                            <div>
                                <h5 class="mb-0">{{ $user->name }}
                                    @if($user->last_seen && now()->diffInMinutes($user->last_seen) < 2)
                                        <span class="online-dot" title="En ligne"></span>
                                    @else
                                        <span class="text-muted" style="font-size:0.95em;">Déconnecté</span>
                                    @endif
                                </h5>
                                @if($user->businessCard)
                                    <small class="text-light">{{ $user->businessCard->position }} chez {{ $user->businessCard->company }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div>
                        @if($user->businessCard)
                        <a href="{{ route('business-cards.show', $user->businessCard) }}" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-id-card me-1"></i>
                            Voir sa carte
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Messages Container -->
                <div class="card-body p-0" style="height: 500px; overflow-y: auto;" id="messagesContainer">
                    <div class="p-3">
                        @if($messages->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun message pour l'instant</h5>
                                <p class="text-muted">Écrivez votre premier message ci-dessous pour démarrer la conversation.</p>
                            </div>
                        @else
                            @foreach($messages as $message)
                                <div class="{{ $message->sender_id === auth()->id() ? 'text-end' : 'text-start' }}">
                                    <div class="p-2 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}">
                                        {{ $message->content }}
                                        <small class="d-block text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Message Input -->
                <div class="card-footer bg-white border-top" style="border-radius: 0;">
                    <form method="POST" action="{{ route('messages.store', $user) }}">
                        @csrf
                        <input type="text" name="content" class="form-control" required maxlength="1000">
                        <button class="btn btn-primary mt-2">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const messagesContainer = document.getElementById('messagesContainer');
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const receiverId = {{ $user->id }};
    
    // Scroll to bottom of messages
    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Initial scroll
    scrollToBottom();
    
    // Handle message submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = messageInput.value.trim();
        if (!content) return;
        
        // Disable form while sending
        const submitBtn = messageForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        submitBtn.disabled = true;
        
        // Send message via AJAX
        fetch(`/messages/${receiverId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ content: content })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Add message to chat
                addMessageToChat(data.message);
                messageInput.value = '';
                scrollToBottom();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Erreur lors de l\'envoi du message');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });
    
    // Add message to chat
    function addMessageToChat(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-3 text-end';
        
        const now = new Date();
        const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                          now.getMinutes().toString().padStart(2, '0');
        
        messageDiv.innerHTML = `
            <div class="d-inline-block bg-primary text-white rounded p-3" style="max-width: 70%;">
                <div class="message-content">
                    ${message.content}
                </div>
                <small class="d-block mt-1 text-light">
                    ${timeString}
                    <i class="fas fa-check text-light ms-1"></i>
                </small>
            </div>
        `;
        
        messagesContainer.querySelector('.p-3').appendChild(messageDiv);
    }
    
    // Real-time updates (polling every 3 seconds)
    setInterval(function() {
        fetch(`/messages/${receiverId}/updates`)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        if (message.sender_id !== {{ auth()->id() }}) {
                            addReceivedMessageToChat(message);
                        }
                    });
                    scrollToBottom();
                }
            })
            .catch(error => console.error('Error fetching updates:', error));
    }, 3000);
    
    // Add received message to chat
    function addReceivedMessageToChat(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-3 text-start';
        
        const messageTime = new Date(message.created_at);
        const timeString = messageTime.getHours().toString().padStart(2, '0') + ':' + 
                          messageTime.getMinutes().toString().padStart(2, '0');
        
        messageDiv.innerHTML = `
            <div class="d-inline-block bg-light rounded p-3" style="max-width: 70%;">
                <div class="message-content">
                    ${message.content}
                </div>
                <small class="d-block mt-1 text-muted">
                    ${timeString}
                </small>
            </div>
        `;
        
        messagesContainer.querySelector('.p-3').appendChild(messageDiv);
    }
});

let lastActivity = Date.now();
let pingInterval = null;

function pingServer() {
    fetch('/user/ping', { method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
}

function startPing() {
    if (!pingInterval) {
        pingInterval = setInterval(() => {
            if (Date.now() - lastActivity < 2 * 60 * 1000) { // 2 minutes d'inactivité max
                pingServer();
            }
        }, 30000); // ping toutes les 30s
    }
}

['mousemove', 'keydown', 'click'].forEach(evt => {
    window.addEventListener(evt, () => {
        lastActivity = Date.now();
        startPing();
    });
});

startPing();

window.Echo.channel('presence')
    .listen('UserPresenceUpdated', (data) => {
        updateUserPresenceUI(data.id, data.last_seen);
    });

function updateUserPresenceUI(userId, lastSeen) {
    const el = document.querySelector(`[data-user-id='${userId}'] .user-status`);
    if (!el) return;
    if (lastSeen && (new Date() - new Date(lastSeen)) < 2 * 60 * 1000) {
        el.innerHTML = '<span class="online-dot"></span> En ligne';
    } else {
        el.innerHTML = `<span class="offline-dot"></span> Vu à ${formatTime(lastSeen)}`;
    }
}

function formatTime(datetime) {
    if (!datetime) return '';
    const d = new Date(datetime);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}
</script>
@endpush

<style>
#messagesContainer {
    background:rgb(255, 255, 255);
    padding: 0 0.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    min-height: 350px;
    max-height: 70vh;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
}

.message-row {
    display: flex;
    margin-bottom: 22px; /* Plus d'espace entre les bulles */
    align-items: flex-end;
}
.message-row.sent {
    justify-content: flex-end;
}
.message-row.received {
    justify-content: flex-start;
}

.message-bubble {
    position: relative;
    padding: 12px 18px 22px 18px;
    border-radius: 22px 22px 6px 22px;
    font-size: 1.05rem;
    max-width: 80vw;
    min-width: 60px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.10);
    word-break: break-word;
    background: #fff;
    color: #222;
    margin: 0 8px;
    transition: background 0.2s;
    border: 1px solid #e0e0e0;
}
.message-row.sent .message-bubble {
    background: #007bff;
    color: #fff;
    border-bottom-right-radius: 6px;
    border-bottom-left-radius: 22px;
    border-top-right-radius: 22px;
    border-top-left-radius: 22px;
    border: 1px solid #007bff;
}
.message-row.received .message-bubble {
    background: #fff;
    color: #222;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 22px;
    border-top-right-radius: 22px;
    border-top-left-radius: 22px;
}

.message-timestamp {
    position: absolute;
    right: 16px;
    bottom: 6px;
    font-size: 0.78em;
    color: #bdbdbd;
    font-weight: 400;
    letter-spacing: 0.02em;
}

@media (max-width: 768px) {
    #messagesContainer {
        max-height: 50vh;
    }
    .message-bubble {
        font-size: 0.97rem;
        max-width: 95vw;
    }
}

.message-content {
    word-wrap: break-word;
}

#messagesContainer::-webkit-scrollbar {
    width: 6px;
}

#messagesContainer::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#messagesContainer::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

#messagesContainer::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

.message-unread {
    background-color: #ffeeba !important;
    border: 1.5px solid #ffc107;
}

.online-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    background: #4caf50;
    border-radius: 50%;
    margin-left: 8px;
    box-shadow: 0 0 4px #4caf50;
    vertical-align: middle;
}

.offline-dot {
    display: inline-block;
    width: 10px; height: 10px;
    background: #bdbdbd;
    border-radius: 50%;
    margin-right: 4px;
}
</style>
@endsection 