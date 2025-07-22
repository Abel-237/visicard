@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="mb-2">{{ $category->name }}</h1>
            @if($category->description)
                <p class="text-muted">{{ $category->description }}</p>
            @endif
        </div>
    </div>
    <div class="row">
        @forelse($events as $event)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                        </h5>
                        <p class="card-text small text-muted">{{ Str::limit($event->excerpt ?? $event->content, 80) }}</p>
                        @if($event->event_date)
                            <div class="mb-2">
                                <i class="far fa-calendar-alt me-1"></i>{{ $event->event_date->format('d/m/Y H:i') }}
                            </div>
                        @endif
                        <a href="{{ route('events.show', $event->slug) }}" class="btn btn-sm btn-primary">Voir l'événement</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">Aucun événement publié dans cette catégorie.</div>
            </div>
        @endforelse
    </div>
    <div class="d-flex justify-content-center mt-4">
        {{ $events->links() }}
    </div>
</div>
@endsection 