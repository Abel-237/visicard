@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Mes conversations
                    </h4>
                </div>
                <div class="card-body p-0">
                    @if($conversations->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune conversation</h5>
                            <p class="text-muted">Commencez à discuter avec d'autres professionnels !</p>
                            <a href="{{ route('business-cards.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>
                                Voir les cartes de visite
                            </a>
                        </div>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($conversations as $userId => $messages)
                                @php
                                    $otherUser = $messages->first()->sender_id === auth()->id() 
                                        ? $messages->first()->receiver 
                                        : $messages->first()->sender;
                                    $lastMessage = $messages->first();
                                    $unreadCount = $messages->where('receiver_id', auth()->id())
                                        ->whereNull('read_at')
                                        ->count();
                                @endphp
                                
                                <a href="{{ route('messages.show', $otherUser) }}" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($otherUser->businessCard && $otherUser->businessCard->logo)
                                                <img src="{{ asset('storage/' . $otherUser->businessCard->logo) }}" 
                                                     alt="{{ $otherUser->name }}" 
                                                     class="rounded-circle" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" 
                                                     style="width: 50px; height: 50px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $otherUser->name }}</h6>
                                            @if($otherUser->businessCard)
                                                <small class="text-muted">{{ $otherUser->businessCard->position }} chez {{ $otherUser->businessCard->company }}</small>
                                            @endif
                                            <p class="mb-0 text-muted small">
                                                {{ Str::limit($lastMessage->content, 50) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">
                                            {{ $lastMessage->created_at->diffForHumans() }}
                                        </small>
                                        @if($unreadCount > 0)
                                            <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.list-group-item:hover {
    background-color: #f8f9fa;
}
.list-group-item-action:focus {
    background-color: #e9ecef;
}
</style>
@endsection 